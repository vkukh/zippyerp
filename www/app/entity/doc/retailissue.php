<?php

namespace App\Entity\Doc;

use App\Entity\AccountEntry;
use App\Entity\Entry;
use App\Helper as H;
use App\Util;

/**
 * Класс-сущность  документ розничная  накладная
 *
 */
class RetailIssue extends Document
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
                    "msr" => $value['msr'],
                    "amount" => H::famt($value['amount'])
                );
            }
        }

        $firm = \App\System::getOptions("firmdetail");
        $customer = \App\Entity\Customer::load($this->customer_id);

        $header = array('date' => date('d.m.Y', $this->document_date),
            "_detail" => $detail,
            "firmname" => $firm['firmname'],
            "customername" => $this->customer_name . ', тел. ' . $customer->phone,
            "order" => $this->headerdata["order"],
            "document_number" => $this->document_number,
            "total" => H::famt($this->headerdata["total"])
        );



        $report = new \App\Report('retailissue.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute() {
        $total = 0;
        $ptotal = 0;

        foreach ($this->detaildata as $stock) {

            $sc = new Entry($this->document_id, "282", $stock['price'] * $item['quantity'], $item['quantity']);
            $sc->setStock($stock['stock_id']);
            $sc->setExtCode($stock['price'] * $stock['quantity']); //Для АВС 

            $sc->save();

            $total += $stock['price'] * $stock['quantity'];
            $ptotal += $stock['partion'] * $stock['quantity'];

            AccountEntry::AddEntry("902", "282", $ptotal, $this->document_id);
            AccountEntry::AddEntry("36", "702", $total, $this->document_id);

            if (H::usends()) {
                $nds = $total * H::nds(true);
                AccountEntry::AddEntry("702", "6412", $nds, $this->document_id);
            }
        }


        if ($this->headerdata['card'] != true) {


            AccountEntry::AddEntry("337", "36", $total, $this->document_id);
            $sc = new Entry($this->document_id, 36, 0 - $total);
            $sc->setExtCode($this->headerdata['store']);
            $sc->save();

            $sc = new Entry($this->document_id, 337, $total);
            $sc->setExtCode($this->headerdata['store']);
            $sc->save();
        }

        if ($this->headerdata['card'] == true) {  //оплата кредитко
            AccountEntry::AddEntry("338", "36", $total, $this->document_id);
            $sc = new Entry($this->document_id, 36, 0 - $total);
            $sc->setCustomer($this->customer_id);
            $sc->save();
            $sc = new Entry($this->document_id, 338, $total);
            $sc->setExtCode($this->headerdata['store']);
            $sc->save();
        }


        //налоговый обязательства
        if ($this->headerdata['totalnds'] > 0) {
            //  AccountEntry::AddEntry("36", "643", $this->headerdata['totalnds'], $this->document_id );
            //$sc = new Entry($this->document_id, 36, 0 -  $this->headerdata['totalnds']);
            //$sc->setCustomer($this->customer_id);
            //$sc->save();         
        }


        return true;
    }

    public function getRelationBased() {
        $list = array();
        $list['Warranty'] = 'Гарантийный талон';
        $list['RetRetIssue'] = 'Возвратная накладная';

        return $list;
    }

}
