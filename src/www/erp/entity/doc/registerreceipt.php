<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use \ZippyERP\ERP\Util;
use \ZippyERP\ERP\Entity\Entry;
use \ZippyERP\ERP\Entity\SubConto;
use \ZippyERP\ERP\Entity\MoneyFund;
use \ZippyERP\ERP\Helper as H;

/**
 * Класс-сущность  документ розничная  накладая
 *
 */
class RegisterReceipt extends Document
{

    public function generateReport()
    {


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

        $firm = \ZippyERP\System\System::getOptions("firmdetail");
        $header = array('date' => date('d.m.Y', $this->document_date),
            "firmname" => $firm['name'],
            "firmcode" => $firm['code'],
            "kassa" => $this->headerdata["kassa"],
            "return" => $this->headerdata["return"],
            "document_number" => $this->document_number,
            "total" => H::fm($this->headerdata["total"]),
            "totalnds" => H::fm($this->headerdata["totalnds"])
        );

        $report = new \ZippyERP\ERP\Report('registerreceipt.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {
        $return = $this->headerdata['return'];


        $ret = 0;
        $cost = 0;
        foreach ($this->detaildata as $value) {
            $stock = \ZippyERP\ERP\Entity\Stock::load($value['stock_id']);
            $ret = $ret + $stock->price - $stock->partion;
            $cost = $cost + $stock->partion;

            $sc = new SubConto($this, 282, $return == 1 ? 0 - ($value['quantity'] / 1000) * $stock->price : ($value['quantity'] / 1000) * $stock->price);
            $sc->setStock($stock->stock_id);
            $sc->setQuantity($return == 1 ? 0 - $value['quantity'] : $value['quantity']);
            $sc->setExtCode($stock->price - $stock->partion);  //Для АВС
            $sc->save();
        }

        if ($return == 1) {  //возврат
            $ret = 0 - $ret;
            $cost = 0 - $cost;
            $this->headerdata['totalnds'] = 0 - $this->headerdata['totalnds'];
        }

        // списываем  наценку
        Entry::AddEntry("285", "282", $ret, $this->document_id, $this->document_date);
        $sc = new SubConto($this, 285, $ret);
        $sc->setExtCode($this->headerdata["store"]);

        $sc->save();
        // себестоимость реализации
        Entry::AddEntry("902", "282", $cost, $this->document_id, $this->document_date);

        //налоговые  обязательства
        if ($this->headerdata['totalnds'] > 0) {
            Entry::AddEntry("643", "702", $this->headerdata['totalnds'], $this->document_id, $this->document_date);
        }
        return true;
    }

    public function getRelationBased()
    {
        $list = array();
        $list['Warranty'] = 'Гарантийный талон';

        return $list;
    }

}
