<?php

namespace ZippyERP\ERP\Entity;

/**
 * Клас-сущность  коментари
 *
 * @view=erp_message_view
 * @table=erp_message
 * @keyfield=message_id
 */
class Message extends \ZCL\DB\Entity
{

    protected function afterLoad() {
        $this->created = strtotime($this->created);
    }

}
