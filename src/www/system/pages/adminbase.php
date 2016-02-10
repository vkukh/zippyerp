<?php

namespace ZippyERP\System\Pages;

use \ZippyERP\System\System;
use \ZippyERP\System\Application as App;

//базовая страница  для  страниц администратора
class AdminBase extends \Zippy\Html\WebPage
{

    public $_errormsg;
    public $_warnmsg;
    public $_successmsg;

    public function __construct()
    {
        $this->title = 'Страница администратора';
        $this->add(new \ZippyERP\System\Blocks\Header("header"));
        $this->add(new \Zippy\Html\Label("errormessage", new \Zippy\Binding\PropertyBinding($this, '_errormsg'), false, true))->setVisible(false);
        $this->add(new \Zippy\Html\Label("warnmessage", new \Zippy\Binding\PropertyBinding($this, '_warnmsg'), false, true))->setVisible(false);
        $this->add(new \Zippy\Html\Label("successmessage", new \Zippy\Binding\PropertyBinding($this, '_successmsg'), false, true))->setVisible(false);

        $user = System::getUser();
        if ($user->user_id == 0) {
            App::Redirect("\\ZippyERP\\System\\Pages\\Userlogin");
        }

        if ($user->userlogin !== 'admin') {
            App::Redirect('\ZippyERP\Pages\Error', 'Вы не админ');
        }
    }

    function setError($msg)
    {
        $this->_errormsg = $msg;
        $this->errormessage->setVisible(true);
    }
    public function setWarn($msg)
    {
        $this->_warnmsg = $msg;
        $this->warnmessage->setVisible(true);
    }

    public function setSuccess($msg)
    {
        $this->_successmsg = $msg;
        $this->successmessage->setVisible(true);
    }
    protected function beforeRender()
    {
        //     $this->errormessage->setVisible(strlen($this->_errormsg) > 0);
    }

    protected function afterRender()
    {
        $this->errormessage->setVisible(false);
        $this->warnmessage->setVisible(false);
        $this->successmessage->setVisible(false);
    }

    final protected function isError()
    {
        return strlen($this->_errormsg) > 0;
    }

}
