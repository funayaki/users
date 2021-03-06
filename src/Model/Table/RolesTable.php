<?php

namespace Users\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Roles Model
 *
 * @property \Users\Model\Table\UsersTable|\Cake\ORM\Association\HasMany $Users
 *
 * @method \Users\Model\Entity\Role get($primaryKey, $options = [])
 * @method \Users\Model\Entity\Role newEntity($data = null, array $options = [])
 * @method \Users\Model\Entity\Role[] newEntities(array $data, array $options = [])
 * @method \Users\Model\Entity\Role|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Users\Model\Entity\Role patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Users\Model\Entity\Role[] patchEntities($entities, array $data, array $options = [])
 * @method \Users\Model\Entity\Role findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RolesTable extends Table
{
    const ROLE_ADMIN = 1;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('roles');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Users', [
            'foreignKey' => 'role_id'
        ]);

        $this->addBehavior('Acl.Acl', ['type' => 'requester']);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('title')
            ->maxLength('title', 100)
            ->requirePresence('title', 'create')
            ->notEmpty('title');

        $validator
            ->scalar('alias')
            ->maxLength('alias', 100)
            ->requirePresence('alias', 'create')
            ->notEmpty('alias');

        return $validator;
    }
}
