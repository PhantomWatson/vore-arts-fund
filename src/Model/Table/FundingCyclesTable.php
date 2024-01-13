<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\FundingCycle;
use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * FundingCycles Model
 *
 * @property \App\Model\Table\ProjectsTable&\Cake\ORM\Association\HasMany $Projects
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
    const GROUP_VOTING = 'Currently voting';
    const GROUP_PAST = 'Past';
    const GROUP_APPLYING = 'Taking applications';
    const GROUP_UPCOMING = 'Upcoming';

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

        $this->hasMany('Projects', [
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
            ->requirePresence('application_begin', 'create')
            ->allowEmptyDateTime('application_begin', 'Required', false);

        $validator
            ->dateTime('application_end')
            ->requirePresence('application_end', 'create')
            ->allowEmptyDateTime('application_end', 'Required', false);

        $validator
            ->dateTime('vote_begin')
            ->requirePresence('vote_begin', 'create')
            ->allowEmptyDateTime('vote_begin', 'Required', false);

        $validator
            ->dateTime('vote_end')
            ->requirePresence('vote_end', 'create')
            ->allowEmptyDateTime('vote_end', 'Required', false);

        $validator
            ->dateTime('resubmit_deadline')
            ->requirePresence('resubmit_deadline', 'create')
            ->allowEmptyDateTime('resubmit_deadline', 'Required', false);

        $validator
            ->integer('funding_available')
            ->requirePresence('funding_available', 'create')
            ->notEmptyString('funding_available');

        return $validator;
    }

    /**
     * Modifies a query to return the funding cycle that is currently accepting applications
     *
     * @param \Cake\ORM\Query $query
     * @return \Cake\ORM\Query
     */
    public function findCurrent(Query $query)
    {
        $now = date('Y-m-d H:i:s');

        return $query
            ->where(['FundingCycles.application_begin <=' => $now])
            ->where(['FundingCycles.application_end >=' => $now]);
    }

    /**
     * Modifies a query to return funding cycles that begin accepting applications in the future
     *
     * @param \Cake\ORM\Query $query
     * @return \Cake\ORM\Query
     */
    public function findFuture(Query $query)
    {
        $now = date('Y-m-d H:i:s');

        return $query
            ->where(['FundingCycles.application_begin >=' => $now]);
    }

    /**
     * Modifies a query to return funding cycles that stopped accepting applications in the past
     *
     * @param \Cake\ORM\Query $query
     * @return \Cake\ORM\Query
     */
    public function findPast(Query $query)
    {
        $now = date('Y-m-d H:i:s');

        return $query
            ->where(['FundingCycles.application_end <=' => $now]);
    }

    /**
     * Modifies a query to return all funding cycles whose applications end in the future
     *
     * @param \Cake\ORM\Query $query
     * @return \Cake\ORM\Query
     */
    public function findCurrentAndFuture(Query $query)
    {
        $now = date('Y-m-d H:i:s');

        return $query
            ->where(['FundingCycles.application_end >=' => $now]);
    }

    /**
     * Modifies a query to return the funding cycle that is currently accepting votes
     *
     * @param \Cake\ORM\Query $query
     * @return \Cake\ORM\Query
     */
    public function findCurrentVoting(Query $query)
    {
        $now = date('Y-m-d H:i:s');

        return $query
            ->where(['FundingCycles.vote_begin <=' => $now])
            ->where(['FundingCycles.vote_end >=' => $now]);
    }

    /**
     * Modifies a query to return the funding cycle that will be accepting votes next
     *
     * @param \Cake\ORM\Query $query
     * @return \Cake\ORM\Query
     */
    public function findNextVoting(Query $query)
    {
        $now = date('Y-m-d H:i:s');

        return $query
            ->where(['FundingCycles.vote_begin >' => $now])
            ->orderAsc('FundingCycles.vote_begin');
    }

    /**
     * Modifies a query to return the funding cycle with the soonest future application deadline
     *
     * @param \Cake\ORM\Query $query
     * @return \Cake\ORM\Query
     */
    public function findNextProject(Query $query)
    {
        $now = date('Y-m-d H:i:s');

        return $query
            ->where(['FundingCycles.application_end >' => $now])
            ->orderAsc('FundingCycles.application_end');
    }

    public function getCurrentVotingInfo()
    {
        $cycle = $this
            ->find('currentVoting')
            ->first();
        if (!$cycle) {
            return null;
        }

        $projectCount = $this->Projects
            ->find('forVoting', ['funding_cycle_id' => $cycle->id])
            ->all()
            ->count();

        return compact('cycle', 'projectCount');
    }

    /**
     * Takes an array of funding cycles and groups them into categories based on what phase they're currently in
     *
     * @param FundingCycle[]|ResultSet $fundingCycles
     * @return null[]|FundingCycle[][]
     */
    static public function groupCycles($fundingCycles)
    {
        $groupedCycles = [];
        foreach ($fundingCycles as $fundingCycle) {
            if ($fundingCycle->isCurrentlyVoting()) {
                $group = self::GROUP_VOTING;
            } elseif ($fundingCycle->votingHasPassed()) {
                $group = self::GROUP_PAST;
            } elseif ($fundingCycle->isCurrentlyApplying()) {
                $group = self::GROUP_APPLYING;
            } else {
                $group = self::GROUP_UPCOMING;
            }
            $groupedCycles[$group][] = $fundingCycle;
        }
        return [
            self::GROUP_UPCOMING => $groupedCycles[self::GROUP_UPCOMING] ?? null,
            self::GROUP_APPLYING => $groupedCycles[self::GROUP_APPLYING] ?? null,
            self::GROUP_VOTING => $groupedCycles[self::GROUP_VOTING] ?? null,
            self::GROUP_PAST => $groupedCycles[self::GROUP_PAST] ?? null,
        ];
    }
}
