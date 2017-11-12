<?php

namespace ZippyERP\ERP;

/**
 * Класс  для  упрвления доступом к метаобьектам
 */
class ACL
{

    /**
     * Возвращает  роли  с  правами  доступа   к обьекту
     */
    public static function getRoleAccess($meta_id)
    {
        $conn = \ZDB\DB::getConnect();
        $users = \ZippyERP\System\User::find("acl like '%<erpacl>3</erpacl>%' or acl like '%<erpacl>2</erpacl>%' " );

        foreach (array_keys($users) as $user_id) {

            $row = $conn->GetRow("select * from erp_metadata_access where metadata_id ={$meta_id} and user_id={$user_id}");
            if (is_array($row)) {
                $users[$user_id]->viewacc = $row['viewacc'];
                $users[$user_id]->editacc = $row['editacc'];
                $users[$user_id]->execacc = $row['execacc'];
            }
        }

        return $users;
    }

    public static function updateRoleAccess($meta_id, $rows)
    {
        $conn = \ZDB\DB::getConnect();
        $conn->Execute("delete from erp_metadata_access where metadata_id ={$meta_id} ");

        foreach ($rows as $row) {
            $item = $row->getDataItem();
            $conn->Execute("insert  into erp_metadata_access (metadata_id,user_id,viewacc,editacc,execacc) values ({$meta_id},{$item->user_id}," . ($item->viewacc ? 1 : 0) . "," . ($item->editacc ? 1 : 0) . "," . ($item->execacc ? 1 : 0) . ") ");
        }
    }

}
