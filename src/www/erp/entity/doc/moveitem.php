<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use ZippyERP\ERP\Entity\Store;
use ZippyERP\ERP\Entity\Stock;

/**
 * Класс-сущность  локумент перемещения товаров
 * 
 * @table=store_document
 * @view=store_document_view
 * @keyfield=document_id
 */
class MoveItem extends Document
{

    public function Execute()
    {
        foreach ($this->detaildata as $value) {

            $stock = Stock::getStock($this->headerdata['storefrom'], $value['item_id'], $value['price'], true);
            $stock->updateStock(0 - $value['quantity'],  $this->document_id, strlen($value['serial_number']) > 0 ? array($value['serial_number']) : array());

            $stock = Stock::getStock($this->headerdata['storeto'], $value['item_id'], $value['price'], true);
            $stock->updateStock($value['quantity'],  $this->document_id, strlen($value['serial_number']) > 0 ? array($value['serial_number']) : array());
        }
        return true;
    }

    public function generateReport()
    {

        $header = array(
            'date' => date('d.m.Y', $this->document_date),
            "from" => Store::load($this->headerdata["storefrom"])->storename,
            "to" => Store::load($this->headerdata["storeto"])->storename,
            "document_number" => $this->document_number
        );

        $reportgen = new \ZCL\RepGen\RepGen(_ROOT . 'templates/erp/templates/moveitem.html', $header);

        $i = 1;
        $detail = array();
        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "item_name" => $value['itemname'],
                "measure" => $value['measure_name'],
                "serial_number" => $value['serial_number'],
                "quantity" => $value['quantity']);
        }


        $html = $reportgen->generateSimple($detail);
        return $html;
    }

}
