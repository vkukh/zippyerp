<?php

namespace ZippyERP\System\Pages;

use Zippy\Html\Label as Label;
use ZippyERP\System\Role;
use ZippyERP\System\System;

class UserInfo extends \ZippyERP\System\Pages\Bases
{

    private $user;

    public function __construct($user_id)
    {
        parent::__construct();

        $this->user = \ZippyERP\System\User::load($user_id);
        $this->add(new Label('login', $this->user->userlogin));
        $this->add(new Label('createdate', date('Y-m-d', $this->user->registration_date)));
        $this->add(new Label('userroles', 'Зарегистрировнный пользователь'));

        $this->add(new \Zippy\Html\Form\Form('roleform'))->setVisible(System::getUser()->userlogin == 'admin');
        $this->roleform->add(new \Zippy\Html\DataList\DataView("rolerow", new \ZCL\DB\EntityDataSource('\ZippyERP\System\Role'), $this, 'OnAddRoleRow'))->Reload();
        $this->roleform->setSubmitHandler($this, 'OnSubmit');
    }

    //вывод строки таблицы  ролей
    public function OnAddRoleRow(\Zippy\Html\DataList\DataRow $datarow)
    {
        $item = $datarow->getDataItem();
        $datarow->add(new \Zippy\Html\Label("description", $item->description));
        $datarow->add(new \Zippy\Html\Form\CheckBox("role", new \Zippy\Binding\PropertyBinding($item, 'tag')))->setChecked($this->user->hasRole($item->rolename));
    }

    //запись  ролей для  юзера
    public function OnSubmit($sender)
    {
        $roles = array();
        $rows = $this->roleform->rolerow->getDataRows();
        foreach ($rows as $row) {
            $ch = $row->getChildElement('role');
            if ($ch->isChecked()) {
                $roles[] = $row->getDataItem()->role_id;
            }
        }

        \ZippyERP\System\Helper::saveRoles($this->user->user_id, $roles);
    }

    public function beforeRender()
    {
        parent::beforeRender();
        $roles = array();
        $roles[] = 'Пользователь';

        $rolelist = Role::find('role_id  in (select role_id from system_user_role where user_id  = ' . $this->user->user_id . ')');
        if (count($rolelist) > 0) {
            foreach ($rolelist as $role) {
                $roles[] = $role->description;
            }
        }
        $this->userroles->setText(implode('<br>', $roles), true);
    }

}
