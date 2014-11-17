<?php

namespace ZippyERP\ERP\Entity;

use ZippyERP\ERP\Consts;

/**
 * Клас-сущность  товар
 * 
 * @table=erp_item
 * @view=erp_item_view
 * @keyfield=item_id
 */
class Item extends \ZCL\DB\Entity
{

    // типы  ТМЦ

    const ITEM_TYPE_GOODS = 1; //товар
    const ITEM_TYPE_MBP = 2;   //МБП
    const ITEM_TYPE_SERVICE = 3; //Услуга
    const ITEM_TYPE_STUFF = 4; //материалы
    const ITEM_TYPE_PRODUCTION = 5; //Готовая продукция
    const ITEM_TYPE_RETSUM = 6; //Вмртуальный товар для  суммового  учета   в  рознице

    protected function afterLoad()
    {

        switch ($this->item_type) {
            case self::ITEM_TYPE_GOODS : $this->typename = 'Товар';
                break;
            case self::ITEM_TYPE_MBP : $this->typename = 'МБП';
                break;
            case self::ITEM_TYPE_SERVICE : $this->typename = 'Услуги';
                break;
            case self::ITEM_TYPE_STUFF : $this->typename = 'Материалы';
                break;
            case self::ITEM_TYPE_PRODUCTION : $this->typename = 'Готовая продукция';
                break;
        }

        $xml = @simplexml_load_string($this->detail);
        $this->priceopt = (string) ($xml->priceopt[0]);
        $this->priceret = (string) ($xml->priceret[0]);
        $this->barcode = (string) ($xml->barcode[0]);
        $this->description = (string) ($xml->description[0]);

        parent::afterLoad();
    }

    // типы  ТМЦ
    public static function getTypeList()
    {
        $list = array();
        $list[self::ITEM_TYPE_GOODS] = 'Товар';
        $list[self::ITEM_TYPE_MBP] = 'МБП';
        $list[self::ITEM_TYPE_SERVICE] = 'Услуги';
        $list[self::ITEM_TYPE_STUFF] = 'Материал';
        $list[self::ITEM_TYPE_PRODUCTION] = 'Готовая продукция';

        return $list;
    }

    protected function beforeSave()
    {
        parent::beforeSave();
        //упаковываем  данные в detail
        $this->detail = "<detail><priceopt>{$this->priceopt}</priceopt>";
        $this->detail .= "<priceret>{$this->priceret}</priceret>";
        $this->detail .= "<priceret>{$this->priceret}</priceret>";
        $this->detail .= "<barcode>{$this->barcode}</barcode>";
        $this->detail .= "<description>{$this->description}</description>";
        $this->detail .= "</detail>";

        return true;
    }

}
