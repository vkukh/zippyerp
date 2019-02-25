<?php

namespace App\Entity\Doc;

use App\Entity\AccountEntry;
use App\Entity\Entry;
use App\Helper as H;

/**
 * Класс-сущность  документ счет входящий
 *
 */
class PurchaseInvoice extends Document
{

    public function generateReport() {


        $i = 1;

        $detail = array();
        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "itemname" => $value['itemname'],
                "itemcode" => $value['item_code'],
                "quantity" => H::fqty($value['quantity']),
                "price" => H::famt($value['price']),
                "pricends" => H::famt($value['pricends']),
                "msr" => $value['msr'],
                "amount" => H::famt($value['amount'])
            );
        }

        $header = array('date' => date('d.m.Y', $this->document_date),
            "_detail" => $detail,
            "paydate" => date('d.m.Y', $this->headerdata["paydate"]),
            "usends" => $this->headerdata["isnds"],
            "customer_name" => $this->customer_name,
            "document_number" => $this->document_number,
            "totalnds" => H::famt($this->headerdata["totalnds"]),
            "total" => H::famt($this->headerdata["total"])
        );


        $report = new \App\Report('goodsreceipt.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute() {




        return true;
    }

    public function getRelationBased() {
        $list = array();

        $list['GoodsReceipt'] = 'Приходная накладная';

        return $list;
    }

}
