<?php

App::uses('AppModel', 'Model');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

class User extends AppModel {
    public $name = 'User';
    public $validate = array(
        'email' => array(
            'required' => array(
                'rule' => 'email',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Une adresse email est requise',
                'on' => 'create'
            ),
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'Cette adresse email est déjà utilisée',
                'on' => 'create'
            )
        ),
        'password' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Un mot de passe est requis',
                'on' => 'create'
            )
        )
    );

    public function beforeSave($options = array()) {
        if (isset($this->data[$this->alias]['password'])) {
            $passwordHasher = new SimplePasswordHasher();
            $this->data[$this->alias]['password'] = $passwordHasher->hash(
                $this->data[$this->alias]['password']
            );
        }
        return true;
    }

    public function sign_in($data) {
        if(isset($data['User']['email'])) {
            $user = $this->findByEmail($data['User']['email']);
            return $user
                && $this->comparePasswords($data['User']['password'], $user['User']['password'])
                && $this->generateApiKey($user['User']['id']);
        }
        return false;
    }

    public function comparePasswords($clear_password, $hashed_password) {
        $passwordHasher = new SimplePasswordHasher();
        return $hashed_password == $passwordHasher->hash($clear_password);
    }

    public function generateApiKey($user_id) {
        $api_key = bin2hex(openssl_random_pseudo_bytes(127));
        if($user_id) {
            $data = array(
                'User' => array(
                    'id' => $user_id,
                    'api_key' => $api_key,
                    'api_key_expiration' => date('Y-m-d H:i:s', strtotime('+1 years'))
                    )
                );
            if($this->save($data))
                return true;
        }
        return false;        
    }
}
