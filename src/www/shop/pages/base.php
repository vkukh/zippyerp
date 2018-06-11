<?php

namespace ZippyERP\Shop\Pages;

use \Zippy\Binding\PropertyBinding;
use \Zippy\Html\Label;
use \Zippy\Html\Panel;
use \Zippy\Html\Link\ClickLink;
use \ZippyERP\ERP\Helper;
use \Zippy\WebApplication as App;
use \ZippyERP\System\System;
use \ZippyERP\System\User;

class Base extends \Zippy\Html\WebPage
{

 
    public $_js;

    public function __construct($params = null) {

        \Zippy\Html\WebPage::__construct();

        $user = System::getUser();

        $this->add(new ClickLink('logout', $this, 'LogoutClick'));
        $this->add(new \Zippy\Html\Link\BookmarkableLink('shopcart', "/?p=/ZippyERP/Shop/Pages/Order"));
        $this->add(new Label('username', $user->userlogin));

        $this->_tvars["islogined"] = $user->user_id > 0;
        $this->_tvars["isadmin"] = $user->userlogin == 'admin';
        $this->_tvars["isconman"] = $user->userlogin == 'admin' || $user->shopcontent > 0;
        $this->_tvars["isorderman"] = $user->userlogin == 'admin' || $user->shoporders > 0;
        
        $this->op = System::getOptions("shop");
        if (!is_array($this->op))
            $this->op = array();
        
        if(strlen($this->op['aboutus']) > 10){
           $this->_tvars["aboutus"] = $this->op['aboutus'];
        }            
        
    }

    public function LogoutClick($sender) {
        setcookie("remember", '', 0);
        System::setUser(new \ZippyERP\System\User());
        $_SESSION['user_id'] = 0;
        $_SESSION['userlogin'] = 'Гость';

        //$page = $this->getOwnerPage();
        //  $page = get_class($page)  ;
        App::Redirect("\\ZippyERP\\System\\Pages\\UserLogin");

        //    App::$app->getresponse()->toBack();
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

    public function getPageInfo() {
        return '';
    }

    public function addJS($js) {
        $this->_js = $js;
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

        if (strlen($this->_js) > 0) {
            App::$app->getResponse()->addJavaScript($this->_js, true);
            $this->_js = "";
        }
    }

    protected function beforeRender() {
        parent::beforeRender();
        $this->shopcart->setVisible(\ZippyERP\Shop\Basket::getBasket()->isEmpty() == FALSE);
    }

    //Перезагрузить страницу  с  клиента
    //например бля  сброса  адресной строки  после  команды удаления
    public function resetURL(){
         \Zippy\WebApplication::$app->setReloadPage();
    }    
}
