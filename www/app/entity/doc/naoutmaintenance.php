<?php

namespace App\Entity\Doc;

use App\Entity\CAsset;
use App\Entity\Entry;
use App\Entity\AccountEntry;

/**
 *   документ ликвидация ОС
 *
 */
class NAOutMaintenance extends Document
{

    public function generateReport() {


        $i = 1;
        $detail = array();

        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "tovar_name" => $value['ca_name'],
                "inventory" => $value['code']
            );
        }

        //$firm = \ZippyERP\System\System::getOptions("firmdetail");
        // $customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata["customer"]);
        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "_detail" => $detail
        );

        $report = new \App\Report('naoutmaintenance.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute() {


        foreach ($this->detaildata as $value) {


            $ca = CAsset::load($value['ca_id']);
            $amount = 0;
            $a13 = '131';
            if ($ca->acc_code == '12')
                $a13 = '133';
            if (in_array($ca->acc_code, array(104, 106)))
                $a13 = '131';;


            $d = 0 - $ca->getDeprecationValue(); //уже начисленый  износ
            $cancelvalue = $ca->value - $d;

            AccountEntry::AddEntry($a13, $ca->acc_code, $d, $this->document_id);
            $sc = new Entry($this->document_id, $a13, 0 - $d);
            $sc->setAsset($ca->ca_id);
            $sc->save();

            if ($cancelvalue == 0)
                continue;


            $ca->cancelvalue = $cancelvalue;
            $ca->save(); //обновляем  в справочнике


            if ($value['editcanceltype'] == 1) { //списание как   потери
                AccountEntry::AddEntry(97, $ca->acc_code, $cancelvalue, $this->document_id);
            }
            if ($value['editcanceltype'] == 2) { //перемещение на  оснвной склад для продажи
                $common = \App\System::getOptions("common");
                if ($common['defstore'] > 0) {
                    AccountEntry::AddEntry(281, $ca->acc_code, $cancelvalue, $this->document_id);
                    $item = new \App\Entity\Item();
                    $item->itemname = $ca->ca_name . ' (списанное ОС)';
                    $item->save();

                    $stock = \App\Entity\Stock::getStock($common['defstore'], $item->item_id, $cancelvalue, 281, true);
                    $sc = new Entry($this->document_id, 281, $cancelvalue, 1);
                    $sc->setStock($stock->stock_id);


                    $sc->save();
                } else {
                    return "Не задан  в настройках основной склад";
                }
            }
        }

        return true;
    }

}
