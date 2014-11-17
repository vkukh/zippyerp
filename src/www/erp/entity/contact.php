<?php

namespace ZippyERP\ERP\Entity;

/**
 * Клас-сущность  контакт
 * 
 * @table=erp_contact
 * @view=erp_contact_view
 * @keyfield=contact_id
 */
class Contact extends \ZCL\DB\Entity
{

    public $address, $url, $phone, $position;

    protected function init()
    {
        $this->contact_id = 0;
    }

    protected function beforeSave()
    {
        parent::beforeSave();
        //упаковываем  данные в detail
        $this->detail = "<detail><address>{$this->address}</address>";
        $this->detail .= "<url>{$this->url}</url>";
        $this->detail .= "<phone>{$this->phone}</phone>";
        $this->detail .= "</detail>";

        return true;
    }

    protected function afterLoad()
    {
        if (strlen($this->detail) > 0) {
            //распаковываем  данные из detail
            $xml = simplexml_load_string($this->detail);
            $this->url = (string) ($xml->url[0]);
            $this->address = (string) ($xml->address[0]);
            $this->phone = (string) ($xml->phone[0]);
        }
        parent::afterLoad();
    }

    //Возвращает  фамилию  и  имя
    public function getShortName()
    {
        return $this->lastname . " " . $this->firstname;
    }

    //Возвращает  фамилию  и  инициалы
    public function getInitName()
    {
        $name = $this->lastname . " " . mb_substr($this->firstname, 0, 1, "UTF-8") . '.';
        if (strlen($this->middlename) > 0) {
            $name = $name . " " . mb_substr($this->middlename, 0, 1, "UTF-8") . '.';
        }
        return $name;
    }

    /**
     * Возвращает сущности  связанные   с  контактом (сотрудник,   контрагент  и т.д.)
     * 
     */
    public function getType()
    {
        $type = "";
        if ($this->employee > 0)
            $type = "Сотрудник";
        if ($this->customer > 0)
            $type = "Контрагент '{$this->customer_name}'";
        return $type;
    }

}
