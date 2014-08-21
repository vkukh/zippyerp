<?php

namespace ZippyERP\ERP\Entity;

/**
 * Класс-сущность  контрагент
 * 
 * @table=erp_customer
 * @view=erp_customer_view
 * @keyfield=customer_id
 */
class Customer extends \ZCL\DB\Entity
{

    public $code, $city, $zip, $address, $url, $phone;

    protected function beforeSave()
    {
        parent::beforeSave();
        //упаковываем  данные в detail
        $this->detail = "<detail><code>{$this->code}</code>";
        $this->detail .= "<inn>{$this->inn}</inn>";
        $this->detail .= "<city>{$this->city}</city>";
        $this->detail .= "<street>{$this->street}</street>";
        $this->detail .= "<bankaccount1><bank>{$this->bank}</bank><account>{$this->bankaccount}</account></bankaccount1>";
        $this->detail .= "</detail>";

        return true;
    }

    protected function afterLoad()
    {
        //распаковываем  данные из detail
        $xml = simplexml_load_string($this->detail);
        $this->code = (string) ($xml->code[0]);
        $this->inn = (string) ($xml->inn[0]);
        $this->city = (string) ($xml->city[0]);
        $this->street = (string) ($xml->street[0]);
        $this->bank = (string) ($xml->bankaccount1->bank[0]);
        $this->bankaccount = (string) ($xml->bankaccount1->account[0]);

        parent::afterLoad();
    }

    public static function AddActivity($customer_id, $amount, $document_id)
    {
        $conn = \ZCL\DB\DB::getConnect();
        $conn->Execute("insert into erp_customer_activity (customer_id,document_id,amount) values ({$customer_id},{$document_id},{$amount})");
    }

    public static function getActivityList($customer_id)
    {
        $conn = \ZCL\DB\DB::getConnect();
        $rs = $conn->Execute("select a.activity_id,a.amount,d.document_date,m.description,d.document_number,d.document_id from  erp_customer_activity a join erp_document d on a.document_id = d.document_id  join erp_metadata m on d.type_id = m.meta_id and m.meta_type =1 order  by  d.document_date  desc");
        $list = array();
        foreach ($rs as $row) {
            $item = new \ZippyERP\ERP\DataItem();
            $item->id = $row['activity_id'];
            $item->document_id = $row['document_id'];
            $item->amount = $row['amount'];
            $item->description = $row['description'];
            $item->document_number = $row['document_number'];
            $item->document_date = strtotime($row['document_date']);

            $list[] = $item;
        }
        return $list;
    }

}
