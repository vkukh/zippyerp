<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use \ZippyERP\ERP\Entity\Store;
use \ZippyERP\ERP\Entity\Stock;
use \ZippyERP\ERP\Entity\Entry;
use \ZippyERP\ERP\Helper as H;

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
        $conn = \ZCL\DB\DB::getConnect();
        $conn->StartTrans();

        $ret = 0;
        $amount = 0;
        foreach ($this->detaildata as $value) {

            $stock = Stock::getStock($this->headerdata['storefrom'], $value['item_id'], $value['partion']);
            $stock->updateStock(0 - $value['quantity'], $this->document_id);

            $store = Store::load($this->headerdata['storeto']);
            if ($store->store_type == Store::STORE_TYPE_OPT) {
                $stock = Stock::getStock($this->headerdata['storeto'], $value['item_id'], $value['price'], true);
                $stock->updateStock($value['quantity'], $this->document_id);
            }
            if ($store->store_type == Store::STORE_TYPE_RET) {
                $stock = Stock::getFirst("store_id={$this->headerdata['storeto']} and item_id={$value['item_id']} and price={$value['price']} and partion={$value['partion']} and closed <> 1");
                if ($stock instanceof Stock) {
                    $stock->updateStock($value['quantity'], $this->document_id);
                } else {
                    $stock = new Stock();
                    $stock->document_id = $this->document_id;
                    $stock->store_id = $this->headerdata['storeto'];
                    $stock->item_id = $value['item_id'];
                    $stock->price = $value['price'];
                    $stock->partion = $value['partion'];  // себестоимость
                }
                $stock->Save();

                $ret += $value['quantity'] * ($value['price'] - $value['partion']);
                $amount += $value['quantity'] * $value['price'];
            }

            if ($store->store_type == Store::STORE_TYPE_RET_SUM) {

                //специальный  товар  для  cуммового  учета
                $item = \ZippyERP\ERP\Entity\Item::getFirst('item_type=' . \ZippyERP\ERP\Entity\Item::ITEM_TYPE_RETSUM);

                $stock = Stock::getStock($this->headerdata['storeto'], $item->item_id, 1, true);
                $stock->updateStock($value['quantity'] * $value['price'], $this->document_id);
                $stock->Save();
                $ret += $value['quantity'] * ($value['price'] - $value['partion']);
                $amount += $value['quantity'] * $value['partion'];
            }

            if ($amount > 0) {  // розница
                Entry::AddEntry(282, 281, $amount, $this->document_id);
                Entry::AddEntry(282, 285, $ret, $this->document_id,0,$store->store_id);
            }
        }
        $conn->CompleteTrans();
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

        $reportgen = new \ZCL\RepGen\RepGen(_ROOT . 'templates/erp/printforms/moveitem.html', $header);

        $i = 1;
        $detail = array();
        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "item_name" => $value['itemname'],
                "measure" => $value['measure_name'],
                "price" => H::fm($value['price']),
                "quantity" => $value['quantity']);
        }


        $report = new \ZippyERP\ERP\Report('moveitem.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

}
