<?php

namespace App\Entity\Doc;

use App\Entity\CAsset;
use App\Entity\Entry;
use App\Entity\AccountEntry;

/**
 *   документ ввод ОС в  эксплуатацию
 *
 */
class NAInMaintenance extends Document
{

    public function generateReport() {


        $i = 1;
        $detail = array();

        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "ca_name" => $value['ca_name'],
                "inventory" => $value['code']
            );
        }

        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "_detail" => $detail
        );

        $report = new \App\Report('nainmaintenance.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute() {


        foreach ($this->detaildata as $value) {


            $ca = CAsset::load($value['ca_id']);
            AccountEntry::AddEntry($ca->acc_code, 15, $ca->value, $this->document_id);

            $sc = new Entry($this->document_id, 15, 0 - $ca->value, 0 - 1);

            $sc->save();

            $sc = new Entry($this->document_id, $ca->acc_code, $ca->value, 1);
            $sc->setAsset($ca->ca_id);

            $sc->save();

            if (strlen($ca->code) > 0) {
                $ca->datemaint = $this->document_date;   //дата  ввода   в эксплуатацию
                $ca->value = $amount;   //начальная стоимость
                // $ca->save();
            }
        }


        return true;
    }

}
