<?php

namespace ZippyERP\ERP\Entity;

/**
 * Класс-сущность  контрагент
 *
 * @table=erp_customer
 * @keyfield=customer_id
 */
class Customer extends \ZCL\DB\Entity
{

    const TYPE_BUYER = 1;         //Покупатель
    const TYPE_SELLER = 2;        //Продавец
    const TYPE_BUYER_SELLER = 3;  //Частная фирма
    const TYPE_GOV = 4;           //Гос. организация
    const TYPE_OTHER = 0;     //Просто   организация
    const TYPE_CLIENT = 5;    //Клиент не фирма  

    public $code, $city, $zip, $address, $url, $phone;

    protected function init() {
        $this->customer_id = 0;
    }

    protected function beforeSave() {
        parent::beforeSave();
        //упаковываем  данные в detail
        $this->detail = "<detail><code>{$this->code}</code>";
        $this->detail .= "<inn>{$this->inn}</inn>";
        $this->detail .= "<lic>{$this->lic}</lic>";
        $this->detail .= "<phone>{$this->phone}</phone>";
        $this->detail .= "<email>{$this->email}</email>";
        $this->detail .= "<discount>{$this->discount}</discount>";
        $this->detail .= "<faddress><![CDATA[{$this->faddress}]]></faddress>";
        $this->detail .= "<laddress><![CDATA[{$this->laddress}]]></laddress>";
        $this->detail .= "<comment><![CDATA[{$this->comment}]]></comment>";
        $this->detail .= "<bankaccount1><bank>{$this->bank}</bank><account>{$this->bankaccount}</account></bankaccount1>";
        $this->detail .= "<bankaccount2><bank>{$this->bank2}</bank><account>{$this->bankaccount2}</account></bankaccount2>";
        $this->detail .= "</detail>";

        return true;
    }

    protected function afterLoad() {
        //распаковываем  данные из detail
        $xml = simplexml_load_string($this->detail);
        $this->code = (string) ($xml->code[0]);
        $this->inn = (string) ($xml->inn[0]);
        $this->lic = (string) ($xml->lic[0]);
        $this->email = (string) ($xml->email[0]);
        $this->phone = (string) ($xml->phone[0]);
        $this->discount = doubleval($xml->discount[0]);
        $this->laddress = (string) ($xml->laddress[0]);
        $this->faddress = (string) ($xml->faddress[0]);
        $this->comment = (string) ($xml->comment[0]);
        $this->bank = (string) ($xml->bankaccount1->bank[0]);
        $this->bank2 = (string) ($xml->bankaccount2->bank[0]);
        $this->bankaccount = (string) ($xml->bankaccount1->account[0]);
        $this->bankaccount2 = (string) ($xml->bankaccount2->account[0]);

        parent::afterLoad();
    }

    //список продавцов
    public static function getSellers() {
        return Customer::findArray('customer_name', 'cust_type=' . self::TYPE_SELLER . ' or cust_type= ' . self::TYPE_BUYER_SELLER, 'customer_name');
    }

    //список покупателей
    public static function getBuyers() {
        return Customer::findArray('customer_name', 'cust_type=' . self::TYPE_BUYER . ' or cust_type= ' . self::TYPE_BUYER_SELLER . ' or cust_type= ' . self::TYPE_CLIENT, 'customer_name');
    }

    //список госучреждений
    public static function getGov() {
        return Customer::findArray('customer_name', 'cust_type=' . self::TYPE_GOV, 'customer_name');
    }

    /**
     * возвращает контрашента по  ИНН
     * null если  не  найден
     *
     */
    public static function loadByInn($inn) {

        return Customer::findOne("detail LIKE '%<inn>{$inn}</inn>%' ");
    }

    protected function beforeDelete() {

        $conn = \ZDB\DB::getConnect();
        $sql = "  select count(*)  from  erp_account_subcont where   customer_id = {$this->customer_id}";
        $cnt = $conn->GetOne($sql);
        return $cnt == 0;
    }

    //поиск клиента по имени или  телефону контакта для автокомплита
    public static function searchClient($text) {
        $list = array();
        $conn = \ZDB\DB::getConnect();
        $text = $conn->qstr('%' . $text . '%');
        $sql = "  select cu.`customer_id`,cu.`customer_name`,ct.`phone` from `erp_customer` cu    join `erp_contact` ct on cu.`contact_id` = ct.`contact_id` where (cust_type=3 or cust_type=5) and (cu.`customer_name`like {$text} or ct.`phone` like {$text})";
        $rs = $conn->Execute($sql);
        foreach ($rs as $c) {

            $list[$c['customer_id']] = $c['customer_name'];
            if (strlen($c['phone']) > 0)
                $list[$c['customer_id']] = $c['customer_name'] . " ({$c['phone']})";
        }

        return $list;
    }

}
