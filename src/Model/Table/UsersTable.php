<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\Project;
use App\Model\Entity\User;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \App\Model\Table\ProjectsTable&\Cake\ORM\Association\HasMany $Projects
 * @property \App\Model\Table\MessagesTable&\Cake\ORM\Association\HasMany $Messages
 * @property \App\Model\Table\NotesTable&\Cake\ORM\Association\HasMany $Notes
 * @property \App\Model\Table\VotesTable&\Cake\ORM\Association\HasMany $Votes
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\ORM\ResultSet findByEmail($email)
 * @method \App\Model\Entity\User[]|\Cake\ORM\ResultSet findByResetPasswordToken($token)
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
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

        $this->setTable('users');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Projects', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Messages', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Notes', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Votes', [
            'foreignKey' => 'user_id',
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

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email');

        $validator
            ->scalar('password')
            ->maxLength('password', 200)
            ->requirePresence('password', 'create')
            ->notEmptyString('password');

        /* Using different names than the actual auth fields so the authentication middleware doesn't log the user in
         * if they try to register an existing account */
        $validator
            ->email('registerEmail')
            ->requirePresence('registerEmail', 'create')
            ->notEmptyString('registerEmail');
        $validator
            ->scalar('registerPassword')
            ->maxLength('registerPassword', 200)
            ->requirePresence('registerPassword', 'create')
            ->notEmptyString('registerPassword');

        $validator
            ->boolean('is_admin')
            ->requirePresence('is_admin', 'create')
            ->notEmptyString('is_admin');

        $validator
            ->boolean('is_verified')
            ->requirePresence('is_verified', 'create')
            ->notEmptyString('is_verified');

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
        $rules->add(
            function (User $user) {
                $phone = User::cleanPhone((string)$user->phone);
                if ($phone === '') {
                    return true;
                }
                if ($phone === Configure::read('testPhoneNumber')) {
                    return true;
                }
                $conditions = compact('phone');
                if ($user->id ?? false) {
                    $conditions['id !='] = $user->id;
                }
                return !$this->exists($conditions);
            },
            'uniquePhoneNumber',
            [
                'errorField' => 'phone',
                'message' => 'This phone number is already in use'
            ]
        );

        $rules->add($rules->isUnique(['email'], 'This email address is already registered'));

        return $rules;
    }

    public function findForArtMart(Query $query, array $options)
    {
        return $query->select(['id', 'name'])
            ->matching('Projects', function (\Cake\Database\Query $query) {
                return $query->where(['status_id' => Project::STATUS_AWARDED_AND_DISBURSED]);
            })
            ->contain([
                'Projects' => function (Query $query) {
                    return $query
                        ->select(['id', 'title', 'amount_awarded', 'loan_agreement_date', 'user_id'])
                        ->where(['status_id' => Project::STATUS_AWARDED_AND_DISBURSED])
                        ->orderDesc('loan_agreement_date');
                }
            ])
            ->orderAsc('name');
    }
}
