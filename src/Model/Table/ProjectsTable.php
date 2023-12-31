<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Note;
use App\Model\Entity\Project;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Projects Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\CategoriesTable&\Cake\ORM\Association\BelongsTo $Categories
 * @property \App\Model\Table\FundingCyclesTable&\Cake\ORM\Association\BelongsTo $FundingCycles
 * @property \App\Model\Table\ImagesTable&\Cake\ORM\Association\HasMany $Images
 * @property \App\Model\Table\MessagesTable&\Cake\ORM\Association\HasMany $Messages
 * @property \App\Model\Table\NotesTable&\Cake\ORM\Association\HasMany $Notes
 * @property \App\Model\Table\VotesTable&\Cake\ORM\Association\HasMany $Votes
 * @property \App\Model\Table\ReportsTable&\Cake\ORM\Association\HasMany $Reports
 * @method Project get($primaryKey, $options = [])
 * @method Project newEntity(array $data, array $options = [])
 * @method Project[] newEntities(array $data, array $options = [])
 * @method Project|false save(EntityInterface $entity, $options = [])
 * @method Project saveOrFail(EntityInterface $entity, $options = [])
 * @method Project patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method Project[] patchEntities($entities, array $data, array $options = [])
 * @method Project findOrCreate($search, callable $callback = null, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @method Project[]|ResultSetInterface|false saveMany($entities, $options = [])
 */
class ProjectsTable extends Table
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

        $this->setTable('projects');
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
            'foreignKey' => 'project_id',
        ]);
        $this->hasMany('Messages', [
            'foreignKey' => 'project_id',
        ]);
        $this->hasMany('Notes', [
            'foreignKey' => 'project_id',
        ]);
        $this->hasMany('Votes', [
            'foreignKey' => 'project_id'
        ]);
        $this->hasMany('Answers', [
            'foreignKey' => 'project_id',
            'dependent' => true,
        ]);
        $this->hasMany('Reports', [
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
            ->inList('status_id', array_keys(Project::getStatuses()), 'Invalid status');

        $validator
            ->scalar('check_name')
            ->maxLength('check_name', 50)
            ->notEmptyString('check_name')
            ->requirePresence('check_name', 'create');

        // Actually saved to the users table, but integrated into the project form
        $validator
            ->scalar('address')
            ->maxLength('address', 50)
            ->notEmptyString('address');
        $validator
            ->scalar('zipcode')
            ->maxLength('zipcode', 10)
            ->notEmptyString('zipcode');

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
     * Returns a project entity for the apply/edit form
     *
     * @param int $projectId
     * @return EntityInterface|Project|null
     */
    public function getForForm($projectId)
    {
        return $this
            ->find()
            ->where(['Projects.id' => $projectId])
            ->contain([
                'Answers',
                'FundingCycles',
                'Images' => function (Query $q) {
                    return $q->orderDesc('weight');
                },
            ])
            ->first();
    }

    /**
     * @param int $projectId
     * @return Project
     */
    public function getForViewing($projectId)
    {
        return $this->get(
            $projectId,
            [
                'contain' => [
                    'Answers',
                    'Categories',
                    'FundingCycles',
                    'Images',
                    'Users',
                    'Reports' => function (Query $query) {
                        return $query->select(['id', 'project_id']);
                    },
                    'Notes' => function (Query $query) {
                        return $query->find('notInternal');
                    },
                ]
            ]
        );
    }

    /**
     * Modifies a query to return the votable projects for the specified funding cycle, including only the
     * necessary fields
     *
     * @param \Cake\ORM\Query $query
     * @param array $options
     * @return \Cake\ORM\Query
     */
    public function findForVoting(Query $query, array $options)
    {
        return $query
            ->select([
                'Projects.accept_partial_payout',
                'Projects.amount_requested',
                'Projects.category_id',
                'Projects.description',
                'Projects.id',
                'Projects.title',
                'Projects.user_id',
            ])
            ->where([
                'Projects.funding_cycle_id' => $options['funding_cycle_id'],
                'Projects.status_id' => Project::STATUS_ACCEPTED,
            ])
            ->contain([
                'Answers' => function (Query $q) {
                    return $q
                        ->select([
                            'Answers.answer',
                            'Answers.project_id',
                            'Answers.id',
                            'Answers.question_id',
                        ])
                        ->contain([
                            'Questions' => function (Query $q) {
                                return $q->select([
                                    'Questions.id',
                                    'Questions.question',
                                    'Questions.weight',
                                ]);
                            }
                        ]);
                },
                'Categories' => function (Query $q) {
                    return $q->select([
                        'Categories.id',
                        'Categories.name',
                    ]);
                },
                'Images' => function (Query $q) {
                    return $q->select([
                        'Images.project_id',
                        'Images.filename',
                        'Images.id',
                        'Images.weight',
                    ]);
                },
                'Users' => function (Query $q) {
                    return $q->select([
                        'Users.id',
                        'Users.name',
                    ]);
                },
            ])
            ->orderAsc('Projects.title');
    }

    /**
     * Returns projects that have not yet been finalized (finished giving reports and presumably have finished paying
     * their loans)
     *
     * @param Query $query
     * @return Query
     */
    public function findNotFinalized(Query $query)
    {
        return $query
            ->notMatching('Reports', function (Query $q) {
                return $q->where([
                    'Reports.is_final' => true
                ]);
            });
    }

    /**
     * Modifies a query to return projects that are either accepted and awaiting voting or have been part of voting
     *
     * @param Query $query
     * @return Query
     */
    public function findAcceptedOrGreater(Query $query): Query
    {
        return $query
            ->where([
                function (QueryExpression $exp) {
                    return $exp->in(
                        'Projects.status_id',
                        [
                            Project::STATUS_ACCEPTED,
                            Project::STATUS_AWARDED,
                            Project::STATUS_NOT_AWARDED,
                        ]
                    );
                }
            ]);
    }
}
