<?php

class PostsController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->autoRender = false;
        $this->Auth->allow('add', 'edit');
    }

    public function index() {
        $posts = array('tadaa', 'post2');
        return json_encode($posts);
    }

    public function view($id = null) {
        return json_encode(array('bcejozp', 'bcuziopb'));
    }


}