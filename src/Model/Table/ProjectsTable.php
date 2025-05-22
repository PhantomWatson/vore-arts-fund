<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Alert\ErrorAlert;
use App\Model\Entity\Project;
use ArrayObject;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\Event;
use Cake\I18n\FrozenDate;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
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
 * @property \App\Model\Table\TransactionsTable&\Cake\ORM\Association\HasMany $Transactions
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
            'saveStrategy' => 'replace',
        ]);
        $this->hasMany('Reports', [
            'foreignKey' => 'project_id',
        ]);
        $this->hasMany('Transactions', [
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
            ->notEmptyString('amount_requested')
            ->lessThan(
                'amount_requested',
                Project::MAXIMUM_ALLOWED_REQUEST + 1,
                'Sorry, but that request was too large. Please limit your request to no more than $'
                . number_format(Project::MAXIMUM_ALLOWED_REQUEST) . '.',
            );

        $validator
            ->boolean('accept_partial_payout')
            ->requirePresence('accept_partial_payout', 'create')
            ->notEmptyString('accept_partial_payout');

        $validator
            ->scalar('check_name')
            ->maxLength('check_name', 50)
            ->notEmptyString('check_name')
            ->requirePresence('check_name', 'create');

        $validator
            ->notEmptyString('loan_agreement_date')
            ->dateTime('loan_agreement_date');

        $validator
            ->notEmptyString('loan_due_date')
            ->dateTime('loan_due_date');

        $validator
            ->integer('loan_agreement_version')
            ->greaterThan('loan_agreement_version', 0)
            ->notEmptyString('loan_agreement_version');

        $validator
            ->scalar('tin')
            ->notEmptyString('tin');

        $validator
            ->scalar('address')
            ->maxLength('address', 50)
            ->notEmptyString('address');

        $validator
            ->scalar('zipcode')
            ->maxLength('zipcode', 10)
            ->notEmptyString('zipcode');

        $validator
            ->scalar('loan_agreement_signature')
            ->maxLength('loan_agreement_signature', 100)
            ->requirePresence('loan_agreement_signature', false)
            ->notEmptyString('loan_agreement_signature');

        $validator
            ->boolean('is_finalized');

        $validator
            ->date('loan_agreement_date')
            ->requirePresence('loan_agreement_date', false);

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
        $rules->add(
            function ($entity) {
                return in_array($entity->status_id, array_keys(Project::getStatuses()))
                    ? true
                    : 'Invalid status: ' . $entity->status_id;
            },
            'validStatus',
            ['errorField' => 'status_id'],
        );
        $rules->add(
            function ($entity) {
                if ($entity->status_id == Project::STATUS_AWARDED_NOT_YET_DISBURSED) {
                    return $entity->amount_awarded > 0
                        ? true
                        : 'Must include loan amount when declaring a project awarded';
                }
                return true;
            },
            'mustIncludeAmountAwardedWhenAwarded',
            ['errorField' => 'amount_awarded'],
        );

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
            ->find('notDeleted')
            ->where(['Projects.id' => $projectId])
            ->contain([
                'Answers',
                'FundingCycles',
                'Images' => function (Query $q) {
                    return $q->orderAsc('weight');
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
        return $this->getNotDeleted(
            $projectId,
            [
                'contain' => [
                    'Answers',
                    'Categories',
                    'FundingCycles',
                    'Images' => function (\Cake\Database\Query $q) {
                        return $q->orderAsc('weight');
                    },
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
            ->find('notDeleted')
            ->select([
                'Projects.accept_partial_payout',
                'Projects.amount_requested',
                'Projects.category_id',
                'Projects.description',
                'Projects.id',
                'Projects.title',
                'Projects.user_id',
                'Projects.funding_cycle_id',
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
                'FundingCycles' => function (Query $q) {
                    // The intention here is "select all", but columns must be listed explicitly
                    return $q->select([
                        'FundingCycles.id',
                        'FundingCycles.application_begin',
                        'FundingCycles.application_end',
                        'FundingCycles.resubmit_deadline',
                        'FundingCycles.vote_begin',
                        'FundingCycles.vote_end',
                        'FundingCycles.funding_available',
                        'FundingCycles.is_finalized'
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
     * Modifies a query to only return projects that can be displayed to the public
     *
     * @param \Cake\ORM\Query $query
     * @return \Cake\ORM\Query
     */
    public function findPublic(Query $query): Query
    {
        return $query->where(function (QueryExpression $exp) {
            return $exp->in('status', Project::VIEWABLE_STATUSES);
        });
    }

    public function afterSave(Event $event, EntityInterface $entity, ArrayObject $options): void
    {
        /** @var Project $entity */
        if ($entity->status_id == Project::STATUS_UNDER_REVIEW && (
            $entity->isNew() || $entity->statusWasJustChangedTo(Project::STATUS_UNDER_REVIEW)
        )) {
            $entity->dispatchSubmittedEvent();
        } elseif ($entity->statusWasJustChangedTo(Project::STATUS_WITHDRAWN)) {
            $entity->dispatchWithdrawnEvent();
        }
    }

    /**
     * "Soft-deletes" a project, saving it with the "deleted" status
     *
     * @param Project $project
     * @return Project|false
     */
    public function markDeleted(Project $project)
    {
        $project->status_id = Project::STATUS_DELETED;
        $result = $this->save($project);

        if ($result) {
            $project->dispatchMarkedDeletedEvent();
        }
        return $result;
    }

    /**
     * @param int $projectId
     * @return Project
     * @throws RecordNotFoundException
     */
    public function getNotDeleted($projectId, array $options = []): Project
    {
        $project = $this->get($projectId, $options);
        if ($project->isDeleted()) {
            throw new RecordNotFoundException('Project not found');
        }
        return $project;
    }

    /**
     * Modifies a query to exclude soft-deleted projects
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findNotDeleted(Query $query, array $options)
    {
        return $query->where(['Projects.status_id !=' => Project::STATUS_DELETED]);
    }

    public function setProjectAwardedDate($projectId, $date)
    {
        $project = $this->get($projectId);
        if ($project->loan_awarded_date) {
            $alert = new ErrorAlert();
            $alert->send(sprintf(
                'Update to loan awarded date for project %s blocked because it\'s already been set. Details: %s',
                $projectId,
                print_r($project->getErrors(), true)
            ));
            return;
        }

        $project->loan_awarded_date = new FrozenDate($date);
        if (!$this->save($project)) {
            $alert = new ErrorAlert();
            $alert->send(sprintf(
                'Failed to set loan awarded date for project %s. Details: %s',
                $projectId,
                print_r($project->getErrors(), true)
            ));
        }
    }

    public function setProjectAsFinalized($projectId): void
    {
        $project = $this->get($projectId);
        $project->is_finalized = true;
        if (!$this->save($project)) {
            $alert = new ErrorAlert();
            $alert->send(sprintf(
                'Failed to set project %s as finalized. Details: %s',
                $projectId,
                print_r($project->getErrors(), true)
            ));
        }
    }
}
