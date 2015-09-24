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
class RetailIssue extends Document
{

    public function generateReport()
    {


        $i = 1;
        $detail = array();

        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "tovar_name" => $value['itemname'],
                "measure" => $value['measure_name'],
                "quantity" => $value['quantity']/1000,
                "price" => H::fm($value['price']),
                "amount" => H::fm(($value['quantity']/1000) * $value['price'])
            );
        }

        $firm = \ZippyERP\System\System::getOptions("firmdetail");
       // $customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata["customer"]);
        $header = array('date' => date('d.m.Y', $this->document_date),
            "firmname" => $firm['name'],
            "firmcode" => $firm['code'],
            "customername" => $this->headerdata["customername"],
            "document_number" => $this->document_number,
            "total" => H::fm($this->headerdata["total"]),
            "totalnds" => H::fm($this->headerdata["totalnds"]),
            "summa" => Util::ucfirst(Util::money2str($total + $this->headerdata["nds"] / 100, '.', ''))
        );

        $report = new \ZippyERP\ERP\Report('retailissue.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {
        $ret = 0;
        $cost = 0;
        foreach ($this->detaildata as $value) {
            $stock = \ZippyERP\ERP\Entity\Stock::load($value['stock_id']);
            $ret = $ret + $stock->price - $stock->partion;
            $cost = $cost + $stock->partion;

                 $sc = new SubConto($this->document_id, $this->document_date, 282);
                 $sc->setStock($stock->stock_id) ;
                 $sc->setQuantity( 0-$value['quantity']) ;
                 $sc->save();

        }
        // списываем  наценку
        Entry::AddEntry("285", "282", $ret, $this->document_id, $this->document_date);
            $sc = new SubConto($this->document_id, $this->document_date, 285);
            $sc->setExtCode($this->headerdata["store"]);
            $sc->setAmount(  $ret);
            $sc->save();
        // себестоимость реализации
        Entry::AddEntry("902", "282", $cost, $this->document_id, $this->document_date);

        //налоговые  обязательства
        if ($this->headerdata['totalnds'] > 0) {
            Entry::AddEntry("702", "643", $this->headerdata['totalnds'], $this->document_id, $this->document_date);
        }
        return true;
    }

    public function getRelationBased()
    {
        $list = array();
        $list['Warranty'] = 'Гарантийный талон';
        $list['ReturnRetailIssue'] = 'Возвратнаая накладная';

        return $list;
    }

}
