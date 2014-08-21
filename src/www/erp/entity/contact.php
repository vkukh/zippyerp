<?php

namespace ZippyERP\ERP\Entity;

/**
 * Клас-сущность  контакт
 * 
 * @table=erp_contact
 * @keyfield=contact_id
 */
class Contact extends \ZCL\DB\Entity
{

    public $address, $url, $phone, $position;

    protected function beforeSave()
    {
        parent::beforeSave();
        //упаковываем  данные в detail
        $this->detail = "<detail><address>{$this->address}</address>";
        $this->detail .= "<url>{$this->url}</url>";
        $this->detail .= "<position>{$this->position}</position>";
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
            $this->position = (string) ($xml->position[0]);
        }
        parent::afterLoad();
    }

    //Возвращает  фамилию  и  инициалы
    public function getShortName()
    {
        return $this->lastname . " " . $this->firstname;
    }

}
