<?php

namespace ZippyERP\ERP\Entity;

/**
 * Клас-сущность  записи  о  движении товара на  складе.
 * 
 * @table=erp_store_stock
 * @view=erp_stock_view
 * @keyfield=stock_id
 */
class Stock extends \ZCL\DB\Entity
{

    /**
     * Метод  для   получения  имени  ТМЦ  с  ценой
     *      
     * @param mixed $criteria
     * @return []
     * @static
     */
    public static function findArrayEx($criteria = "", $orderbyfield = null, $orderbydir = null, $count = -1, $offset = -1)
    {
        $entitylist = self::find($criteria, $orderbyfield, $orderbydir, $count, $offset);

        $list = array();
        foreach ($entitylist as $key => $value) {

            $list[$key] = $value->itemname . ', ' . \ZippyERP\ERP\Helper::fm($value->price);
        }

        return $list;
    }

    /**
     * Возвращает запись  со  склада по  цене (партии  для  оптового)  товара.
     * 
     * @param mixed $store_id  Склад
     * @param mixed $tovar_id  Товар
     * @param mixed $price     Цена 
     * @param mixed $create    Создать  если  не   существует
     */
    public static function getStock($store_id, $item_id, $price, $create = false)
    {

        $stock = self::findOne("store_id = {$store_id} and item_id = {$item_id} and price = {$price} ");
        if ($stock == null && $create == true) {
            $stock = new Stock();
            $stock->store_id = $store_id;
            $stock->item_id = $item_id;
            $stock->price = $price;
            $stock->partion = $price;

            if ($item_id == 0) {  // товар  для  суммового  учета
                $stock->price = 1;
                $stock->partion = 1;
                $stock->item_id = 0;
            }

            $stock->Save();
        }
        if ($stock->closed == 1) {
            $stock->closed == 0;
            $stock->Save();     //enable partion
        }
        return $stock;
    }

    /**
     * Обновляет  склад.
     * 
     * @param mixed $stock    Запись  о  товаре
     * @param mixed $qty      Количество )отрицительное  списывает  товар)
     * @param mixed $document Документ 
     * @param mixed $serials  Серийные  номера, RFID 
     */
    public function updateStock($qty, $document_id, $serials = array())
    {
        $conn = \ZCL\DB\DB::getConnect();
        $sql = "insert  into erp_stock_activity (stock_id,document_id,qty) values ( {$this->stock_id},{$document_id},{$qty} )";
        $conn->Execute($sql);

        foreach ($serials as $serial) {
            if ($qty > 0)
                $sql = "insert into erp_store_stock_serials (stock_id,serial_number)  values ( {$this->stock_id}," . $this->qstr($serial) . " ) ";
            else
                $sql = "delete from erp_store_stock_serials where stock_id={$this->stock_id},serial_number= " . $this->qstr($serial);
            $conn->Execute($sql);
        }
    }

    /**
     * Количество в  партии на складе на  дату
     * 
     * @param mixed $stock_id
     * @param mixed $date
     * 
     */
    public static function getQuantity($stock_id, $date)
    {
        $conn = \ZCL\DB\DB::getConnect();
        $sql = " select coalesce(sum(quantity),0) AS quantity  from erp_stock_activity_view  where    stock_id = {$stock_id} and date(document_date) <= " . $conn->DBDate($date);
        return $conn->GetOne($sql);
    }

    /**
     * Количество зарезервинование  и  ожидаемое после  даты
     * 
     * @param mixed $stock_id
     * @param mixed $date
     * 
     * @return Массив с  двумя  значениями 'r'  и 'p'
     */
    public static function getQuantityFuture($stock_id, $date)
    {
        $conn = \ZCL\DB\DB::getConnect();
        $sql = " select coalesce(sum(case  when  quantity > 0 then quantity else 0 end ),0) as  w,  coalesce(sum(case  when  quantity < 0 then 0-quantity else 0 end ),0) as  r  from erp_stock_activity_view  where    stock_id = {$stock_id} and date(document_date) > " . $conn->DBDate($date);
        return $conn->GetRow($sql);
    }

}
