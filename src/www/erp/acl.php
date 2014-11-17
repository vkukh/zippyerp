<?php

namespace ZippyERP\ERP;

use \ZCL\DB\DB;

/**
 * Класс  для  упрвления доступом к метаобьектам
 */
class ACL
{

    /**
     * Возвращает  роли  с  правами  доступа   к  
     */
    public static function getRoleAccess($meta_id)
    {
        $conn = \ZCL\DB\DB::getConnect();
        $roles = \ZippyERP\System\Role::find();

        foreach (array_keys($roles) as $role_id) {

            $row = $conn->GetRow("select * from erp_metadata_access where metadata_id ={$meta_id} and role_id={$role_id}");
            if (is_array($row)) {
                $roles[$role_id]->viewacc = $row['viewacc'];
                $roles[$role_id]->editacc = $row['editacc'];
                $roles[$role_id]->deleteacc = $row['deleteacc'];
                $roles[$role_id]->execacc = $row['execacc'];
            }
        }

        return $roles;
    }

    public static function updateRoleAccess($meta_id, $rows)
    {
        $conn = \ZCL\DB\DB::getConnect();
        $conn->Execute("delete from erp_metadata_access where metadata_id ={$meta_id} ");

        foreach ($rows as $row) {
            $item = $row->getDataItem();
            $conn->Execute("insert  into erp_metadata_access (metadata_id,role_id,viewacc,editacc,deleteacc,execacc) values ({$meta_id},{$item->role_id}," . ($item->viewacc ? 1 : 0) . "," . ($item->editacc ? 1 : 0) . "," . ($item->deleteacc ? 1 : 0) . "," . ($item->execacc ? 1 : 0) . ") ");
        }
    }

}
