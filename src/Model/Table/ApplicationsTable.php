<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Application;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Applications Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\CategoriesTable&\Cake\ORM\Association\BelongsTo $Categories
 * @property \App\Model\Table\FundingCyclesTable&\Cake\ORM\Association\BelongsTo $FundingCycles
 * @property \App\Model\Table\ImagesTable&\Cake\ORM\Association\HasMany $Images
 * @property \App\Model\Table\MessagesTable&\Cake\ORM\Association\HasMany $Messages
 * @property \App\Model\Table\NotesTable&\Cake\ORM\Association\HasMany $Notes
 * @property \App\Model\Table\VotesTable&\Cake\ORM\Association\HasMany $Votes
 * @method Application get($primaryKey, $options = [])
 * @method Application newEntity(array $data, array $options = [])
 * @method Application[] newEntities(array $data, array $options = [])
 * @method Application|false save(EntityInterface $entity, $options = [])
 * @method Application saveOrFail(EntityInterface $entity, $options = [])
 * @method Application patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method Application[] patchEntities($entities, array $data, array $options = [])
 * @method Application findOrCreate($search, callable $callback = null, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @method Application[]|ResultSetInterface|false saveMany($entities, $options = [])
 */
class ApplicationsTable extends Table
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

        $this->setTable('applications');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Categories', [
            'foreignKey' => 'category_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('FundingCycles', [
            'foreignKey' => 'funding_cycle_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Images', [
            'foreignKey' => 'application_id',
        ]);
        $this->hasMany('Messages', [
            'foreignKey' => 'application_id',
        ]);
        $this->hasMany('Notes', [
            'foreignKey' => 'application_id',
        ]);
        $this->hasMany('Votes', [
            'foreignKey' => 'application_id',
        ]);
        $this->hasMany('Answers', [
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('title')
            ->maxLength('title', 200)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('description')
            ->maxLength('description', 2000)
            ->requirePresence('description', 'create')
            ->notEmptyString('description');

        $validator
            ->integer('amount_requested')
            ->requirePresence('amount_requested', 'create')
            ->notEmptyString('amount_requested');

        $validator
            ->boolean('accept_partial_payout')
            ->requirePresence('accept_partial_payout', 'create')
            ->notEmptyString('accept_partial_payout');

        $validator
            ->integer('status_id')
            ->requirePresence('status_id', 'create')
            ->inList('status_id', Application::getStatuses(), 'Invalid status');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['category_id'], 'Categories'));
        $rules->add($rules->existsIn(['funding_cycle_id'], 'FundingCycles'));

        return $rules;
    }

    /**
     * Returns an application entity for the apply/edit form
     *
     * @param int $applicationId
     * @return EntityInterface|Application|null
     */
    public function getForForm($applicationId)
    {
        return $this
            ->find()
            ->where(['Applications.id' => $applicationId])
            ->contain('FundingCycles')
            ->first();
    }
}
