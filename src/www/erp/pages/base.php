<?php

namespace ZippyERP\ERP\Pages;

use \ZippyERP\ERP\Helper;
use \ZippyERP\System\System;
use \ZippyERP\System\Application as App;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;

//базовая страница
class Base extends \Zippy\Html\WebPage
{

    public $_errormsg;
    public $_warnmsg;
    public $_successmsg;

    public function __construct()
    {

        $this->add(new \ZippyERP\System\Blocks\Header("header"));
        $this->add(new Label("errormessage", new \Zippy\Binding\PropertyBinding($this, '_errormsg'), false, true))->setVisible(false);
        $this->add(new Label("warnmessage", new \Zippy\Binding\PropertyBinding($this, '_warnmsg'), false, true))->setVisible(false);
        $this->add(new Label("successmessage", new \Zippy\Binding\PropertyBinding($this, '_successmsg'), false, true))->setVisible(false);
        $this->add(new Label("menudoc", Helper::generateMenu(1), true));
        $this->add(new Label("menurep", Helper::generateMenu(2), true));
        $this->add(new Label("menureg", Helper::generateMenu(3), true));
        $this->add(new Label("menuref", Helper::generateMenu(4), true));
        $this->add(new Label("menupage", Helper::generateMenu(5), true));
        $this->add(new ClickLink("pageinfo"))->setAttribute("data-content", $this->getPageInfo());
        ;

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
        $this->errormessage->setVisible(strlen($this->_errormsg) > 0);
        $this->warnmessage->setVisible(strlen($this->_warnmsg) > 0);
        $this->successmessage->setVisible(strlen($this->_successmsg) > 0);
    }

    protected function afterRender()
    {
        $this->setError('');
        $this->setWarn('');
        $this->setSuccess('');
    }

    protected function isError()
    {
        return strlen($this->_errormsg) > 0 ? true : false;
    }

    /**
     * Функция  возвращающая  описание страницы.
     * Может  перегружатся  дочерними  страницами.
     * Как  правило  выводится  описание с  обьекта  метадагнных
     * @return mixed
     */
    public function getPageInfo()
    {
        $class = explode("\\", get_class($this));
        $classname = $class[count($class) - 1];
        $info = \ZippyERP\ERP\Helper::getMetaNotes($classname);
        if (strlen($info) == 0) {
            return "Об этой  странице нет информации";
        } else {
            return $info;
        }
    }

}
