<?php

namespace ZippyERP\System\Pages;

use ZippyERP\System\Application as App;
use ZippyERP\System\System;

//базовая страница  для  страниц пользователя
class UserBase extends \Zippy\Html\WebPage
{

    public $_errormsg;

    public function __construct()
    {

        $this->add(new \ZippyERP\System\Blocks\Header("header"));
        $this->add(new \Zippy\Html\Label("errormessage", new \Zippy\Binding\PropertyBinding($this, '_errormsg'), false, true))->setVisible(false);

        $user = System::getUser();
        if ($user->user_id == 0) {
            App::Redirect("\\ZippyERP\\System\\Pages\\Userlogin");
        }
    }

    public function setError($msg)
    {
        $this->_errormsg = $msg;
        $this->errormessage->setVisible(true);
    }

    protected function beforeRender()
    {
        $this->errormessage->setVisible(strlen($this->_errormsg) > 0);
    }

    protected function afterRender()
    {
        $this->setError('');
    }

    protected function isError()
    {
        return strlen($this->_errormsg) > 0 ? true : false;
    }

}
