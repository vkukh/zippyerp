<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use \ZippyERP\ERP\Entity\Store;
use \ZippyERP\ERP\Entity\Stock;
use \ZippyERP\ERP\Entity\Entry;
use \ZippyERP\ERP\Entity\SubConto;
use \ZippyERP\ERP\Helper as H;

/**
 * Класс-сущность  локумент перемещения товаров
 *
 * @table=store_document
 * @view=store_document_view
 * @keyfield=document_id
 */
class MoveBackItem extends Document
{

    public function Execute()
    {
        $conn = \ZDB\DB\DB::getConnect();
        $conn->StartTrans();

        $ret = 0;    // торговая  наценка
        $amount = 0;
        foreach ($this->detaildata as $value) {

            //приниммаем на склад
            $stockto = Stock::getStock($this->headerdata['storeto'], $value['item_id'], $value['partion'], true);
            $sc = new SubConto($this, $value['type'], ($value['quantity'] / 1000) * $stockto->price);
            $sc->setStock($stockto->stock_id);
            $sc->setQuantity($value['quantity']);
            $sc->save();

            $store = Store::load($this->headerdata['storefrom']);

            if ($store->store_type == Store::STORE_TYPE_RET) {    //розница
                $stockfrom = Stock::getFirst("store_id={$this->headerdata['storefrom']} and item_id={$value['item_id']} and price={$value['price']} and partion={$value['partion']} and closed <> 1");
                if ($stockfrom == null)
                    return false;
                $sc = new SubConto($this, 282, 0 - ($value['quantity'] / 1000) * $stockfrom->price);
                $sc->setStock($stockfrom->stock_id);
                $sc->setQuantity(0 - $value['quantity']);
                $sc->save();


                $ret += ($value['quantity'] / 1000) * ($value['price'] - $value['partion']);
                $amount += ($value['quantity'] / 1000) * $value['partion'];
            }

            if ($store->store_type == Store::STORE_TYPE_RET_SUM) {   //розница суммовой учет
                //специальный  товар  для  cуммового  учета
                $item = \ZippyERP\ERP\Entity\Item::getSumItem();

                $stockfrom = Stock::getStock($this->headerdata['storefrom'], $item->item_id, 1, true);
                $sc = new SubConto($this, 282, 0 - ($value['quantity'] ) * $value['price']);  //цена  единицы  товара = 1 копейка.
                $sc->setStock($stockfrom->stock_id);
                $sc->setQuantity(0 - ($value['quantity'] ) * $value['price']); //цена  единицы  товара - 1 копейка.
                $sc->save();

                $ret += ($value['quantity'] / 1000) * ($value['price'] - $value['partion']);
                $amount += ($value['quantity'] / 1000) * $value['partion'];
            }
        }
        if ($amount > 0) {  // розница
            Entry::AddEntry(281, 282, $amount, $this->document_id, $this->document_date);
            Entry::AddEntry(285, 282, $ret, $this->document_id, $this->document_date);
            $sc = new SubConto($this, 285, $ret);
            $sc->setExtCode($store->store_id);
            $sc->save();
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
                "quantity" => $value['quantity'] / 1000);
        }


        $report = new \ZippyERP\ERP\Report('movebackitem.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

}
