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
    //   const ITEM_TYPE_GOODS = 1; //товар
    //  const ITEM_TYPE_MBP = 2;   //МБП
    const ITEM_TYPE_SERVICE = 3; //Услуга
    const ITEM_TYPE_STUFF = 0; //материалы
    // const ITEM_TYPE_PRODUCTION = 5; //Готовая продукция
    const ITEM_TYPE_RETSUM = 6; //Вмртуальный товар для  суммового  учета   в  рознице

    protected function afterLoad()
    {



        $xml = @simplexml_load_string($this->detail);
        $this->priceopt = (string) ($xml->priceopt[0]);
        $this->priceret = (string) ($xml->priceret[0]);
        $this->barcode = (string) ($xml->barcode[0]);
        $this->uktzed = (string) ($xml->uktzed[0]);
        $this->code = (string) ($xml->code[0]);
        $this->description = (string) ($xml->description[0]);

        parent::afterLoad();
    }

    // типы   
    public static function getTypeList()
    {
        $list = array();

        $list[self::ITEM_TYPE_SERVICE] = 'Услуга';
        $list[self::ITEM_TYPE_STUFF] = 'ТМЦ';


        return $list;
    }

    protected function beforeSave()
    {
        parent::beforeSave();
        //упаковываем  данные в detail
        $this->detail = "<detail><priceopt>{$this->priceopt}</priceopt>";
        $this->detail .= "<priceret>{$this->priceret}</priceret>";
        $this->detail .= "<code>{$this->code}</code>";
        $this->detail .= "<uktzed>{$this->uktzed}</uktzed>";
        $this->detail .= "<barcode>{$this->barcode}</barcode>";
        $this->detail .= "<description>{$this->description}</description>";
        $this->detail .= "</detail>";

        return true;
    }

    /**
     * Количество на оптовом складе на  дату
     *
     * @param mixed $item_id
     * @param mixed $date
     *
     */
    public static function getQuantity($item_id, $date)
    {
        $conn = \ZCL\DB\DB::getConnect();
        $where = "   stock_id IN( select stock_id from erp_store_stock st join erp_store sr on st.store_id = sr.store_id  where item_id= {$item_id} and store_type = " . Store::STORE_TYPE_OPT . " )  and date(document_date) <= " . $conn->DBDate($date);
        $sql = " select coalesce(sum(quantity),0) AS quantity  from erp_account_subconto  where " . $where;
        return $conn->GetOne($sql);
    }

    /**
     * возвращает ТМЦ по  коду УКТ ЗЕД
     * null если  не  найден
     *
     */
    public static function loadByUktzed($code)
    {

        return Item::findOne("detail LIKE '%<uktzed>{$code}</uktzed>%' ");
    }

    /**
     * возвращает  специльный  товар  для  суммвового  учета
     * 
     */
    public static function getSumItem()
    {
        $item = Item::getFirst('item_type=' . Item::ITEM_TYPE_RETSUM);
        if ($item instanceof Item) {
            return $item;
        } else {
            throw new \ZippyERP\System\Exception("Не  найдет товар для  суммового  учета");
        }
    }

}
