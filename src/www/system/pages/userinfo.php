<?php

namespace ZippyERP\System\Pages;

use Zippy\Html\Label as Label;
use ZippyERP\System\Role;
use ZippyERP\System\System;

class UserInfo extends \ZippyERP\System\Pages\Base
{

    private $user;

    public function __construct($user_id)
    {
        parent::__construct();
      
        $user = System::getUser();
        if ($user->user_id == 0) {
            App::Redirect("\\ZippyERP\\System\\Pages\\Userlogin");
        }
      
        $this->user = \ZippyERP\System\User::load($user_id);
        $this->add(new Label('login', $this->user->userlogin));
        $this->add(new Label('createdate', date('Y-m-d', $this->user->registration_date)));
        $this->add(new Label('email', $this->user->email));
     

    }

 

 

}
