<?php

namespace App\Entity\Doc;

use App\Entity\Entry;
use App\Entity\AccountEntry;
use App\Helper as H;

/**
 *   документ списания  МЦ  с  эксплуатации
 *
 */
class MZOutMaintenance extends Document
{

    public function generateReport() {


        $i = 1;
        $detail = array();

        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "item_name" => $value['itemname'],
                "quantity" => H::fqty($value['qty'])
            );
        }

        $header = array('date' => date('d.m.Y', $this->document_date),
            "_detail" => $detail,
            "document_number" => $this->document_number
        );

        $report = new \App\Report('mzoutmaintenance.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute() {
        $a13 = 0;
        $amz = 0;
        foreach ($this->detaildata as $value) {
            $amount = $value['qty'] * $value['price'];
            if ($value['os'] == '1' || $value['os'] == 'true') {   //необоротный актив
                $a13 += $amount;

                $sc = new Entry($this->document_id, 112, 0 - $amount, 0 - $value['qty']);
                $sc->setAsset($value['item_id']);
                $sc->save();
                $sc = new Entry($this->document_id, 132, $amount, $value['qty']);

                $sc->save();
            } else {
                $sc = new Entry($this->document_id, 'МЦ', 0 - $amount, 0 - $value['qty']);
                $amz += $amount;
                $sc->setExtCode($value['item_id']);

                $sc->save();
                //забалансовый

                $sc = new Entry($this->document_id, 'МЦ', 0 - $amount, 0 - $value['qty']);

                $sc->setExtCode($value['item_id']);
                $sc->save();
            }
        }
        AccountEntry::AddEntry("132", "112", $a13, $this->document_id);
        AccountEntry::AddEntry("", 'МЦ', $amz, $this->document_id);


        return true;
    }

}
