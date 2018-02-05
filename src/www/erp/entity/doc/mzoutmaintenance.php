<?php

namespace ZippyERP\ERP\Entity\Doc;

use ZippyERP\ERP\Entity\Entry;
use ZippyERP\ERP\Entity\SubConto;

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
                "tovar_name" => $value['itemname'],
                "measure" => $value['measure_name'],
                "quantity" => $value['quantity'] / 1000
            );
        }

        //$firm = \ZippyERP\System\System::getOptions("firmdetail");
        // $customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata["customer"]);
        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number
        );

        $report = new \ZippyERP\ERP\Report('mzoutmaintenance.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute() {
        $a13 = 0;
        $a1001 = 0;
        foreach ($this->detaildata as $value) {
            $qt = \ZippyERP\ERP\Entity\SubConto::getQuantity(0, 1001, 0, 0, 0, 0, $value['item_id']);
            $am = \ZippyERP\ERP\Entity\SubConto::getAmount(0, 1001, 0, 0, 0, 0, $value['item_id']);
            $os = \ZippyERP\ERP\Entity\SubConto::getAmount(0, 11, 0, 0, 0, 0, $value['item_id']);
            $price = $am / ($qt / 1000); //средняя цена
            $amount = $price * ($value['quantity'] / 1000);
            if ($os > 0) {   //необоротный актив
                $a13 += $amount;


                $sc = new SubConto($this, 11, 0 - $amount);
                $sc->setAsset($value['item_id']);
                $sc->save();
            } else {
                
            }

            //забалансовый
            $a1001 += $amount;
            $sc = new SubConto($this, 1001, 0 - $amount);
            $sc->setQuantity(0 - $value['quantity']);
            $sc->setAsset($value['item_id']);
            $sc->save();
        }


        Entry::AddEntry("13", "11", $a13, $this->document_id, $this->document_date);
        Entry::AddEntry(-1, 1001, $a1001, $this->document_id, $this->document_date);


        return true;
    }

    public function getRelationBased() {
        $list = array();

        return $list;
    }

}
