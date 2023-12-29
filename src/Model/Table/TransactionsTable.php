<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Transaction;
use Cake\I18n\FrozenDate;
use Cake\Log\Log;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

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
     * @return bool
     */
    public function addPayment(\Stripe\Charge $charge): bool
    {
        $transaction = $this->newEntity([
            'date' => new FrozenDate(),
            'amount_gross' => $charge->amount_captured,
            'type' => Transaction::TYPE_DONATION,
            'project_id' => null,
            'meta' => json_encode($charge),
            'name' => $charge->metadata['name'] ?? '',
        ]);
        if ($this->save($transaction)) {
            return true;
        }
        Log::write(
            'Error',
            'Can\'t save charge. Details: ' . print_r($transaction->getErrors(), true),
            ['scope' => 'stripe']
        );
        return false;
    }
}
