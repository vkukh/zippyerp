<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use \ZippyERP\ERP\Util;
use \ZippyERP\ERP\Helper as H;

/**
 * Класс-сущность  документ счета   входящего
 * 
 */
class PurchaseInvoice extends Document
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
                "amount" => H::fm($value['quantity'] * $value['price'])
            );
            $total += $value['quantity'] * $value['price'] / 100;
        }

        $bank = \ZippyERP\ERP\Entity\Bank::load($f->bank);
        $customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata["customer"]);
        $header = array('date' => date('d.m.Y', $this->document_date),
            "account" => $f->bankaccount,
            "bank" => $bank->bank_name,
            "mfo" => $bank->mfo,
            "address" => $firm['city'] . ', ' . $firm['street'],
            "customername" => $customer->customer_name,
            "document_number" => $this->document_number,
            "base" => $this->base,
            "paydate" => date('d.m.Y', $this->headerdata["payment_date"]),
            "nds" => H::fm($this->headerdata["nds"]),
            "total" => H::fm($this->headerdata["total"]));

        $report = new \ZippyERP\ERP\Report('purchaseinvoice.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {

        return true;
    }

    public function nextNumber()
    {
        $doc = Document::getFirst("meta_name='PurchaseInvoice'", "document_id ", 'desc');
        if ($doc == null)
            return '';
        $prevnumber = $doc->document_number;
        if (strlen($prevnumber) == 0)
            return '';
        $prevnumber = preg_replace('/[^0-9]/', '', $prevnumber);

        return "СВ-" . sprintf("%05d", ++$prevnumber);
    }

    public function getRelationBased()
    {
        $list = array();
        $list['GoodsReceipt'] = 'Приходная накладная';
        return $list;
    }

}
