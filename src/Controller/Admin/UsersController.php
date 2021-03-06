<?php
namespace Users\Controller\Admin;

use Cake\Event\Event;

/**
 * Users Controller
 *
 * @property \Users\Model\Table\UsersTable $Users
 *
 * @method \Users\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{

    /**
     * @param Event $event
     * @return \Cake\Http\Response|null|void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['forgot', 'reset']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Roles']
        ];
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Roles']
        ]);

        $this->set('user', $user);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->viewBuilder()->setTemplate('form');

        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__d('localized', 'The {0} has been saved.', __d('localized', 'User')));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__d('localized', 'The {0} could not be saved. Please, try again.', __d('localized', 'User')));
        }
        $roles = $this->Users->Roles->find('list', ['limit' => 200]);
        $this->set(compact('user', 'roles'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->viewBuilder()->setTemplate('form');

        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__d('localized', 'The {0} has been saved.', __d('localized', 'User')));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__d('localized', 'The {0} could not be saved. Please, try again.', __d('localized', 'User')));
        }
        $roles = $this->Users->Roles->find('list', ['limit' => 200]);
        $this->set(compact('user', 'roles'));
    }

    /**
     * Login method
     *
     * @return \Cake\Http\Response|null
     */
    public function login()
    {
        $this->viewBuilder()->setLayout('Cirici/AdminLTE.login');

        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Flash->error(__d('localized', 'Username or password is incorrect'));
            }
        }
    }

    /**
     * Logout method
     *
     * @return \Cake\Http\Response|null
     */
    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }

    /**
     * Forgot method
     *
     * @return \Cake\Http\Response|null
     */
    public function forgot()
    {
        $this->viewBuilder()->setLayout('Cirici/AdminLTE.login');

        if ($this->request->is('post')) {
            $username = $this->request->getData('username');

            $user = $this->Users
                ->find('active')
                ->find('admin')
                ->where(['username' => $username])
                ->first();

            if (!$user) {
                $this->Flash->error(__d('localized', 'Invalid username'));
                return;
            }

            $success = $this->Users->resetPassword($user);
            if (!$success) {
                $this->Flash->error(__d('localized', 'An error occurred. Please try again.'));
                return;
            }

            $this->Flash->success(__d('localized', 'An email has been sent with instructions for resetting your password.'));
            return $this->redirect(['action' => 'login']);
        }
    }

    /**
     * Reset method
     *
     * @param $username
     * @param $token
     * @return \Cake\Http\Response|null
     */
    public function reset($username = null, $token = null)
    {
        $this->viewBuilder()->setLayout('Cirici/AdminLTE.login');

        $user = $this->Users
            ->find('active')
            ->find('admin')
            ->where(['username' => $username, 'token' => $token])
            ->first();

        if (!$user) {
            $this->Flash->error(__d('localized', 'An error occurred.'));
            return $this->redirect(['action' => 'login']);
        }

        if ($this->request->is(['put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__d('localized', 'Your password has been reset successfully.'));
                return $this->redirect(['action' => 'login']);
            } else {
                $this->Flash->error(__d('localized', 'Your password could not be saved. Please, try again.'));
            }
        }

        $this->set(compact('user'));
    }

    /**
     * Change Password method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function changePassword($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);

        $user->password = '';

        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__d('localized', 'The {0} has been saved.', __d('localized', 'User')));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__d('localized', 'The {0} could not be saved. Please, try again.', __d('localized', 'User')));
        }
        $this->set(compact('user'));
    }
}
