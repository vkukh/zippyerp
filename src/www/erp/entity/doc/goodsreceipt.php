<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use \ZippyERP\ERP\Entity\Item;
use \ZippyERP\ERP\Helper as H;

/**
 * Класс-сущность  локумент приходная  накладая
 * 
 */
class GoodsReceipt extends Document
{

    public function generateReport()
    {

        $customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata["customer"]);

        $i = 1;

        $detail = array();
        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "itemname" => $value['itemname'],
                "measure" => $value['measure_name'],
                "quantity" => $value['quantity'],
                "price" => H::fm($value['price']),
                "pricends" => H::fm($value['pricends']),
                "amount" => H::fm($value['amount'])
            );
        }

        $header = array('date' => date('d.m.Y', $this->document_date),
            "customer" => $customer->customer_name,
            "document_number" => $this->document_number,
            "totalnds" => H::fm($this->headerdata["totalnds"]),
            "total" => H::fm($this->headerdata["total"])
        );



        $report = new \ZippyERP\ERP\Report('goodsreceipt.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {
        $conn = \ZCL\DB\DB::getConnect();
        $conn->StartTrans();

        foreach ($this->detaildata as $value) {
            //поиск  записи  о  товаре   на складе

            $stock = \ZippyERP\ERP\Entity\Stock::getStock($this->headerdata['store'], $value['item_id'], $value['price'], true);
            $stock->updateStock($value['quantity'], $this->document_id, strlen($value['serial_number']) > 0 ? array($value['serial_number']) : array());
        }
        $total = $this->headerdata['total'];
        $customer_id = $this->headerdata["customer"];

       // \ZippyERP\ERP\Entity\Customer::AddActivity($customer_id,  $total, $this->document_id);

        if ($this->headerdata['cash'] == true) {
            
            $cash = MoneyFund::getCash();
            \ZippyERP\ERP\Entity\Entry::AddEntry("63", "30", $total, $this->document_id,$customer_id,$cash->id);
          //  MoneyFund::AddActivity($cash->id, 0 - $this->headerdata['total'], $this->document_id);
        }

        //налоговый кредит
        if ($this->headerdata['totalnds'] > 0) {
            \ZippyERP\ERP\Entity\Entry::AddEntry("644", "63", $this->headerdata['totalnds'], $this->document_id);
        }

        $a281 = 0;
        $a201 = 0;
        foreach ($this->detaildata as $value) {
            $amount = $value['quantity'] * $value['price'];
            if ($value['item_type'] == Item::ITEM_TYPE_GOODS) {  //товары
                $a281 += $amount;
            }
            if ($value['item_type'] == Item::ITEM_TYPE_STUFF) {  //материалы
                $a201 += $amount;
            }
        }
        if ($a281 > 0) {
            \ZippyERP\ERP\Entity\Entry::AddEntry("281", "63", $a281, $this->document_id,0,$customer_id);
        }
        if ($a201 > 0) {
            \ZippyERP\ERP\Entity\Entry::AddEntry("201", "63", $a201, $this->document_id,0,$customer_id);
        }

        $conn->CompleteTrans();



        return true;
    }

    public function getRelationBased()
    {
        $list = array();
        $list['TaxInvoiceIncome'] = 'Входящая НН';

        return $list;
    }

}
