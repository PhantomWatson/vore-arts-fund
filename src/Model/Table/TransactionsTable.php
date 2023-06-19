<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Transaction;
use Cake\Log\Log;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Transactions Model
 *
 * @property \App\Model\Table\ApplicationsTable&\Cake\ORM\Association\BelongsTo $Applications
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

        $this->belongsTo('Applications', [
            'foreignKey' => 'application_id',
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
        $validator
            ->integer('type')
            ->requirePresence('type', 'create')
            ->notEmptyString('type');

        $validator
            ->integer('amount')
            ->allowEmptyString('amount');

        $validator
            ->integer('application_id')
            ->allowEmptyString('application_id');

        $validator
            ->scalar('meta')
            ->requirePresence('meta', 'create')
            ->notEmptyString('meta');

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
        $rules->add($rules->existsIn('application_id', 'Applications'), ['errorField' => 'application_id']);

        return $rules;
    }

    /**
     * Saves a payment
     *
     * Assumes that this is a donation and not a load repayment.
     * TODO: Check metadata for application_id and update type and application_id as appropriate
     *
     * @param \Stripe\Charge $charge
     * @return bool
     */
    public function addPayment(\Stripe\Charge $charge): bool
    {
        $transaction = $this->newEntity([
            'amount' => $charge->amount_captured,
            'type' => Transaction::TYPE_DONATION,
            'application_id' => null,
            'meta' => json_encode($charge),
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