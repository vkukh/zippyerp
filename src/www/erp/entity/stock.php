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
     * @param mixed $fieldname
     * @param mixed $criteria
     * @return []
     * @static
     */
    public static function findArrayEx($criteria = "")
    {
        $entitylist = self::find($criteria);

        $list = array();
        foreach ($entitylist as $key => $value) {

            $list[$key] = $value->itemname . ', ' . number_format($value->price / 100, 2);
        }

        return $list;
    }

    /**
     * Возвращает запись  со  склада
     * 
     * @param mixed $store_id  Склад
     * @param mixed $tovar_id  Товар
     * @param mixed $price     Цена 
     * @param mixed $create    Создать  если  не   существует
     */
    public static function getStock($store_id, $item_id, $partion, $create = false)
    {
        $stock = self::findOne("store_id = {$store_id} and item_id = {$item_id} and partion = {$partion} ");

        if ($stock == null && $create == true) {
            $stock = new Stock();
            $stock->store_id = $store_id;
            $stock->item_id = $item_id;
            //  партия  товара  определяется  себестоимостью
            $stock->price = $partion;
            $stock->partion = $partion;
            $stock->Save();
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
    public function updateStock($qty,  $document_id, $serials = array())
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

}
