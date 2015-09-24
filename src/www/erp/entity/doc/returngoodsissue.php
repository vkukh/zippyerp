<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use \ZippyERP\ERP\Util;
use \ZippyERP\ERP\Entity\Entry;
use \ZippyERP\ERP\Entity\SubConto;
use \ZippyERP\ERP\Entity\MoneyFund;
use \ZippyERP\ERP\Entity\Item;
use \ZippyERP\ERP\Helper as H;

/**
 * Класс-сущность  документ возвратная  накладая
 *
 */
class ReturnGoodsIssue extends Document
{

    public function generateReport()
    {


        $i = 1;
        $detail = array();

        foreach ($this->detaildata as $value) {

            if (isset($detail[$value['item_id']])) {
                $detail[$value['item_id']]['quantity'] += $value['quantity']/1000;
            } else {
                $detail[$value['item_id']] = array("no" => $i++,
                    "tovar_name" => $value['itemname'],
                    "measure" => $value['measure_name'],
                    "quantity" => $value['quantity']/1000,
                    "price" => H::fm($value['price']),
                    "amount" => H::fm(($value['quantity']/1000) * $value['price'])
                );
            }
        }


        //$customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata["customer"]);
        $header = array('date' => date('d.m.Y', $this->document_date),
            "customername" => $this->headerdata["customername"],
            "document_number" => $this->document_number,
            "nds" => H::fm($this->headerdata["totalnds"]),
            "total" => H::fm($this->headerdata["total"]),
            "summa" => Util::ucfirst(Util::money2str($this->headerdata["total"] / 100, '.', ''))
        );

        $report = new \ZippyERP\ERP\Report('returngoodsissue.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {
        $conn = \ZCL\DB\DB::getConnect();
        $conn->StartTrans();

         $types = array();

        //аналитика
        foreach ($this->detaildata as $item) {
            $stock = \ZippyERP\ERP\Entity\Stock::getStock($this->headerdata['store'], $item['item_id'], $item['price'], true);

            $sc = new SubConto($this->document_id, $this->document_date, $item['type']);
            $sc->setStock($stock->stock_id);
            $sc->setQuantity( $item['quantity']);
            $sc->setAmount( $item['pamount'] );
            $sc->save();

            //группируем по синтетическим счетам
            if ($types[$item['type']] > 0) {
                $types[$item['type']]['amount'] = $types[$item['type']] + $item['price'] *($item['quantity']/1000);
                $types[$item['type']]['pamount'] = $types[$item['type']] + $item['partion'] *($item['quantity']/1000);
                $types[$item['type']]['namount'] = $types[$item['type']] + $item['nds'] ;
            } else {
                $types[$item['type']]['amount'] =  $item['pricends'] *($item['quantity']/1000);
                $types[$item['type']]['pamount'] =  $item['partion'] *($item['quantity']/1000);
                $types[$item['type']]['namount'] =  $item['nds'] ;

            }
        }

        foreach ($types as $acc => $value) {

            if($acc == 281){
               Entry::AddEntry("902", "281", 0-$value['pamount'], $this->document_id,$this->document_date);
               Entry::AddEntry("36", "702", 0-$value['amount'], $this->document_id,$this->document_date);
               if ($this->headerdata['isnds'] > 0) {
                  Entry::AddEntry("702", "643", 0-$value['namount'], $this->document_id,$this->document_date);
               }
            }
            if($acc == 26){
               Entry::AddEntry("902", "26", 0-$value['pamount'], $this->document_id,$this->document_date);
               Entry::AddEntry("36", "701", 0-$value['amount'], $this->document_id,$this->document_date);
               if ($this->headerdata['isnds'] > 0) {
                  Entry::AddEntry("701", "643", 0-$value['namount'], $this->document_id,$this->document_date);
               }
            }
               $sc = new SubConto($this->document_id, $this->document_date, 36);
               $sc->setCustomer($this->headerdata["customer"]);
               $sc->setAmount( 0- $value['amount']);
               $sc->save();



        }


        if ($this->headerdata['cash'] == true) {

            $cash = MoneyFund::getCash();
            \ZippyERP\ERP\Entity\Entry::AddEntry("30", "36", 0-$total, $this->document_id, $cash->id, $customer_id);
            $sc = new SubConto($this->document_id, $this->document_date, 36);
            $sc->setCustomer($this->headerdata["customer"]);
            $sc->setAmount(  $total);
            $sc->save();
            $sc = new SubConto($this->document_id, $this->document_date, 30);
            $sc->setMoneyfund($cash->id);
            $sc->setAmount(0- $total);
            // $sc->save();

        }

        $conn->CompleteTrans();
        return true;
    }

    public function getRelationBased()
    {
        $list = array();
        $list['PurchaseInvoice'] = 'Счет входящий';

        return $list;
    }

}
