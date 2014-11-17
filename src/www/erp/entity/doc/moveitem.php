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
        $ret = 0;
        $amount = 0;
        foreach ($this->detaildata as $value) {

            $stock = Stock::getStock($this->headerdata['storefrom'], $value['item_id'], $value['partion']);
            $stock->updateStock(0 - $value['quantity'], $this->document_id);

            $store = Store::load($this->headerdata['storeto']);
            if ($store->store_type == Store::STORE_TYPE_OPT) {
                $stock = Stock::getStock($this->headerdata['storeto'], $value['item_id'], $value['partion'], true);
                $stock->updateStock($value['quantity'], $this->document_id);
            }
            if ($store->store_type == Store::STORE_TYPE_RET) {
                $stock = Stock::getStock($this->headerdata['storeto'], $value['item_id'], $value['partion'], true);
                $stock->updateStock($value['quantity'], $this->document_id);
                $stock->price = $value['price'];
                $stock->Save();
                $ret += $value['quantity'] * ($value['price'] - $value['partion']);
                $amount += $value['quantity'] * $value['partion'];
            }

            if ($store->store_type == Store::STORE_TYPE_RET_SUM) {

                //специальный  товар  для  уммового  учета
                $item = \ZippyERP\ERP\Entity\Item::getFirst('item_type=' . \ZippyERP\ERP\Entity\Item::ITEM_TYPE_RETSUM);

                $stock = Stock::getStock($this->headerdata['storeto'], $item->item_id, 1, true);
                $stock->updateStock($value['quantity'] * $value['price'], $this->document_id);
                $stock->Save();
                $ret += $value['quantity'] * ($value['price'] - $value['partion']);
                $amount += $value['quantity'] * $value['partion'];
            }

            if ($amount > 0) {  // розница
                Entry::AddEntry(282, 281, $amount, $this->document_id, 'Передача   в  розницу');
                Entry::AddEntry(282, 285, $ret, $this->document_id, 'Торговая наценка');
            }
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

        $reportgen = new \ZCL\RepGen\RepGen(_ROOT . 'templates/erp/printforms/moveitem.html', $header);

        $i = 1;
        $detail = array();
        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "item_name" => $value['itemname'],
                "measure" => $value['measure_name'],
                "price" => $value['price'],
                "quantity" => $value['quantity']);
        }


        $report = new \ZippyERP\ERP\Report('moveitem.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

}
