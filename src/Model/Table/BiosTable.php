<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Bio;
use Cake\Http\Exception\InternalErrorException;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Bios Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Bio newEmptyEntity()
 * @method \App\Model\Entity\Bio newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Bio[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Bio get($primaryKey, $options = [])
 * @method \App\Model\Entity\Bio findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Bio patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Bio[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Bio|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Bio saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Bio[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Bio[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Bio[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Bio[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class BiosTable extends Table
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

        $this->setTable('bios');
        $this->setDisplayField('bio');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
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
            ->integer('user_id')
            ->notEmptyString('user_id');

        $validator
            ->scalar('title')
            ->maxLength('title', 100);

        $validator
            ->scalar('bio')
            ->maxLength('bio', 2000);

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
        $rules->add($rules->existsIn('user_id', 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }

    /**
     * Returns the existing bio or a new (unsaved) bio for this user
     *
     * @param int $userId
     * @return Bio
     * @throws InternalErrorException
     */
    public function getForUser(int $userId): Bio
    {
        /** @var Bio|false $bio */
        $bio = $this->find()->where(['user_id' => $userId])->first();
        if ($bio) {
            return $bio;
        }

        $bio = $this->newEmptyEntity();
        $bio->user_id = $userId;
        $bio->title = 'Director';
        $user = $this->Users->get($userId);
        $bio->bio = $user->name . ' is...';
        return $bio;
    }
}
