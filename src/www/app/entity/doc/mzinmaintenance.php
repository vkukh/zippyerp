<?php

namespace App\Entity\Doc;

use App\Entity\Entry;
use App\Entity\AccountEntry;
use App\Helper as H;

/**
 *   документ ввод МЦ в  эксплуатацию
 *
 */
class MZInMaintenance extends Document
{

    public function generateReport() {


        $i = 1;
        $detail = array();

        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "tovar_name" => $value['itemname'],
                "measure" => $value['measure_name'],
                "quantity" => $value['quantity'],
                "price" => H::famt($value['price']),
                "amount" => H::famt(($value['quantity'] ) * $value['price'])
            );
        }

        //$firm = \ZippyERP\System\System::getOptions("firmdetail");
        // $customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata["customer"]);
        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "expensesname" => $this->headerdata["expensesname"],
            "_detail" => $detail
        );

        $report = new \App\Report('mzinmaintenance.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute() {
        $a22 = 0;
        $a15 = 0;

        foreach ($this->detaildata as $value) {
            $amount = $value['quantity'] * $value['price'];
            if ($value['os'] == '1' || $value['os'] == 'true') {   //необоротный актив
                $a15 += $amount;



                $sc = new Entry($this->document_id, 112, $amount, $value['quantity']);
                $sc->setAsset($value['ca_id']);

                $sc->save();
            } else {
                $sc = new Entry($this->document_id, 22, 0 - $amount, 0 - $value['quantity']);
                $a22 += $amount;
                $sc->setStock($value['stock_id']);

                $sc->save();
                //забалансовый

                $sc = new Entry($this->document_id, 'МЦ', $amount, $value['quantity']);

                $sc->setExtCode($value['item_id']);
                $sc->save();
            }
        }

        AccountEntry::AddEntry($this->headerdata["expenses"], "22", $a22, $this->document_id);
        AccountEntry::AddEntry("112", "15", $a15, $this->document_id);
        AccountEntry::AddEntry($this->headerdata["expenses"], "132", $a15, $this->document_id);
        AccountEntry::AddEntry('МЦ', "", $a22, $this->document_id);



        return true;
    }

}
