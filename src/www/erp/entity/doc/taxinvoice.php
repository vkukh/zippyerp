<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use \ZippyERP\ERP\Util;
use \ZippyERP\ERP\Entity\Entry;
use \ZippyERP\ERP\Entity\MoneyFund;
use \ZippyERP\ERP\Helper as H;

/**
 * Класс-сущность  документ налоговая  накладая
 * 
 */
class TaxInvoice extends Document
{

    public function generateReport()
    {


        $i = 1;
        $detail = array();
        $total = 0;
        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "tovar_name" => $value['itemname'],
                "measure" => $value['measure_name'],
                "quantity" => $value['quantity'],
                "price" => H::fm($value['price']),
                "pricends" => H::fm($value['pricends']),
                "amount" => H::fm($value['quantity'] * $value['price'])
            );
            $total += $value['quantity'] * $value['price'];
        }

        $firm = \ZippyERP\System\System::getOptions("firmdetail");
        $customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata["customer"]);
        $header = array('date' => date('d.m.Y', $this->document_date),
            "firmname" => $firm['name'],
            "firmcode" => $firm['code'],
            "customername" => $customer->customer_name,
            "document_number" => $this->document_number,
            "totalnds" => H::fm($this->headerdata["totalnds"]),
            "total" => H::fm($this->headerdata["total"])
        );

        $report = new \ZippyERP\ERP\Report('taxinvoice.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {
        
    }
    
    public function export($type)
    {
     if($type==self::EX_XML_GNAU)
        return array("filename"=>"test.xml","content"=>"<test/>");
    }
    
 public  function supportedExport(){
        return array(self::EX_EXCEL,self::EX_WORD,self::EX_XML_GNAU);
    }    
}
