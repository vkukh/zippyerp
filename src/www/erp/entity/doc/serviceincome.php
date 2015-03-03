<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use \ZippyERP\ERP\Entity\Item;
use \ZippyERP\ERP\Helper as H;

/**
 * Класс-сущность  локумент акт  о  выполненных работах
 * сторонней организацией
 * 
 */
class ServiceIncome extends Document
{

    public function generateReport()
    {

        $customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata["customer"]);

        $i = 1;
        $total = 0;
        $detail = array();
        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "itemname" => $value['itemname'],
                "measure" => $value['measure_name'],
                "quantity" => $value['quantity'],
                "price" => H::fm($value['price']),
                "pricends" => H::fm($value['pricends']),
                "amount" => H::fm($value['quantity'] * $value['price'])
            );
            $total += $value['quantity'] * $value['price'];
        }

        $header = array('date' => date('d.m.Y', $this->document_date),
            "customer" => $customer->customer_name,
            "document_number" => $this->document_number,
            "nds" => H::fm($this->headerdata["nds"]),
            "totalnds" => H::fm($this->headerdata["totalnds"]),
            "total" => H::fm($this->headerdata["total"])
        );


        $report = new \ZippyERP\ERP\Report('serviceincome.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {


        $total = $this->headerdata['total'];
        $customer_id = $this->headerdata["customer"];

      //  \ZippyERP\ERP\Entity\Customer::AddActivity($customer_id, 0 - $total, $this->document_id);

        if ($this->headerdata['cash'] == true) {
            $cash = MoneyFund::getCash();
            //MoneyFund::AddActivity($cash->id, 0 - $this->headerdata['total'], $this->document_id);

            \ZippyERP\ERP\Entity\Entry::AddEntry("63", "30", $total, $this->document_id,$customer_id,$cash->id);
        }

        if ($this->headerdata['totalnds'] > 0) {
            $total = $total - $this->headerdata['totalnds'];
            \ZippyERP\ERP\Entity\Entry::AddEntry("644", "63", $this->headerdata['totalnds'], $this->document_id,0,$customer_id);
        }

        \ZippyERP\ERP\Entity\Entry::AddEntry("91", "63", $total, $this->document_id,0,$customer_id);


        return true;
    }

    public function getRelationBased()
    {
        $list = array();
        $list['TaxInvoiceIncome'] = 'Входящая НН';

        return $list;
    }

}
