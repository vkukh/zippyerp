<?php

namespace ZippyERP\System\Pages;

use \Zippy\Html\Form\TextInput as TextInput;
use \ZippyERP\System\Helper;
use \ZippyERP\System\User;
use \ZippyERP\System\Application as App;
use \ZippyERP\System\System;
use \Zippy\Binding\PropertyBinding as Bind;

class Registration extends AdminBase
{

    public $_login, $_password, $_confirm;

    public function __construct()
    {
        parent::__construct();

        $form = new \Zippy\Html\Form\Form('regform');
        $form->add(new TextInput('r_userlogin', new Bind($this, '_login')));
        $form->add(new TextInput('r_userpassword', new Bind($this, '_password')));
        $form->add(new TextInput('r_confirmpassword', new Bind($this, '_confirm')));
        $form->add(new \Zippy\Html\Form\SubmitButton('r_submit'))->setClickHandler($this, 'onsubmit');

        $this->add($form);
    }

    public function onsubmit($sender)
    {
        $this->setError('');
        if ($this->_login == '') {
            $this->setError('Введите логин');
        } else
        if ($this->_password == '') {
            $this->setError('Введите пароль');
        } else
        if ($this->_confirm == '') {
            $this->setError('Подтвердите пароль');
        } else
        if ($this->_confirm != $this->_password) {
            $this->setError('Неверное подтверждение');
        } else
        if ($user = Helper::login($this->_login) != false) {
            $this->setError('Логин уже существует');
        } 

        if (!$this->isError()) {
            $user = new User();
            $user->userlogin = $this->_login;

           $user->userpass = (\password_hash($this->_password, PASSWORD_DEFAULT));
            $user->Save();

            App::Redirect('\\ZippyERP\\System\\Pages\\UserInfo',$user->user_id);
        }
        $this->_confirm = '';
        $this->_password = '';

    }

}
