<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * FundingCycles Model
 *
 * @property \App\Model\Table\ApplicationsTable&\Cake\ORM\Association\HasMany $Applications
 * @property \App\Model\Table\VotesTable&\Cake\ORM\Association\HasMany $Votes
 * @method \App\Model\Entity\FundingCycle get($primaryKey, $options = [])
 * @method \App\Model\Entity\FundingCycle newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\FundingCycle[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\FundingCycle|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\FundingCycle saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\FundingCycle patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\FundingCycle[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\FundingCycle findOrCreate($search, callable $callback = null, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @method \App\Model\Entity\FundingCycle[]|\Cake\Datasource\ResultSetInterface|false saveMany($entities, $options = [])
 */
class FundingCyclesTable extends Table
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

        $this->setTable('funding_cycles');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Applications', [
            'foreignKey' => 'funding_cycle_id',
        ]);
        $this->hasMany('Votes', [
            'foreignKey' => 'funding_cycle_id',
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->dateTime('application_begin')
            ->allowEmptyDateTime('application_begin');

        $validator
            ->dateTime('application_end')
            ->allowEmptyDateTime('application_end');

        $validator
            ->dateTime('vote_begin')
            ->allowEmptyDateTime('vote_begin');

        $validator
            ->dateTime('vote_end')
            ->allowEmptyDateTime('vote_end');

        $validator
            ->integer('funding_available')
            ->requirePresence('funding_available', 'create')
            ->notEmptyString('funding_available');

        return $validator;
    }

    /**
     * Modifies a query to return the current funding cycle
     *
     * @param \Cake\ORM\Query $query
     * @return \Cake\ORM\Query
     */
    public function findCurrent(Query $query)
    {
        $now = date('Y-m-d H:i:s');

        return $query
            ->find('currentAndFuture')
            ->where(['FundingCycles.application_end >=' => $now]);
    }

    /**
     * Modifies a query to return the current and future funding cycles
     *
     * @param \Cake\ORM\Query $query
     * @return \Cake\ORM\Query
     */
    public function findCurrentAndFuture(Query $query)
    {
        $now = date('Y-m-d H:i:s');

        return $query
            ->where(['FundingCycles.application_begin <=' => $now]);
    }
}
