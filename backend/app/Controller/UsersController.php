<?php

class UsersController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('add', 'logout', 'login', 'test');
    }

    public function index() {
        $this->User->recursive = 0;
        $this->set('users', $this->paginate());
    }

    public function view($id = null) {
        if (!$this->User->exists($id)) {
            throw new NotFoundException(__('User invalide'));
        }
        $this->set('user', $this->User->findById($id));
    }

    public function test() {
        pr('test');
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $user_id = $this->User->getLastInsertId();
                if($this->User->generateApiKey($user_id)) {
                    $this->set(array(
                        'message' => array(
                            'register_success' => true,
                            'api_key' => $this->User->findById($user_id)['User']['api_key']
                        ),
                        '_serialize' => array('message')
                    )
                    );
                }                
            }
            else {
                $this->set(array(
                    'message' => array(
                        'register_success' => false,
                        'validation_errors' => $this->User->validationErrors
                    ),
                    '_serialize' => array('message')
                ));
            }
        }
    }

    public function edit($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('User Invalide'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->save($this->request->data)) {
                $this->Flash->success(__('L\'user a été sauvegardé'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(__('L\'user n\'a pas été sauvegardé. Merci de réessayer.'));
            }
        } else {
            $this->request->data = $this->User->findById($id);
            unset($this->request->data['User']['password']);
        }
    }

    public function delete($id = null) {
        // Avant 2.5, utilisez
        // $this->request->onlyAllow('post');

        $this->request->allowMethod('post');

        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('User invalide'));
        }
        if ($this->User->delete()) {
            $this->Flash->success(__('User supprimé'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Flash->error(__('L\'user n\'a pas été supprimé'));
        return $this->redirect(array('action' => 'index'));
    }

	public function login() {
        if($this->request->is('post')) {
            if($this->User->sign_in($this->request->data)) {
                $user = $this->User->findByEmail($this->request->data['User']['email']);
                $this->set(array(
                    'message' => array(
                        'login_success' => true,
                        'api_key' => $user['User']['api_key']
                        ),
                    '_serialize' => array('message')
                ));
            }
            else {
                $this->set(array(
                    'message' => array(
                        'login_success' => false
                        ),
                    '_serialize' => array('message')
                ));
            }
        }
	}

	public function logout() {
	    return $this->redirect($this->Auth->logout());
	}

}