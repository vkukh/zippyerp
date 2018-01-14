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
        $users = \ZippyERP\System\User::find("   acl like '%<erpacl>2</erpacl>%' " );

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

    /**
    * Проверка права  на чтение
    * 
    * @param mixed $meta_name  имя класса
    * @param int $meta_type  тип метаданных
    * @param int $id
    */
    public static function checkRead($meta_name,$meta_type,$id=0)  {
       $user = \ZippyERP\System\System::getUser(); 
       return true;
    }   
    /**
    * Проверка права  на выполнение
    * 
    * @param mixed $meta_name  имя класса
    * @param int $meta_type  тип метаданных
    * @param int $id
    */
    public static function checkExec($meta_name,$meta_type,$id=0)  {    
       $user = \ZippyERP\System\System::getUser(); 
       return true;
    }   
    /**
    * Проверка права  на редактирование
    * 
    * @param mixed $meta_name  имя класса
    * @param int $meta_type  тип метаданных
    * @param int $id
    */
    public static function checkWrite($meta_name,$meta_type,$id=0)   {   
       $user = \ZippyERP\System\System::getUser(); 
       $conn = \ZDB\DB::getConnect();
       $r= $conn->GetOne("select count(*) from erp_metadata_access_view where editacc=1 and  meta_name ={$meta_name} and meta_type ={$meta_type} and user_id={$$user->user_id}");
   
 
       return true;
    }   
    
    /**
    * Условие для списка недоступных для списка документов в erp_document
    * 
    */
    public static function getWhere()   {   
       $user = \ZippyERP\System\System::getUser(); 
       if($user->erpacl == 1){
          return "1=1";    
       }
       if($user->erpacl == 2){   //прописано в  метаданных
          return " type_id not  in(select metadata_id from erp_metadata_access_view where meta_type=1 and  viewacc= 1  and user_id={$user_id} ) ";    
       }
       if($user->erpacl == 3){   //только  свои
          return " user_id ={$user_id}  ";    
       }
       
    }      
    
    
    
}
