<?php

namespace App\Entity;

/**
 * Клас-сущность  ОС и НМА
 *
 * @table=cassetlist
 * @keyfield=ca_id
 */
class CAsset extends \ZCL\DB\Entity
{

    protected function init() {
        $this->ca_id = 0;
    }

    protected function beforeSave() {
        parent::beforeSave();
        //упаковываем  данные в detail
        $this->detail = "<detail><emp_id>{$this->emp_id}</emp_id>";
        $this->detail .= "<emp_name>{$this->emp_name}</emp_name>";
        $this->detail .= "<description><![CDATA[{$this->description}]]></description>";
        $this->detail .= "<serial>{$this->serial}</serial>";
        $this->detail .= "<code>{$this->code}</code>";
        $this->detail .= "<depreciation>{$this->depreciation}</depreciation>";
        $this->detail .= "<cancelvalue>{$this->cancelvalue}</cancelvalue>";
        $this->detail .= "<value>{$this->value}</value>";
        $this->detail .= "<term>{$this->term}</term>";
        $this->detail .= "<acc_code>{$this->acc_code}</acc_code>";
        $this->detail .= "<expenses>{$this->expenses}</expenses>";
        $this->detail .= "<datemaint>{$this->datemaint}</datemaint>";
        $this->detail .= "<norma>{$this->norma}</norma>";
        $this->detail .= "<group>{$this->group}</group>";


        $this->detail .= "</detail>";

        return true;
    }

    protected function afterLoad() {
        //распаковываем  данные из detail
        $xml = simplexml_load_string($this->detail);
        $this->emp_id = (int) ($xml->emp_id[0]);
        $this->emp_name = (string) ($xml->emp_name[0]);
        $this->serial = (string) ($xml->serial[0]);
        $this->code = (string) ($xml->code[0]);
        $this->expenses = (string) ($xml->expenses[0]);
        $this->datemaint = (int) ($xml->datemaint[0]);
        $this->depreciation = (int) ($xml->depreciation[0]);
        $this->description = (string) ($xml->description[0]);

        $this->acc_code = (string) ($xml->acc_code[0]);
        $this->cancelvalue = (string) ($xml->cancelvalue[0]);
        $this->value = (string) ($xml->value[0]);
        $this->term = (string) ($xml->term[0]);
        $this->group = (int) ($xml->group[0]);
        $this->norma = (string) ($xml->norma[0]);





        parent::afterLoad();
    }

    protected function beforeDelete() {

        $conn = \ZDB\DB::getConnect();
        $sql = "  select count(*)  from  entrylist where   ca_id = {$this->ca_id}";
        $cnt = $conn->GetOne($sql);
        return ($cnt > 0) ? "Нельзя удалять используемый ТМЦ" : "";
    }    

    /**
     * найти  по инвентарному  номеру
     *
     * @param mixed $inventory
     */
    public static function loadByInventory($code) {
        return CapitalAsset::getFirst("detail like '%<code>{$code}</code>%'");
    }

    //начисленая амортизация
    public function getDeprecationValue() {
        if (in_array($this->acc_code, array(104, 106))) {
            $acc_code = '131';
        }
        if ($this->acc_code == '12') {
            $acc_code = '133';   //нематериальные активы
        }
        return \App\Entity\Entry::getAmount(0, $acc_code, 0, 0, 0, 0, $this->ca_id);
    }

}
