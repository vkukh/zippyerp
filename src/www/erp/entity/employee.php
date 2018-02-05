<?php

namespace ZippyERP\ERP\Entity;

/**
 * Клас-сущность  сотрудник
 *
 * @table=erp_staff_employee
 * @view=erp_staff_employee_view
 * @keyfield=employee_id
 */
class Employee extends \ZCL\DB\Entity
{

    const TAX_ACCOUNTABLE = 1; // подотчет

    protected function init() {
        $this->employee_id = 0;
        $this->hiredate = time();
    }

    protected function beforeSave() {
        parent::beforeSave();
        //упаковываем  данные в detail
        $this->detail = "<detail><login>{$this->login}</login>";
        $this->detail .= "<salarytype>{$this->salarytype}</salarytype>";
        $this->detail .= "<exptype>{$this->exptype}</exptype>";
        $this->detail .= "<salary>{$this->salary}</salary>";
        $this->detail .= "<inn>{$this->inn}</inn>";
        $this->detail .= "<avans>{$this->avans}</avans>";
        $this->detail .= "<combined>{$this->combined}</combined>";
        $this->detail .= "<invalid>{$this->invalid}</invalid>";
        $this->detail .= "</detail>";

        return true;
    }

    protected function afterLoad() {
        $this->hiredate = strtotime($this->hiredate);
        if (strlen($this->firedate) > 0)
            $this->firedate = strtotime($this->firedate);

        //распаковываем  данные из detail
        $xml = simplexml_load_string($this->detail);
        $this->login = (string) ($xml->login[0]);
        $this->salarytype = (int) ($xml->salarytype[0]);
        $this->exptype = (int) ($xml->exptype[0]);
        $this->salary = (int) ($xml->salary[0]);
        $this->inn = (string) ($xml->inn[0]);
        $this->avans = (int) ($xml->avans[0]);
        $this->combined = (int) ($xml->combined[0]);
        $this->invalid = (int) ($xml->invalid[0]);


        parent::afterLoad();
    }

    //Возвращает  фамилию  и  имя
    public function getShortName() {
        return $this->lastname . " " . $this->firstname;
    }

    //Возвращает  фамилию  и  инициалы
    public function getInitName() {
        $name = $this->lastname . " " . mb_substr($this->firstname, 0, 1, "UTF-8") . '.';
        if (strlen($this->middlename) > 0) {
            $name = $name . " " . mb_substr($this->middlename, 0, 1, "UTF-8") . '.';
        }
        return $name;
    }

    /**
     * Возвращает начисленое к  оплате
     *
     * @param mixed $date
     */
    public function getForPayed($date) {
        return SubConto::getAmount($date, 0, 0, 0, $this->employee_id, 0, 0, 0);
    }

    /**
     * возвращает контакт, связанный с  сотрудником
     *
     */
    public function getContact() {
        return Contact::load($this->contact);
    }

}
