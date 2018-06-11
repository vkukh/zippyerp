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
 
    protected $_module;

    public function __construct($params = null) {


        \Zippy\Html\WebPage::__construct();




        $user = System::getUser();


        $this->_tvars["islogined"] = $user->user_id > 0;

        $this->_tvars["isadmin"] = $user->userlogin == 'admin';
        $this->_tvars["isconman"] = $user->userlogin == 'admin' || $user->shopcontent > 0;
        $this->_tvars["isorderman"] = $user->userlogin == 'admin' || $user->shoporders > 0;

        $this->add(new ClickLink('logout', $this, 'LogoutClick'));
        $this->add(new Label('username', $user->userlogin));
    }

    public function LogoutClick($sender) {
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
    public function getPageInfo() {
        return '';
    }

    //вывод ошибки,  используется   в дочерних страницах
 
    public function setError($msg) {
        System::setErrorMsg($msg) ;
    }

    public function setSuccess($msg) {
        System::setSuccesMsg($msg) ;
    }

    public function setWarn($msg) {
        System::setWarnMsg($msg) ;
    }

    public function setInfo($msg) {
         System::setInfoMsg($msg) ;
    }

    final protected function isError() {
        return strlen(System::getErrorMsg()) > 0;
    }

    

    protected function beforeRender() {
        
    }

    protected function afterRender() {
        if (strlen(System::getErrorMsg()) > 0)
            App::$app->getResponse()->addJavaScript("toastr.error('".System::getErrorMsg()."')        ", true);
        if (strlen(System::getWarnMsg()) > 0)
            App::$app->getResponse()->addJavaScript("toastr.warning('".System::getWarnMsg()."')        ", true);
        if (strlen(System::getSuccesMsg()) > 0)
            App::$app->getResponse()->addJavaScript("toastr.success('".System::getSuccesMsg()."')        ", true);        
        if (strlen(System::getInfoMsg()) > 0)
            App::$app->getResponse()->addJavaScript("toastr.info('".System::getInfoMsg()."')        ", true);



        $this->setError('');
        $this->setSuccess('');

        $this->setInfo('');
        $this->setWarn('');
    }

}
