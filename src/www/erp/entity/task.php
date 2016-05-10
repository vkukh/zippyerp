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
        $conn = \ZDB\DB\DB::getConnect();
        // $conn->Execute("delete from erp_document_update_log  where document_id =" . $this->document_id);

        return true;
    }

    //список возможных  состоянийй задачи
    public static function getStatusList()
    {
        $list = array();
        $list[0] = "Новая";
        $list[1] = "В работе";
        $list[2] = "Закончена";
        $list[3] = "Закрыта";
        $list[4] = "Проверка";
        $list[5] = "На  исправлении";
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

    //список возможных  исполнителей
    public static function getAssignedList()
    {

        // return \ZippyERP\System\User::findArray('userlogin');
        return \ZippyERP\ERP\Entity\Employee::findArray('shortname');
    }

}
