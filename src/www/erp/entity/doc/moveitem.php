<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use \ZippyERP\ERP\Entity\Store;
use \ZippyERP\ERP\Entity\Stock;
use \ZippyERP\ERP\Entity\SubConto;
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

        $ret = 0;    // торговая  наценка
        $amount = 0;
        foreach ($this->detaildata as $value) {

            //списываем  со склада
            $stockfrom = Stock::getStock($this->headerdata['storefrom'], $value['item_id'], $value['partion']);
            $sc = new SubConto($this->document_id, $this->document_date, $value['type']);
            $sc->setStock($stockfrom->stock_id) ;
            $sc->setQuantity(0-$value['quantity']) ;
            $sc->save();

            $store = Store::load($this->headerdata['storeto']);
            if ($store->store_type == Store::STORE_TYPE_OPT) {    //оптовый
                 $stockto = Stock::getStock($this->headerdata['storeto'], $value['item_id'], $value['price'], true);

                 $sc = new SubConto($this->document_id, $this->document_date, $value['type']);
                 $sc->setStock($stockto->stock_id) ;
                 $sc->setQuantity( $value['quantity']) ;
                 $sc->save();
            }
            if ($store->store_type == Store::STORE_TYPE_RET) {    //розница
                $stockto = Stock::getFirst("store_id={$this->headerdata['storeto']} and item_id={$value['item_id']} and price={$value['price']} and partion={$value['partion']} and closed <> 1");
                if ($stockto instanceof Stock) {

                } else {
                    $stockto = new Stock();
                    $stockto->document_id = $this->document_id;
                    $stockto->store_id = $this->headerdata['storeto'];
                    $stockto->item_id = $value['item_id'];
                    $stockto->price = $value['price'];
                    $stockto->partion = $value['partion'];  // себестоимость
                    $stockto->Save();
                }
                 $sc = new SubConto($this->document_id, $this->document_date, 282);
                 $sc->setStock($stockto->stock_id) ;
                 $sc->setQuantity( $value['quantity']) ;
                 $sc->save();


                $ret += ($value['quantity']/1000) * ($value['price'] - $value['partion']);
                $amount += ($value['quantity']/1000)* $value['price'];
            }

            if ($store->store_type == Store::STORE_TYPE_RET_SUM) {   //розница суммовой учет

                //специальный  товар  для  cуммового  учета
                $item = \ZippyERP\ERP\Entity\Item::getSumItem();

                $stockto = Stock::getStock($this->headerdata['storeto'], $item->item_id, 1, true);
                $sc = new SubConto($this->document_id, $this->document_date, 282);
                $sc->setStock($stockto->stock_id) ;
                $sc->setQuantity( ($value['quantity'] ) * $value['price']) ; //цена  единицы  товара - 1 копейка.
                $sc->save();

                $ret += ($value['quantity']/1000) * ($value['price'] - $value['partion']);
                $amount += ($value['quantity']/1000) * $value['partion'];
            }
        }
        if ($amount > 0) {  // розница
            Entry::AddEntry(282, 281, $amount, $this->document_id, $this->document_date);
            Entry::AddEntry(282, 285, $ret, $this->document_id, $this->document_date );
            $sc = new SubConto($this->document_id, $this->document_date, 285);
            $sc->setExtCode($store->store_id);  //запоминаем на  каком  магащине  сколько наценки
            $sc->setAmount(0 - $ret);
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
                "quantity" => $value['quantity']/1000);
        }


        $report = new \ZippyERP\ERP\Report('moveitem.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

}
