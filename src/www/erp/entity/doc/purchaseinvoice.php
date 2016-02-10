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

        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "itemname" => $value['itemname'],
                "measure" => $value['measure_name'],
                "quantity" => $value['quantity'] / 1000,
                "price" => H::fm($value['price']),
                "pricends" => H::fm($value['pricends']),
                "amount" => H::fm($value['amount'])
            );
        }

        $bank = \ZippyERP\ERP\Entity\Bank::load($f->bank);
        //$customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata["customer"]);
        $header = array('date' => date('d.m.Y', $this->document_date),
            "account" => $f->bankaccount,
            "bank" => $bank->bank_name,
            "mfo" => $bank->mfo,
            "address" => $firm['city'] . ', ' . $firm['street'],
            "customername" => $this->headerdata["customername"],
            "document_number" => $this->document_number,
            "base" => $this->base,
            "paydate" => date('d.m.Y', $this->headerdata["payment_date"]),
            "totalnds" => $this->headerdata["totalnds"] > 0 ? H::fm($this->headerdata["totalnds"]) : 0,
            "total" => H::fm($this->headerdata["total"]));

        $report = new \ZippyERP\ERP\Report('purchaseinvoice.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {

        return true;
    }

    public function getRelationBased()
    {
        $list = array();
        $list['GoodsReceipt'] = 'Приходная накладная';
        $list['TaxInvoiceIncome'] = 'Входящая НН';
        $list['ServiceIncome'] = 'Оказанные услуги';
        return $list;
    }

}
