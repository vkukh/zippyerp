<?php

namespace ZippyERP\ERP\Entity\Doc;

use ZippyERP\ERP\Entity\Entry;
use ZippyERP\ERP\Entity\SubConto;
use ZippyERP\ERP\Entity\MoneyFund;
use ZippyERP\ERP\Helper as H;

/**
 * Класс-сущность  локумент акт  о  выполненных работах
 * сторонней организацией
 *
 */
class ServiceIncome extends Document
{

    public function generateReport() {


        $i = 1;
        $total = 0;
        $detail = array();
        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "itemname" => $value['itemname'],
                "measure" => $value['measure_name'],
                "quantity" => $value['quantity'] / 1000,
                "price" => H::fm($value['price']),
                "pricends" => H::fm($value['pricends']),
                "amount" => H::fm(($value['quantity'] / 1000) * $value['price'])
            );
            $total += ($value['quantity'] / 1000) * $value['price'];
        }

        $header = array('date' => date('d.m.Y', $this->document_date),
            "customer" => $this->headerdata["customername"],
            "document_number" => $this->document_number,
            "nds" => H::fm($this->headerdata["nds"]),
            "totalnds" => $this->headerdata["totalnds"] > 0 ? H::fm($this->headerdata["totalnds"]) : 0,
            "total" => H::fm($this->headerdata["total"])
        );


        $report = new \ZippyERP\ERP\Report('serviceincome.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute() {


        $total = $this->headerdata['total'];
        $customer_id = $this->headerdata["customer"];


        if ($this->headerdata['cash'] == true) {

            $cash = MoneyFund::getCash();
            Entry::AddEntry("91", "30", $total, $this->document_id, $this->document_date);

            $sc->save();
            $sc = new SubConto($this, 30, 0 - $total);
            $sc->setMoneyfund($cash->id);
            $sc->setCustomer($this->headerdata["customer"]);

            $sc->save();
        }
        if ($this->headerdata["ccard"] == 1) {
            $bank = MoneyFund::getBankAccount();
            Entry::AddEntry("91", "31", $value['amount'], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 31, 0 - $total);
            $sc->setMoneyfund($bank->id);
            $sc->setCustomer($this->headerdata["customer"]);

            $sc->save();
        }


        if ($this->headerdata['prepayment'] == 1) {  //предоплата
            Entry::AddEntry("63", "371", $this->headerdata["total"], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 63, $this->headerdata["total"]);
            $sc->setCustomer($this->headerdata["customer"]);
            $sc->save();
            $sc = new SubConto($this, 371, 0 - $this->headerdata["total"]);
            $sc->setCustomer($this->headerdata["customer"]);
            $sc->save();
        }
        //налоговый кредит  
        if ($this->headerdata['totalnds'] > 0) {
            Entry::AddEntry("644", "703", $this->headerdata['totalnds'], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 644, $this->headerdata["totalnds"]);
            $sc->setExtCode(\ZippyERP\ERP\Consts::TAX_NDS);
            //  $sc->save();
        }
        return true;
    }

    public function getRelationBased() {
        $list = array();
        $list['TaxInvoiceIncome'] = 'Вхідна ПН';

        return $list;
    }

}
