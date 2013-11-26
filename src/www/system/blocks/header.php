<?php

namespace ZippyERP\System\Blocks;

use \ZippyERP\System\Application as App;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\CheckBox;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Binding\PropertyBinding as Bind;
use \ZippyERP\System\System;
use \ZippyERP\System\User;

class Header extends \Zippy\Html\PageFragment
{

        public $_login;
        public $_password;

        public function __construct($id)
        {
                parent::__construct($id);



                $form = new \Zippy\Html\Panel('welcomform');
                $form->add(new Label('username'));
                $form->add(new ClickLink('logout', $this, 'LogoutClick'));
                $this->add($form);

                $form->add(new Label('adminmenu', ''));

                if ($_COOKIE['remember'] && System::getUser()->user_id == 0) {
                        $arr = explode('_', $_COOKIE['remember']);
                        $_config = parse_ini_file(_ROOT . 'config/config.ini', true);
                        if ($arr[0] > 0 && $arr[1] === md5($_SERVER['REMOTE_ADDR'] . $_config['common']['salt'])) {
                                $user = User::load($arr[0]);
                        }

                        if ($user instanceof User) {


                                System::setUser($user);

                                $_SESSION['user_id'] = $user->user_id; //для  использования  вне  Application
                                $_SESSION['userlogin'] = $user->userlogin; //для  использования  вне  Application
                                //   @mkdir(_ROOT . UPLOAD_USERS .$user->user_id) ;
                                //  \ZippyERP\System\Util::removeDirRec(_ROOT . UPLOAD_USERS .$user->user_id.'/tmp') ;
                                //   @mkdir(_ROOT .UPLOAD_USERS .$user->user_id .'/tmp') ; 
                        }
                }
        }

        protected function beforeRender()
        {
                parent::beforeRender();
                $user = System::getUser();

                $this->welcomform->SetVisible($user->isLogined());
                $this->welcomform->username->setText($user->userlogin);
                $this->welcomform->adminmenu->SetVisible($user->userlogin == 'admin');
        }

        public function LogoutClick($sender)
        {
                setcookie("remember", '', 0);
                System::setUser(new \ZippyERP\System\User());
                $_SESSION['user_id'] = 0;
                $_SESSION['userlogin'] = 'Guest';

                //$page = $this->getOwnerPage();
                //  $page = get_class($page)  ;
                App::RedirectHome();
                ;
                ;
                //    App::$app->getresponse()->toBack();
        }

}