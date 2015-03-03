<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use \ZippyERP\ERP\Util;
use \ZippyERP\ERP\Entity\Entry;
use \ZippyERP\ERP\Entity\MoneyFund;
use \ZippyERP\ERP\Entity\Item;
use \ZippyERP\ERP\Helper as H;

/**
 * Класс-сущность  документ расходная  накладая
 * 
 */
class GoodsIssue extends Document
{

    public function generateReport()
    {


        $i = 1;
        $detail = array();

        foreach ($this->detaildata as $value) {

            if (isset($detail[$value['item_id']])) {
                $detail[$value['item_id']]['quantity'] += $value['quantity'];
            } else {
                $detail[$value['item_id']] = array("no" => $i++,
                    "tovar_name" => $value['itemname'],
                    "measure" => $value['measure_name'],
                    "quantity" => $value['quantity'],
                    "price" => H::fm($value['price']),
                    "amount" => H::fm($value['quantity'] * $value['price'])
                );
            }
        }

        $firm = \ZippyERP\System\System::getOptions("firmdetail");

        $customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata["customer"]);
        $header = array('date' => date('d.m.Y', $this->document_date),
            "firmname" => $firm['name'],
            "firmcode" => $firm['code'],
            "customername" => $customer->customer_name,
            "document_number" => $this->document_number,
            "nds" => H::fm($this->headerdata["totalnds"]),
            "total" => H::fm($this->headerdata["total"]),
            "summa" => Util::ucfirst(Util::money2str($this->headerdata["total"] / 100, '.', ''))
        );

        $report = new \ZippyERP\ERP\Report('goodsissue.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {
        $conn = \ZCL\DB\DB::getConnect();
        $conn->StartTrans();

        // \ZippyERP\ERP\Entity\Customer::AddActivity($customer_id, 0 - $total, $this->document_id);


        if ($this->headerdata['cash'] == true) {
            
            $cash = MoneyFund::getCash();
            \ZippyERP\ERP\Entity\Entry::AddEntry("30", "36", $total, $this->document_id,$cash->id,$customer_id);
            //MoneyFund::AddActivity($cash->id, $this->headerdata['total'], $this->document_id);
        }


        //налоговые  обязательства
        if ($this->headerdata['nds'] > 0) {
            //$total = $total - $this->headerdata['nds'];
            \ZippyERP\ERP\Entity\Entry::AddEntry("702", "643", $this->headerdata['nds'], $this->document_id);
        }


        //группируем  суммы
        $a281 = 0;
        $a26 = 0;
        $s701 = 0;
        $s702 = 0;

        foreach ($this->detaildata as $value) {
            $stock = \ZippyERP\ERP\Entity\Stock::getStock($this->headerdata['store'], $value['item_id'], $value['partion'], true);
            $stock->updateStock(0 - $value['quantity'], $this->document_id);


            $amount = $value['quantity'] * $value['price'];
            $_a = $value['quantity'] * $value['partion'];
            if ($value['item_type'] == Item::ITEM_TYPE_GOODS) {  //товары
                $a281 += $_a;
                $s702 += $amount;
            }
            if ($value['item_type'] == Item::ITEM_TYPE_PRODUCTION) {  //готовая продукция
                $s701 += $amount;
                $a26 += $_a;
            }
        }
        if ($a281 > 0) {
            \ZippyERP\ERP\Entity\Entry::AddEntry("902", "281", $a281, $this->document_id);
            \ZippyERP\ERP\Entity\Entry::AddEntry("36", "702", $s702, $this->document_id,$customer_id);
        }
        if ($a26 > 0) {
            \ZippyERP\ERP\Entity\Entry::AddEntry("902", "26", $a26, $this->document_id);
            \ZippyERP\ERP\Entity\Entry::AddEntry("36", "701", $s701, $this->document_id,$customer_id);
        }

        $conn->CompleteTrans();
        return true;
    }

    public function getRelationBased()
    {
        $list = array();
        $list['TaxInvoice'] = 'Налоговая накладная';

        return $list;
    }

}
