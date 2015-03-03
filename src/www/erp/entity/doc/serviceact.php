<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use \ZippyERP\ERP\Entity\Item;
use \ZippyERP\ERP\Helper as H;

/**
 * Класс-сущность  локумент акт  о  выполненных работах
 * 
 * 
 */
class ServiceAct extends Document
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
            $total += $value['quantity'] * $value['pricends'];
        }

        $header = array('date' => date('d.m.Y', $this->document_date),
            "customer" => $customer->customer_name,
            "document_number" => $this->document_number,
            "totalnds" => H::fm($this->headerdata["totalnds"]),
            "total" => H::fm($this->headerdata["total"])
        );


        $report = new \ZippyERP\ERP\Report('serviceact.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {
         $conn = \ZCL\DB\DB::getConnect();
        $conn->StartTrans();

        $total = $this->headerdata['total'];
        $customer_id = $this->headerdata["customer"];

       // \ZippyERP\ERP\Entity\Customer::AddActivity($customer_id, 0 - $total, $this->document_id);

        if ($this->headerdata['cash'] == true) {
            $cash = MoneyFund::getCash();
            \ZippyERP\ERP\Entity\Entry::AddEntry("30", "36", $total, $this->document_id,$cash->id,$customer_id,0);
        }

        if ($this->headerdata['nds'] > 0) {
            $total = $total - $this->headerdata['nds'];
            \ZippyERP\ERP\Entity\Entry::AddEntry("36", "643", $this->headerdata['nds'], $this->document_id,$customer_id,0);
        }


        \ZippyERP\ERP\Entity\Entry::AddEntry("36", "703", $total, $this->document_id,$customer_id,0);



        $conn->CompleteTrans();


        return true;
    }

    public function getRelationBased()
    {
        $list = array();
        $list['TaxInvoice'] = 'Налоговая накладная';

        return $list;
    }

}
