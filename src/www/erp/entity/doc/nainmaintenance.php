<?php

namespace ZippyERP\ERP\Entity\Doc;

use ZippyERP\ERP\Entity\CapitalAsset;
use ZippyERP\ERP\Entity\Entry;
use ZippyERP\ERP\Entity\SubConto;

/**
 *   документ ввод ОС в  эксплуатацию
 *
 */
class NAInMaintenance extends Document
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

        //$firm = \ZippyERP\System\System::getOptions("firmdetail");
        // $customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata["customer"]);
        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number
        );

        $report = new \ZippyERP\ERP\Report('nainmaintenance.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {


        foreach ($this->detaildata as $value) {
            $amount = $value['value'];

            $ca = CapitalAsset::load($value['item_id']);
            Entry::AddEntry($ca->typeos, 15, $amount, $this->document_id, $this->document_date);

            $sc = new SubConto($this, 15, 0 - $amount);
            $sc->setStock($value['stock_id']);
            $sc->setQuantity(0 - $value['quantity']);
            //  $sc->save();
            $sc = new SubConto($this, $ca->typeos, $amount);
            $sc->setAsset($ca->item_id);
            $sc->setQuantity($value['quantity']);
            $sc->save();

            if (strlen($ca->inventory) > 0) {
                $ca->datemaint = $this->document_date;   //дата  ввода   в эксплуатацию
                $ca->value = $amount;   //начальная стоимость
                $ca->save();
            }

            /*
              if ($ca->typeos == 11) {
              //списываем сразу

              Entry::AddEntry($ca->expenses, 13, $value['value'], $this->document_id, $this->document_date);
              $sc = new SubConto($this, 13, $value['value']);
              $sc->setAsset($ca->item_id);

              $sc->save();
              }
             */
        }


        return true;
    }

    public function getRelationBased()
    {
        $list = array();

        return $list;
    }

}
