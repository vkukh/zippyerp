<?php

namespace ZippyERP\System\Pages;

use \Zippy\Binding\PropertyBinding;

class Base extends \Zippy\Html\WebPage
{

    public $_errormsg;
    public $_warnmsg;
    public $_successmsg;

    public function __construct($params = null)
    {
        \Zippy\Html\WebPage::__construct();
        $this->add(new \ZippyERP\System\Blocks\Header("header"));
        $this->add(new \Zippy\Html\Label("warnmessage", new \Zippy\Binding\PropertyBinding($this, '_warnmsg'), false, true))->setVisible(false);
        $this->add(new \Zippy\Html\Label("successmessage", new \Zippy\Binding\PropertyBinding($this, '_successmsg'), false, true))->setVisible(false);

        $this->add(new \Zippy\Html\Label("errormessage", new PropertyBinding($this, '_errormsg')))->setVisible(false);
    }

    //вывод ошибки,  используется   в дочерних страницах
    final protected function setError($msg)
    {
        $this->_errormsg = $msg;
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
    final protected function isError()
    {
        return strlen($this->_errormsg) > 0;
    }

    protected function beforeRender()
    {
        $this->errormessage->setVisible(strlen($this->_errormsg) > 0);
    }
    protected function afterRender()
    {
        $this->errormessage->setVisible(false);
        $this->warnmessage->setVisible(false);
        $this->successmessage->setVisible(false);
    }

}
