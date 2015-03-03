<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use \ZippyERP\ERP\Util;
use \ZippyERP\ERP\Helper as H;

/**
 * Класс-сущность  документ счет-фактура
 * 
 */
class Invoice extends Document
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

        $firm = \ZippyERP\System\System::getOptions("firmdetail");

        $f = \ZippyERP\ERP\Entity\MoneyFund::findOne('ftype = 1');
        $bank = \ZippyERP\ERP\Entity\Bank::load($f->bank);
        $customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata["customer"]);
        $header = array('date' => date('d.m.Y', $this->document_date),
            "firmname" => $firm['name'],
            "firmcode" => $firm['code'],
            "account" => $f->bankaccount,
            "bank" => $bank->bank_name,
            "mfo" => $bank->mfo,
            "address" => $firm['city'] . ', ' . $firm['street'],
            "customername" => $customer->customer_name,
            "document_number" => $this->document_number,
            "base" => $this->base,
            "paydate" => date('d.m.Y', $this->headerdata["payment_date"]),
            "nds" => H::fm($this->headerdata["nds"]),
            "total" => H::fm($total),
            "totalnds" => H::fm($total + $this->headerdata["nds"]),
            "summa" => Util::ucfirst(Util::money2str($total + $this->headerdata["nds"] / 100, '.', ''))
        );

        $report = new \ZippyERP\ERP\Report('invoice.tpl');

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
        $list['GoodsIssue'] = 'Расходная накладная';
        $list['ServiceAct'] = 'Акт выполненных работ';
        $list['TaxInvoice'] = 'Налоговая  накладная';
        return $list;
    }

}
