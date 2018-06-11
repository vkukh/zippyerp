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

    const TYPE_FIRM = 3;  //Частный предприиматель
    const TYPE_GOV = 4;    //Гос. организация
    const TYPE_JUR = 1;     //Фирма юр. лицо
    const TYPE_CLIENT = 5;    //Клиент физлицо  
    const TYPE_OTHER = 2;    //Прочий контакт

    protected function init() {
        $this->customer_id = 0;
    }

    protected function beforeSave() {
        parent::beforeSave();
        //упаковываем  данные в detail
        $this->detail = "<detail><code>{$this->code}</code>";
        $this->detail .= "<inn>{$this->inn}</inn>";
        $this->detail .= "<lic>{$this->lic}</lic>";

        $this->detail .= "<discount>{$this->discount}</discount>";
        $this->detail .= "<faddress><![CDATA[{$this->faddress}]]></faddress>";
        $this->detail .= "<laddress><![CDATA[{$this->laddress}]]></laddress>";
        $this->detail .= "<comment><![CDATA[{$this->comment}]]></comment>";
        $this->detail .= "<bankaccount1><bank>{$this->bank}</bank><account>{$this->bankaccount}</account></bankaccount1>";
        $this->detail .= "</detail>";

        return true;
    }

    protected function afterLoad() {
        //распаковываем  данные из detail
        $xml = simplexml_load_string($this->detail);
        $this->code = (string) ($xml->code[0]);
        $this->inn = (string) ($xml->inn[0]);
        $this->lic = (string) ($xml->lic[0]);

        $this->discount = doubleval($xml->discount[0]);
        $this->laddress = (string) ($xml->laddress[0]);
        $this->faddress = (string) ($xml->faddress[0]);
        $this->comment = (string) ($xml->comment[0]);
        $this->bank = (string) ($xml->bankaccount1->bank[0]);
        $this->bankaccount = (string) ($xml->bankaccount1->account[0]);

        parent::afterLoad();
    }

    /**
     * возвращает контрашента по  ИНН
     * null если  не  найден
     *
     */
    public static function loadByInn($inn) {

        return Customer::findOne("detail LIKE '%<inn>{$inn}</inn>%' ");
    }

    public function beforeDelete() {

        $conn = \ZDB\DB::getConnect();
        $sql = "  select count(*)  from  erp_account_subconto where   customer_id = {$this->customer_id}";
        $cnt = $conn->GetOne($sql);
        return ($cnt == 0) ? true : false;
        ;
    }

    public static function getTypeList() {

        return array(
            Customer::TYPE_FIRM => 'ФОП',
            Customer::TYPE_CLIENT => 'Фіз. особа',
            Customer::TYPE_JUR => 'Юр. особа',
            Customer::TYPE_OTHER => 'Різні контакти',
            Customer::TYPE_GOV => 'Держзаклад'
        );
    }

}
