<?php

namespace ZippyERP\ERP\Pages;

use \ZippyERP\ERP\Helper;
use \ZippyERP\System\System;
use \ZippyERP\System\Application as App;
use \Zippy\Html\Label;

//базовая страница  
class Base extends \Zippy\Html\WebPage
{

        public $_errormsg;

        public function __construct()
        {

                $this->add(new \ZippyERP\System\Blocks\Header("header"));
                $this->add(new Label("errormessage", new \Zippy\Binding\PropertyBinding($this, '_errormsg'), false, true))->setVisible(false);
                $this->add(new Label("menudoc", Helper::generateMenu(1), true));
                $this->add(new Label("menurep", Helper::generateMenu(2), true));
                $this->add(new Label("menureg", Helper::generateMenu(3), true));
                $this->add(new Label("menuref", Helper::generateMenu(4), true));

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

