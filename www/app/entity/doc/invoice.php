<?php

namespace App\Entity\Doc;

use App\Entity\AccountEntry;
use App\Entity\Entry;
use App\Helper as H;
use App\Util;

/**
 * Класс-сущность  документ расходная  накладая
 *
 */
class Invoice extends Document
{

    public function generateReport() {


        $i = 1;
        $detail = array();

        foreach ($this->detaildata as $value) {

            if (isset($detail[$value['item_id']])) {
                $detail[$value['item_id']]['quantity'] += $value['quantity'];
            } else {
                $detail[] = array("no" => $i++,
                    "tovar_name" => $value['itemname'],
                    "tovar_code" => $value['item_code'],
                    "quantity" => H::fqty($value['quantity']),
                    "price" => H::famt($value['price']),
                    "pricends" => H::famt($value['pricends']),
                    "msr" => $value['msr'],
                    "amount" => H::famt($value['amount'])
                );
            }
        }

        $firm = \App\System::getOptions("firmdetail");

        $header = array('date' => date('d.m.Y', $this->document_date),
            "_detail" => $detail,
            "paydate" => date('d.m.Y', $this->headerdata["paydate"]),
            "usends" => H::usends(),
            "firmname" => $firm['firmname'],
            "customername" => $this->customer_name,
            "document_number" => $this->document_number,
            "totalnds" => H::famt($this->headerdata["totalnds"]),
            "total" => H::famt($this->headerdata["total"])
        );

        $report = new \App\Report('invoice.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute() {
        return true;
    }

    public function getRelationBased() {
        $list = array();

        $list['GoodsIssue'] = 'Расходная  накладная';

        return $list;
    }

}
