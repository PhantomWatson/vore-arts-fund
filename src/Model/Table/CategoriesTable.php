<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Categories Model
 *
 * @property \App\Model\Table\ProjectsTable&\Cake\ORM\Association\HasMany $Projects
 * @method \App\Model\Entity\Category get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Category newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Category[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Category|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Category saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Category patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Category[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Category findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array $search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Category[]|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Category>|false saveMany(iterable $entities, array $options = [])
 * @method \App\Model\Entity\Category newEmptyEntity()
 * @method \App\Model\Entity\Category[]|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Category> saveManyOrFail(iterable $entities, array $options = [])
 * @method \App\Model\Entity\Category[]|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Category>|false deleteMany(iterable $entities, array $options = [])
 * @method \App\Model\Entity\Category[]|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Category> deleteManyOrFail(iterable $entities, array $options = [])
 */
class CategoriesTable extends Table
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

        $this->setTable('categories');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Projects', [
            'foreignKey' => 'category_id',
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
            ->scalar('name')
            ->maxLength('name', 200)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        return $validator;
    }

    /**
     * Returns a list-style array of categories alphabetized, but with "Other" at the end
     *
     * @return string[]
     */
    public function getOrdered()
    {
        $categories = $this
            ->find('list')
            ->where(function (QueryExpression $exp) {
                return $exp->notLike('name', '%other%');
            })
            ->orderByAsc('name')
            ->toArray();

        return $categories + $this
            ->find('list')
            ->where(function (QueryExpression $exp) {
                return $exp->like('name', '%other%');
            })
            ->orderByAsc('name')
            ->toArray();
    }
}
