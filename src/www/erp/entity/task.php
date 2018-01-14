<?php

namespace ZippyERP\ERP\Entity;

/**
 * Клас-сущность  задача
 *
 * @view=erp_task_task_view
 * @table=erp_task_task
 * @keyfield=task_id
 */
class Task extends \ZCL\DB\Entity
{

    protected function afterLoad()
    {
        $this->start_date = strtotime($this->start_date);
        $this->end_date = strtotime($this->end_date);
        $this->updated = strtotime($this->updated);
    }

    protected function beforeDelete()
    {
        //$conn = \ZDB\DB::getConnect();
        // $conn->Execute("delete from erp_document_update_log  where document_id =" . $this->document_id);

        return true;
    }

    //список возможных  состоянийй задачи
    public static function getStatusList()
    {
        $list = array();
        $list[0] = "Нове";
        $list[1] = "Виконуєтся";
        $list[2] = "Закінчене";
        $list[3] = "Закрити";
        $list[4] = "Перевірка";
        $list[5] = "Виправляєтся";
        return $list;
    }

    //список приоритетов
    public static function getPriorityList()
    {
        $list = array();
        $list[0] = "Высокий";
        $list[3] = "Нормальный";
        $list[5] = "Низкий";
        return $list;
    }

    //список    исполнителей
    public   function getAssignedList()
    {

        // return \ZippyERP\System\User::findArray('userlogin');
        return Employee::find("  employee_id  in (select employee_id from erp_task_task_emp where task_id={$this->task_id})");
    }
    public   function updateAssignedList($list)
    {

         $conn = \ZDB\DB::getConnect();
         $conn->Execute("delete from erp_task_task_emp  where task_id =" . $this->task_id);
         foreach($list as $emp){
            $conn->Execute("insert into erp_task_task_emp (task_id,employee_id) values({$this->task_id},{$emp->employee_id})");    
         }
   }

   //запись статуса  в  историю
   public function addStatus($date,$user){
       $conn = \ZDB\DB::getConnect();
       $conn->Execute("insert into erp_task_sh (task_id,username,status,sdate) values({$this->task_id},".$conn->qstr($user).",{$this->status},". $conn->DBTimeStamp($date) .")"   );
   
   }
   
   public function getStatusHistory(){
       $conn = \ZDB\DB::getConnect();
       $list = array();
       $rs = $conn->Execute("select * from erp_task_sh where task_id = {$this->task_id} order by  sdate " );
       foreach($rs as $row){
           $list[] = new \ZippyERP\ERP\DataItem($row);
       }
       return $list;
   }
   
}
