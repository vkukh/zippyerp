<?php

namespace ZippyERP\ERP\Entity;

/**
 * Клас-сущность  группа  ТМЦ
 * 
 * @table=erp_item_group
 * @keyfield=group_id
 */
class GroupItem extends \ZCL\DB\Entity
{

    protected function beforeDelete()
    {
        $conn = \ZCL\DB\DB::getConnect();
        $conn->Execute("update erp_item set  group_id = 0 ");

        return true;
    }

    /**
     * Возвращает  список  для   комбо
     * 
     */
    public static function getList()
    {
        return GroupItem::findArray('group_name', '', 'group_name');
    }

}
