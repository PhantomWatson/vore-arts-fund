<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Question;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Questions Model
 *
 * @property \App\Model\Table\AnswersTable&\Cake\ORM\Association\HasMany $Answers
 * @method Question newEmptyEntity()
 * @method Question newEntity(array $data, array $options = [])
 * @method Question[] newEntities(array $data, array $options = [])
 * @method Question get($primaryKey, $options = [])
 * @method Question findOrCreate($search, ?callable $callback = null, $options = [])
 * @method Question patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method Question[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method Question|false save(EntityInterface $entity, $options = [])
 * @method Question saveOrFail(EntityInterface $entity, $options = [])
 * @method Question[]|ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method Question[]|ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method Question[]|ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method Question[]|ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class QuestionsTable extends Table
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

        $this->setTable('questions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Answers', [
            'foreignKey' => 'question_id',
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
            ->scalar('question')
            ->allowEmptyString('question');

        $validator
            ->boolean('enabled')
            ->notEmptyString('enabled');

        $validator
            ->integer('weight')
            ->notEmptyString('weight');

        return $validator;
    }

    /**
     * Modifies a query to return appropriate results for displaying in an application
     *
     * @param \Cake\ORM\Query $query
     * @return \Cake\ORM\Query
     */
    public function findForApplication(Query $query)
    {
        return $query
            ->where(['enabled' => true])
            ->orderAsc('weight');
    }

    /**
     * @param RulesChecker $rules
     * @return RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        // Prevent deleting questions with answers
        $rules->addDelete(function ($entity, $options) {
            return $entity->hasAnswers;
        }, 'cantDeleteQuestionsWithAnswers');

        return $rules;
    }
}
