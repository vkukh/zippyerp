<?php

namespace ZippyERP\ERP\Entity\Doc;

use ZippyERP\ERP\Entity\Entry;
use ZippyERP\ERP\Entity\SubConto;
use ZippyERP\ERP\Helper as H;

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
                "quantity" => $value['quantity'] / 1000,
                "price" => H::fm($value['price']),
                "amount" => H::fm(($value['quantity'] / 1000) * $value['price'])
            );
        }

        //$firm = \ZippyERP\System\System::getOptions("firmdetail");
        // $customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata["customer"]);
        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "expensesname" => $this->headerdata["expensesname"]
        );

        $report = new \ZippyERP\ERP\Report('mzinmaintenance.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute() {
        $a22 = 0;
        $a15 = 0;
        $a1001 = 0;
        foreach ($this->detaildata as $value) {
            $amount = ($value['quantity'] / 1000) * $value['price'];
            //  $stock = \ZippyERP\ERP\Entity\Stock::load($value['stock_id']);
            // $os=   SubConto::getQuantity($this->document_date,15,$stock->stock_id);
            if ($value['os'] == 'true') {   //необоротный актив
                $a15 += $amount;

                // $sc = new SubConto($this, 15, 0 - $amount);
                // $sc->setStock($stock->stock_id);
                // $sc->setQuantity(0 - $value['quantity']);
                // $sc->save();


                $sc = new SubConto($this, 11, $amount);
                $sc->setAsset($value['item_id']);

                $sc->save();
            } else {
                $sc = new SubConto($this, 22, 0 - $amount);
                $a22 += $amount;
                $sc->setStock($value['stock_id']);
                $sc->setQuantity(0 - $value['quantity']);
                $sc->save();
            }

            //забалансовый
            $a1001 += $amount;
            $sc = new SubConto($this, 1001, $amount);
            $sc->setQuantity($value['quantity']);
            $sc->setAsset($value['item_id']);
            $sc->save();
        }

        Entry::AddEntry($this->headerdata["expenses"], "22", $a22, $this->document_id, $this->document_date);
        Entry::AddEntry("11", "15", $a15, $this->document_id, $this->document_date);
        Entry::AddEntry($this->headerdata["expenses"], "13", $a15, $this->document_id, $this->document_date);
        Entry::AddEntry(1001, -1, $a22, $this->document_id, $this->document_date);
        Entry::AddEntry(1001, -1, $a15, $this->document_id, $this->document_date);


        return true;
    }

    public function getRelationBased() {
        $list = array();

        return $list;
    }

}
