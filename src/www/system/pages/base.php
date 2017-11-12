<?php

namespace ZippyERP\System\Pages;

use Zippy\Binding\PropertyBinding;
use Zippy\Html\Label;
use Zippy\Html\Panel;
use Zippy\Html\Link\ClickLink;
use ZippyERP\ERP\Helper;
use Zippy\WebApplication as App;
use ZippyERP\System\System;
use ZippyERP\System\User;

class Base extends \Zippy\Html\WebPage
{

    private $_errormsg;
    private $_successmsg;
    private $_warnmsg;
    private $_infomsg;
    protected $_module;

    public function __construct($params = null)
    {

           
        \Zippy\Html\WebPage::__construct();
        
 
      
      
        $user = System::getUser();
       
       
            $this->_tvars["islogined"] = $user->user_id > 0 ;
               
            $this->_tvars["isadmin"] = $user->userlogin == 'admin';
            $this->_tvars["isconman"] = $user->userlogin == 'admin' || $user->shopcontent >0;         
            $this->_tvars["isorderman"] = $user->userlogin == 'admin' || $user->shoporders >0;         
        
            $this->add(new ClickLink('logout', $this, 'LogoutClick'));
            $this->add(new Label('username', $user->userlogin));            
       
        
   
    }

    public function LogoutClick($sender)
    {
        setcookie("remember", '', 0);
        System::setUser(new \ZippyERP\System\User());
        $_SESSION['user_id'] = 0;
        $_SESSION['userlogin'] = 'Гость';
        $this->_tvars["islogined"] = false;
        App::RedirectHome();
    }

    /**
    * Вывод информации о  странице
    * 
    */
    public function getPageInfo()
    {
        return '';
    }

    //вывод ошибки,  используется   в дочерних страницах
   public function setError($msg) {


        $this->_errormsg = $msg;
    }

    public function setSuccess($msg) {
        $this->_successmsg = $msg;
    }

    public function setWarn($msg) {
        $this->_warnmsg = $msg;
    }

    public function setInfo($msg) {
        $this->_infomsg = $msg;
    }

    final protected function isError()
    {
        return strlen($this->_errormsg) > 0;
    }

    protected function beforeRender()
    {
         
    }

    protected function afterRender()
    {
        if (strlen($this->_errormsg) > 0)
            App::$app->getResponse()->addJavaScript("toastr.error('{$this->_errormsg}')        ", true);
        if (strlen($this->_warnmsg) > 0)
            App::$app->getResponse()->addJavaScript("toastr.warning('{$this->_warnmsg}')        ", true);
        if (strlen($this->_successmsg) > 0)
            App::$app->getResponse()->addJavaScript("toastr.success('{$this->_successmsg}')        ", true);
        if (strlen($this->_infomsg) > 0)
            App::$app->getResponse()->addJavaScript("toastr.info('{$this->_infomsg}')        ", true);


        $this->setError('');
        $this->setSuccess('');

        $this->setInfo('');
        $this->setWarn('');
    }

}
