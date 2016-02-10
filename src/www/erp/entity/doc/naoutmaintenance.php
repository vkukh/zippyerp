<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use \ZippyERP\ERP\Util;
use \ZippyERP\ERP\Entity\Item;
use \ZippyERP\ERP\Entity\CapitalAsset;
use \ZippyERP\ERP\Entity\Stock;
use \ZippyERP\ERP\Entity\Entry;
use \ZippyERP\ERP\Entity\SubConto;
use \ZippyERP\ERP\Entity\MoneyFund;
use \ZippyERP\ERP\Helper as H;

/**
 *   документ ликвидация ОС
 *
 */
class NAOutMaintenance extends Document
{

    public function generateReport()
    {


        $i = 1;
        $detail = array();

        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "tovar_name" => $value['itemname'],
                "inventory" => $value['inventory']
            );
        }

        $firm = \ZippyERP\System\System::getOptions("firmdetail");
        // $customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata["customer"]);
        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number
        );

        $report = new \ZippyERP\ERP\Report('naoutmaintenance.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {


        foreach ($this->detaildata as $value) {


            $ca = CapitalAsset::load($value['item_id']);
            $d = $ca->getDeprecationValue(); //уже начисленый  износ
            $cancelvalue = $ca->value - $d;

            Entry::AddEntry(13, $ca->typeos, $d, $this->document_id, $this->document_date);
            $sc = new SubConto($this, 13, 0 - $d);
            $sc->setAsset($ca->item_id);
            $sc->save();

            if ($cancelvalue == 0)
                continue;


            $ca->cancelvalue = $cancelvalue;
            $ca->save(); //обновляем  в справлчнике


            if ($value['editcanceltype'] == 1) { //списание как   потери
                Entry::AddEntry(97, $ca->typeos, $cancelvalue, $this->document_id, $this->document_date);
            }
            if ($value['editcanceltype'] == 2) { //перемещение на  оснвной склад для продажи
                $common = \ZippyERP\System\System::getOptions("common");
                if ($common['basestore'] > 0) {
                    Entry::AddEntry(201, $ca->typeos, $cancelvalue, $this->document_id, $this->document_date);
                    $stock = \ZippyERP\ERP\Entity\Stock::getStock($common['basestore'], $ca->item_id, $cancelvalue, true);
                    $sc = new SubConto($this, 201, $cancelvalue);
                    $sc->setStock($stock->stock_id);
                    $sc->setQuantity(1000);

                    $sc->save();
                } else {
                    return "Не задан  в настройках основной склад";
                }
            }
        }




        return true;
    }

    public function getRelationBased()
    {
        $list = array();

        return $list;
    }

}
