<?php

namespace ZippyERP\System;

/**
 * Класс,  инкапсулирующий  роль  юзера   в   системме
 */
class Role extends \ZCL\DB\Entity
{

    protected function init()
    {
        $this->role_id = 0;
    }

    protected static function getMetadata()
    {
        return array('table' => 'system_roles', 'keyfield' => 'role_id');
    }

}
