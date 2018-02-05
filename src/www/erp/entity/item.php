<?php

namespace ZippyERP\ERP\Entity;

/**
 * Клас-сущность  ТМЦ
 *
 * @table=erp_item
 * @view=erp_item_view
 * @keyfield=item_id
 */
class Item extends \ZCL\DB\Entity
{

    // типы  ТМЦ
    //      const ITEM_TYPE_GOODS = 1; //товар
    //    const ITEM_TYPE_MBP = 2;   //МБП
    const ITEM_TYPE_SERVICE = 3; //Услуга
    const ITEM_TYPE_STUFF = 0; //ТМЦ
    // const ITEM_TYPE_PRODUCTION = 5; //Готовая продукция
    const ITEM_TYPE_RETSUM = 6; //Вмртуальный товар для  суммового  учета   в  рознице
    const ITEM_TYPE_OS = 7; //Основные средства

    // const ITEM_TYPE_NMA = 8; //Нематериальные активы
    // const ITEM_TYPE_MNMA = 9; //Малоценные необортные средства

    protected function init() {
        $this->item_id = 0;
        $this->curname = '';
        $this->currate = 0.0;
        $this->priceopt = 0;
        $this->priceret = 0;
        $this->item_type = 0;
    }

    protected function afterLoad() {


        $xml = @simplexml_load_string($this->detail);
        $this->priceopt = (string) ($xml->priceopt[0]);
        $this->priceret = (string) ($xml->priceret[0]);
        $this->barcode = (string) ($xml->barcode[0]);
        $this->uktzed = (string) ($xml->uktzed[0]);
        //   $this->code = (string) ($xml->code[0]);
        $this->curname = (string) ($xml->curname[0]);
        $this->currate = doubleval($xml->currate[0]);

        parent::afterLoad();
    }

    // типы  ТМЦ
    public static function getTMZList() {
        $list = array();

        $list[self::ITEM_TYPE_SERVICE] = 'Послуга';
        $list[self::ITEM_TYPE_STUFF] = 'ТМЦ';
        //$list[self::ITEM_TYPE_GOODS] = 'Товари';
        //  $list[self::ITEM_TYPE_MBP] = 'МШП';
        //  $list[self::ITEM_TYPE_MNMA] = 'МНМА';


        return $list;
    }

    protected function beforeSave() {
        parent::beforeSave();
        //упаковываем  данные в detail
        $this->detail = "<detail><priceopt>{$this->priceopt}</priceopt>";
        $this->detail .= "<priceret>{$this->priceret}</priceret>";
        // $this->detail .= "<code>{$this->code}</code>";
        $this->detail .= "<uktzed>{$this->uktzed}</uktzed>";
        $this->detail .= "<barcode>{$this->barcode}</barcode>";
        $this->detail .= "<curname>{$this->curname}</curname>";
        $this->detail .= "<currate>{$this->currate}</currate>";
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
    public static function getQuantity($item_id, $date) {
        $conn = \ZDB\DB::getConnect();
        $where = "   stock_id IN( select stock_id from erp_store_stock st join erp_store sr on st.store_id = sr.store_id  where item_id= {$item_id} and store_type = " . Store::STORE_TYPE_OPT . " )  and date(document_date) <= " . $conn->DBDate($date);
        $sql = " select coalesce(sum(quantity),0) AS quantity  from erp_account_subconto  where " . $where;
        return $conn->GetOne($sql);
    }

    /**
     * возвращает ТМЦ по  коду УКТ ЗЕД
     * null если  не  найден
     *
     */
    public static function loadByUktzed($code) {

        return Item::findOne("detail LIKE '%<uktzed>{$code}</uktzed>%' ");
    }

    /**
     * возвращает  специльный  товар  для  суммвового  учета
     *
     */
    public static function getSumItem() {
        $item = Item::getFirst('item_type=' . Item::ITEM_TYPE_RETSUM);
        if ($item instanceof Item) {
            return $item;
        } else {
            throw new \ZippyERP\System\Exception("Не  найдет товар для  суммового  учета");
        }
    }

    /**
     * Возвращает  имя товара с  кодом
     * 
     * @param mixed $criteria
     * @param mixed $orderbyfield
     * @param mixed $orderbydir
     * @param mixed $count
     * @param mixed $offset
     */
    public static function findArrayEx($criteria = "", $orderbyfield = null, $orderbydir = null, $count = -1, $offset = -1) {
        if ($orderbyfield == null) {
            $orderbyfield = "itemname";
            $orderbydir = "asc";
        }

        $entitylist = self::find($criteria, $orderbyfield, $count, $offset);

        $list = array();
        foreach ($entitylist as $key => $value) {
            $list[$key] = $value->itemname;
            if (strlen($value->item_code) > 0)
                $list[$key] = $value->item_code . ', ' . $list[$key];
        }

        return $list;
    }

    protected function beforeDelete() {

        $conn = \ZDB\DB::getConnect();
        $sql = "  select count(*)  from  erp_store_stock where   item_id = {$this->item_id}";
        $cnt = $conn->GetOne($sql);
        return $cnt == 0;
    }

    //Возвращает розничную цену
    //$partionprice - учетная цена
    public function getRetPrice($partionprice) {
        if ($this->priceret == 0) {
            return $partionprice;
        }
        if (strpos($this->priceret, '%') > 0) {
            $ret = doubleval(str_replace('%', '', $this->priceret));
            return $partionprice + (int) $partionprice / 100 * $ret;
        } else {
            return $this->priceret * 100;
        }
    }

    //Возвращает оптовую цену
    public function getOptPrice($partionprice) {
        if ($this->priceopt == 0) {
            return $this->getRetPrice($partionprice);
        }
        if (strpos($this->priceopt, '%') > 0) {
            $ret = doubleval(str_replace('%', '', $this->priceopt));
            return $partionprice + (int) $partionprice / 100 * $ret;
        } else {
            return $this->priceopt * 100;
        }
    }

}
