<?php

namespace ZippyERP\System\Pages;

use Zippy\Binding\PropertyBinding as Bind;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Label;
use ZippyERP\System\System;

class UserProfile extends \ZippyERP\System\Pages\Base
{

    public $_userlogin, $_userpass, $_confirm;

    public function __construct()
    {
        parent::__construct();

        $user = System::getUser();

        $this->_userlogin = $user->userlogin;
        //форма   профиля
        $form = new \Zippy\Html\Form\Form('profileform');
        $form->add(new Label('userlogin', new Bind($this, '_userlogin')));
        $form->add(new TextInput('userpassword', new Bind($this, '_userpass')));
        $form->add(new TextInput('confirmpassword', new Bind($this, '_confirm')));
        $form->add(new \Zippy\Html\Form\SubmitButton('submitpass'))->onClick($this, 'onsubmitpass');

        $form->onSubmit($this, 'onsubmit');
        $this->add($form);
    }

    //записать  пароль
    public function onsubmitpass($sender)
    {
        $this->setError('');

        if ($this->_userpass == '') {
            $this->setError('Введите пароль');
        } else
        if ($this->_confirm == '') {
            $this->setError('Подтвердите пароль');
        } else
        if ($this->_confirm != $this->_userpass) {
            $this->setError('Неверное подтверждение');
        }


        if (!$this->isError()) {
            $user = System::getUser();
            $user->userpass = (\password_hash($this->_userpass, PASSWORD_DEFAULT));

            $user->save();
        }
        $this->_confirm = '';
        $this->_userpass = '';
    }

    //запись  профиля
    public function onsubmit($sender)
    {

        if (!$this->isError()) {
            $user = System::getUser();
            //$uploaddir = UPLOAD_USERS;
            // @mkdir($uploaddir) ;

            $user->save();
        }
    }

    public function beforeRender()
    {
        parent::beforeRender();

        //$user = System::getUser();
    }

}
