<?php

namespace ZippyERP\System;

class UserMessage extends \ZCL\DB\Entity
{

    protected function init()
    {
        $this->createdon = time();
        $this->readed = 0;
    }

    protected static function getMetadata()
    {
        return array('table' => 'system_user_messages', 'view' => 'system_user_messages_view', 'keyfield' => 'message_id');
    }

    protected function afterLoad()
    {
        $this->createdon = strtotime($this->createdon);
    }

}
