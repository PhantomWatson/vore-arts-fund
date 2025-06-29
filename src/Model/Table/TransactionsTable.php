<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Alert\Alert;
use App\Alert\ErrorAlert;
use App\Model\Entity\Project;
use App\Model\Entity\Transaction;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\I18n\FrozenTime;
use Cake\Log\Log;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Validation\Validator;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

/**
 * Transactions Model
 *
 * @property \App\Model\Table\ProjectsTable&\Cake\ORM\Association\BelongsTo $Projects
 *
 * @method \App\Model\Entity\Transaction newEmptyEntity()
 * @method \App\Model\Entity\Transaction newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Transaction[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Transaction get($primaryKey, $options = [])
 * @method \App\Model\Entity\Transaction findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Transaction patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Transaction[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Transaction|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Transaction saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Transaction[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Transaction[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Transaction[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Transaction[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TransactionsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('transactions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Projects', [
            'foreignKey' => 'project_id',
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $types = array_keys(Transaction::getTypes());
        $validator
            ->integer('type')
            ->requirePresence('type', 'create')
            ->inList('type', $types)
            ->range('type', [min($types), max($types)])
            ->notEmptyString('type');

        $validator
            ->decimal('amount_gross')
            ->notEmptyString('amount_gross');

        $validator
            ->decimal('amount_net')
            ->notEmptyString('amount_net');

        $validator
            ->integer('project_id')
            ->allowEmptyString('project_id');

        $validator
            ->scalar('meta')
            ->allowEmptyString('meta');

        $validator
            ->dateTime('date')
            ->requirePresence('date', 'create');

        $validator
            ->scalar('name')
            ->allowEmptyString('name')
            ->maxLength('name', 100);

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('project_id', 'Projects'), ['errorField' => 'project_id']);

        return $rules;
    }

    /**
     * Saves a payment
     *
     * Assumes that this is a donation and not a loan repayment.
     * TODO: Check metadata for project_id and update type and project_id as appropriate
     *
     * @param \Stripe\Charge $charge
     * @return Transaction|false
     */
    public function addPayment(\Stripe\Charge $charge): Transaction|false
    {
        $transactionType = isset($charge->metadata['transactionType'])
            ? (int)$charge->metadata['transactionType']
            : null;
        $isValidType = !$transactionType
            || !in_array($transactionType, [Transaction::TYPE_DONATION, Transaction::TYPE_LOAN_REPAYMENT]);
        if (!$isValidType) {
            self::logStripeError(sprintf(
                'Invalid transaction type for charge %s. Metadata: %s. Type will be recorded as a donation.',
                $charge->id,
                json_encode($charge->metadata)
            ));
            $transactionType = Transaction::TYPE_DONATION;
        }

        $data = [
            'amount_gross' => $charge->amount_captured,
            'amount_net' => self::getNetAmount($charge->balance_transaction),
            'date' => new FrozenTime(),
            'type' => $transactionType,
            'project_id' => isset($charge->metadata['projectId']) ? (int)$charge->metadata['projectId'] : null,
            'meta' => json_encode($charge),
            'name' => $charge->metadata['name'] ?? null,
            'user_id' => isset($charge->metadata['userId']) ? (int)$charge->metadata['userId'] : null,
        ];
        $transaction = $this->newEntity($data);
        if ($this->save($transaction)) {
            return $transaction;
        }
        self::logStripeError(
            "Can't save charge.\nData: " . print_r($data, true)
            . "\nError details: " . print_r($transaction->getErrors(), true),
        );
        return false;
    }

    /**
     * Returns the net amount of the specified balance transaction, or NULL if there's an error
     *
     * @param string $balanceTransactionId
     * @return int|null
     */
    public static function getNetAmount(string $balanceTransactionId): ?int
    {
        $stripe = new StripeClient(Configure::read('Stripe.secret_key'));
        try {
            $balanceTransaction = $stripe->balanceTransactions->retrieve($balanceTransactionId, []);
            return $balanceTransaction->net;
        } catch (ApiErrorException $e) {
            self::logStripeError(sprintf(
                'Failed to fetch net amount for balance transaction %s. Details: %s',
                $balanceTransactionId,
                $e->getMessage()
            ));
            return null;
        }
    }

    public static function logStripeError($msg): void
    {
        Log::write(
            LOG_ERR,
            $msg,
            ['scope' => 'stripe']
        );

        ErrorAlert::send($msg);
    }

    protected function findForProject($query, $options)
    {
        return $this
            ->find()
            ->where(['project_id' => $options['project_id']])
            ->orderAsc('created');
    }

    private function sendNewTransactionAlert(Transaction $transaction): void
    {
        $alert = new Alert();
        $alert->addLine(sprintf(
            '<%s|Transaction #%s> %s',
            Router::url(
                [
                    'prefix' => 'Admin',
                    'controller' => 'Transactions',
                    'action' => 'view',
                    'id' => $transaction->id
                ],
                true
            ),
            $transaction->id,
            $transaction->isNew() ? 'saved' : 'updated'
        ));

        $details = ['Transaction type: ' . $transaction->type_name];
        if ($transaction->amount_net == $transaction->amount_gross) {
            $details[] = 'Amount: ' . $transaction->dollar_amount_gross_formatted;
        } else {
            $details[] = 'Amount (gross): ' . $transaction->dollar_amount_gross_formatted;
            $details[] = 'Amount (net): ' . $transaction->dollar_amount_net_formatted;
        }
        if ($transaction->project_id) {
            $projectsTable = TableRegistry::getTableLocator()->get('Projects');
            try {
                $project = $projectsTable->get($transaction->project_id);
                $projectDetail = sprintf(
                    'Project: <%s|%s>',
                    Router::url(
                        [
                            'prefix' => 'Admin',
                            'controller' => 'Projects',
                            'action' => 'review',
                            'id' => $transaction->project_id,
                        ],
                        true
                    ),
                    $project->title,
                );
            } catch (RecordNotFoundException $e) {
                $projectDetail = "Project: (invalid project #{$transaction->project_id}) selected)";
            }
            $details[] = $projectDetail;
        }
        $alert->addList($details);
        $alert->send(Alert::TYPE_TRANSACTIONS);
    }

    /**
     * @param Event $event
     * @param EntityInterface|Transaction $entity
     * @param $options
     * @return void
     */
    public function afterSave(Event $event, EntityInterface $entity, $options): void
    {
        $this->sendNewTransactionAlert($entity);
        if ($entity->type == Transaction::TYPE_LOAN) {
            $projectsTable = TableRegistry::getTableLocator()->get('Projects');
            $projectsTable->setProjectAwardedDate($entity->project_id, $entity->date);
            $projectsTable->updateStatus($entity->project_id, Project::STATUS_AWARDED_AND_DISBURSED);
        }
    }

    /**
     * @param int $projectId
     * @return \Cake\Datasource\ResultSetInterface|Transaction[]
     */
    public function getRepaymentsForProject(int $projectId)
    {
        return $this->find()
            ->where([
                'project_id' => $projectId,
                'type' => Transaction::TYPE_LOAN_REPAYMENT,
            ])
            ->order(['date' => 'DESC'])
            ->all();
    }
}
