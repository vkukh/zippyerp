<?php

namespace App\Entity;

/**
 * Клас-сущность  сотрудник
 *
 * @table=employees
 * @keyfield=employee_id
 */
class Employee extends \ZCL\DB\Entity
{

    protected function init() {
        $this->employee_id = 0;
    }

    protected function beforeDelete() {
        $cnt = Entry::findCnt('employee_id=' . $this->employee_id);
        if ($cnt > 0)
            return "Сотрудник уже  используется в документах или проводках";
//        $cnt=CAsset::findCnt("detail like '%<emp_id>{$this->employee_id}</emp_id>%'");
//        if($cnt>0)return false;

        return "";
    }

    protected function beforeSave() {
        parent::beforeSave();
        //упаковываем  данные в detail
        $this->detail = "<detail><login>{$this->login}</login>";
        $this->detail .= "<email>{$this->email}</email>";
        $this->detail .= "<phone>{$this->phone}</phone>";
        $this->detail .= "<comment>{$this->comment}</comment>";
        $this->detail .= "<firedate>{$this->firedate}</firedate>";
        $this->detail .= "<hiredate>{$this->hiredate}</hiredate>";
        $this->detail .= "<invalid>{$this->invalid}</invalid>";
        $this->detail .= "<combined>{$this->combined}</combined>";
        $this->detail .= "<inn>{$this->inn}</inn>";
        $this->detail .= "<salary>{$this->salary}</salary>";
        $this->detail .= "<avans>{$this->avans}</avans>";
        $this->detail .= "<expense>{$this->expense}</expense>";
        $this->detail .= "<stype>{$this->stype}</stype>";
        $this->detail .= "<fired>{$this->fired}</fired>";

        $this->detail .= "</detail>";

        return true;
    }

    protected function afterLoad() {
        //распаковываем  данные из detail
        $xml = simplexml_load_string($this->detail);
        $this->login = (string) ($xml->login[0]);
        $this->email = (string) ($xml->email[0]);
        $this->phone = (string) ($xml->phone[0]);
        $this->comment = (string) ($xml->comment[0]);
        $this->firedate = (int) ($xml->firedate[0]);
        $this->hiredate = (int) ($xml->hiredate[0]);
        $this->invalid = (int) ($xml->invalid[0]);
        $this->combined = (int) ($xml->combined[0]);
        $this->stype = (int) ($xml->stype[0]);
        $this->expense = (int) ($xml->expense[0]);
        $this->salary = (int) ($xml->salary[0]);
        $this->avans = (int) ($xml->avans[0]);
        $this->fired = (int) ($xml->fired[0]);
        $this->inn = (string) ($xml->inn[0]);


        parent::afterLoad();
    }

    //найти  по  логину
    public static function getByLogin($login) {
        if (strlen($login) == 0)
            return null;
        $login = Employee::qstr($login);
        return Employee::getFirst("login=" . $login);
    }

    /**
     * Возвращает начисленое к  оплате
     *
     * @param mixed $date
     */
    public function getForPayed($date) {
        return Entry::getAmount($date, '66', 0, 0, $this->employee_id, 0);
    }

}
