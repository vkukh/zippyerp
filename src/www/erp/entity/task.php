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

    const TASK_STATUS_NEW = 0;
    const TASK_STATUS_EXECUTED = 1;
    const TASK_STATUS_FINISHED = 2;
    const TASK_STATUS_CLOSED = 3;
    const TASK_STATUS_CHECKED = 4;
    const TASK_STATUS_FIXED = 5;
    const TASK_PRIORITY_HIGH = 1;
    const TASK_PRIORITY_NORMAL = 3;
    const TASK_PRIORITY_LOW = 5;

    private $items = array();

    protected function init() {
        $this->prod = 0;
    }

    protected function afterLoad() {
        $this->start_date = strtotime($this->start_date);
        $this->end_date = strtotime($this->end_date);
        $this->updated = strtotime($this->updated);

        $xml = @simplexml_load_string($this->details);
        $this->retissue = (int) ($xml->retissue[0]);
        foreach ($xml->items->children() as $item) {

            $stock = Stock::load((int) $item->id);
            $stock->qty = (int) $item->qty;
            $stock->price = (int) $item->price;
            $this->items[$stock->stock_id] = $stock;
        }

        parent::afterLoad();
    }

    protected function beforeSave() {
        parent::beforeSave();
        //упаковываем  данные в detail
        $this->details = "<detail>";
        $this->details .= "<items>";
        foreach ($this->items as $item) {
            $this->details .= "<item><id>{$item->stock_id}</id><qty>{$item->qty}</qty><price>{$item->price}</price></item>";
        }
        $this->details .= "</items>";
        $this->details .= "<prod>{$this->prod}</prod>";
        $this->details .= "</detail>";

        return true;
    }

    protected function beforeDelete() {
        //$conn = \ZDB\DB::getConnect();
        // $conn->Execute("delete from erp_document_update_log  where document_id =" . $this->document_id);

        return true;
    }

    //список возможных  состояний  задачи
    public static function getStatusList($status = -1) {
        $list = array();
        if ($status < 1)
            $list[Task::TASK_STATUS_NEW] = "Нове";
        $list[Task::TASK_STATUS_EXECUTED] = "Виконуєтся";
        $list[Task::TASK_STATUS_FINISHED] = "Закінчене";
        $list[Task::TASK_STATUS_CLOSED] = "Закрите";
        $list[Task::TASK_STATUS_CHECKED] = "Перевірка";
        $list[Task::TASK_STATUS_FIXED] = "Виправляєтся";
        return $list;
    }

    //список приоритетов
    public static function getPriorityList() {
        $list = array();
        $list[Task::TASK_PRIORITY_HIGH] = "Високий";
        $list[Task::TASK_PRIORITY_NORMAL] = "Нормальний";
        $list[Task::TASK_PRIORITY_LOW] = "Низький";
        return $list;
    }

    //список    исполнителей
    public function getAssignedList() {

        // return \ZippyERP\System\User::findArray('userlogin');
        return Employee::find("  employee_id  in (select employee_id from erp_task_task_emp where task_id={$this->task_id})");
    }

    //список  ТМЦ
    public function getItemsList() {
        return $this->items;
    }

    public function updateItemsList($items) {
        return $this->items = $items;
    }

    public function updateAssignedList($list) {

        $conn = \ZDB\DB::getConnect();
        $conn->Execute("delete from erp_task_task_emp  where task_id =" . $this->task_id);
        foreach ($list as $emp) {
            $conn->Execute("insert into erp_task_task_emp (task_id,employee_id) values({$this->task_id},{$emp->employee_id})");
        }
    }

    //запись статуса  в  историю
    public function addStatus($date, $user) {
        $conn = \ZDB\DB::getConnect();
        $conn->Execute("insert into erp_task_sh (task_id,username,status,sdate) values({$this->task_id}," . $conn->qstr($user) . ",{$this->status}," . $conn->DBTimeStamp($date) . ")");
    }

    public function getStatusHistory() {
        $conn = \ZDB\DB::getConnect();
        $list = array();
        $rs = $conn->Execute("select * from erp_task_sh where task_id = {$this->task_id} order by  sdate ");
        foreach ($rs as $row) {
            $list[] = new \ZippyERP\ERP\DataItem($row);
        }
        return $list;
    }

}
