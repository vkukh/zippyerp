<?php

namespace ZippyERP\System\Pages;

use \Zippy\Binding\PropertyBinding;

class Base extends \Zippy\Html\WebPage
{

    public $_errormsg;

    public function __construct($params = null)
    {
        \Zippy\Html\WebPage::__construct();
        $this->add(new \ZippyERP\System\Blocks\Header("header"));

        $this->add(new \Zippy\Html\Label("errormessage", new PropertyBinding($this, '_errormsg')))->setVisible(false);
    }

    //вывод ошибки,  используется   в дочерних страницах
    final protected function setError($msg)
    {
        $this->_errormsg = $msg;
    }

    final protected function isError()
    {
        return strlen($this->_errormsg) > 0;
    }

    protected function beforeRender()
    {
        $this->errormessage->setVisible(strlen($this->_errormsg) > 0);
    }

}
