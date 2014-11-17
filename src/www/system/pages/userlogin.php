<?php

namespace ZippyERP\System\Pages;

use \Zippy\Html\Form\TextInput as TextInput;
use \ZippyERP\System\Helper;
use \ZippyERP\System\User;
use \ZippyERP\System\Application as App;
use \ZippyERP\System\System;
use \Zippy\Binding\PropertyBinding as Bind;

class UserLogin extends Base
{

    public $_login, $_password;

    public function __construct()
    {
        parent::__construct();

        $form = new \Zippy\Html\Form\Form('loginform');
        $form->add(new TextInput('userlogin', new Bind($this, '_login')));
        $form->add(new TextInput('userpassword', new Bind($this, '_password')));
        $form->add(new \Zippy\Html\Form\CheckBox('remember'));
        $form->add(new \Zippy\Html\Form\SubmitButton('submit'))->setClickHandler($this, 'onsubmit');

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
        }

        if (strlen($this->_login) > 0 && strlen($this->_password)) {

            $user = Helper::login($this->_login, $this->_password);

            if ($user instanceof User) {
                System::setUser($user);
                $_SESSION['user_id'] = $user->user_id; //для  использования  вне  Application
                $_SESSION['userlogin'] = $user->userlogin; //для  использования  вне  Application
                //App::$app->getResponse()->toBack();
                if ($this->loginform->remember->isChecked()) {
                    $_config = parse_ini_file(_ROOT . 'config/config.ini', true);
                    setcookie("remember", $user->user_id . '_' . md5($user->user_id . $_config['common']['salt']), time() + 60 * 60 * 24 * 30);
                }

                @mkdir(_ROOT . UPLOAD_USERS . $user->user_id);
                \ZippyERP\System\Util::removeDirRec(_ROOT . UPLOAD_USERS . $user->user_id . '/tmp');
                @mkdir(_ROOT . UPLOAD_USERS . $user->user_id . '/tmp');

                App::RedirectHome();
            } else {
                $this->setError('Неверный  логин');
            }
        }

        $this->_password = '';
    }

    public function beforeRequest()
    {
        if (System::getUser()->user_id > 0) {
            App::RedirectHome();
        }
    }

}
