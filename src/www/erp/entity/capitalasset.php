<?php

namespace ZippyERP\ERP\Entity;

/**
 * Клас-сущность  основные средства
 *
 * @table=erp_item
 * @view=erp_item_view
 * @keyfield=item_id
 */
class CapitalAsset extends \ZCL\DB\Entity
{

    protected function afterLoad() {


        $xml = @simplexml_load_string($this->detail);
        $this->typeos = (int) ($xml->typeos[0]);
        $this->expenses = (int) ($xml->expenses[0]);
        $this->datemaint = (int) ($xml->datemaint[0]);
        $this->depreciation = (string) ($xml->depreciation[0]);

        $this->inventory = (string) ($xml->inventory[0]);
        $this->cancelvalue = (string) ($xml->cancelvalue[0]);
        $this->value = (string) ($xml->value[0]);
        $this->term = (string) ($xml->term[0]);
        $this->group = (int) ($xml->group[0]);
        $this->norma = (string) ($xml->norma[0]);

        parent::afterLoad();
    }

    // типы  необоротных активов
    public static function getNAList() {
        $list = array();

        $list[10] = 'ОС';
        $list[11] = 'Прочие необоротные средства';
        $list[12] = 'Нематериальные активы';


        return $list;
    }

    protected function beforeSave() {
        parent::beforeSave();
        //упаковываем  данные в detail

        $this->detail = "<detail><depreciation>{$this->depreciation}</depreciation>";
        $this->detail .= "<inventory>{$this->inventory}</inventory>";
        $this->detail .= "<cancelvalue>{$this->cancelvalue}</cancelvalue>";
        $this->detail .= "<value>{$this->value}</value>";
        $this->detail .= "<term>{$this->term}</term>";
        $this->detail .= "<typeos>{$this->typeos}</typeos>";
        $this->detail .= "<expenses>{$this->expenses}</expenses>";
        $this->detail .= "<datemaint>{$this->datemaint}</datemaint>";
        $this->detail .= "<norma>{$this->norma}</norma>";
        $this->detail .= "<group>{$this->group}</group>";
        $this->detail .= "</detail>";

        return true;
    }

    /**
     * найти  по инвентарному  номеру
     *
     * @param mixed $inventory
     */
    public static function loadByInventory($inventory) {
        return CapitalAsset::getFirst("detail like '%<inventory>{$inventory}</inventory>%'");
    }

    //начисленая амортизация
    public function getDeprecationValue() {
        return SubConto::getAmount(0, 13, 0, 0, 0, 0, $this->item_id);
    }

}
