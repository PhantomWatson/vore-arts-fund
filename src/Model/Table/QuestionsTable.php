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
 * @method \App\Model\Entity\Question newEmptyEntity()
 * @method \App\Model\Entity\Question newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Question[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Question get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Question findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array $search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Question patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Question[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Question|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Question saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Question[]|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Question>|false saveMany(iterable $entities, array $options = [])
 * @method \App\Model\Entity\Question[]|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Question> saveManyOrFail(iterable $entities, array $options = [])
 * @method \App\Model\Entity\Question[]|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Question>|false deleteMany(iterable $entities, array $options = [])
 * @method \App\Model\Entity\Question[]|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Question> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @extends \Cake\ORM\Table<array{Timestamp: \Cake\ORM\Behavior\TimestampBehavior}>
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
     * Modifies a query to return appropriate results for displaying in a project
     *
     * @param \Cake\ORM\Query $query
     * @return \Cake\ORM\Query
     */
    public function findForProject(Query $query)
    {
        return $query
            ->where(['enabled' => true])
            ->orderByAsc('weight');
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

    /**
     * Returns the highest weight of an enabled question (or zero if there are no questions)
     *
     * @return int
     */
    public function getHighestWeight(): int
    {
        /** @var Question $result */
        $result = $this
            ->find()
            ->select(['weight'])
            ->where(['enabled' => true])
            ->orderByDesc('weight')
            ->first();
        return $result ? $result->weight : 0;
    }
}
