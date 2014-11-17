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
                "tovar_name" => $value['tovarname'],
                "measure" => $value['measure_name'],
                "quantity" => $value['quantity'],
                "price" => H::fm($value['price']),
                "amount" => H::fm($value['quantity'] * $value['price'])
            );
            $total += $value['quantity'] * $value['price'];
        }

        $header = array('date' => date('d.m.Y', $this->document_date),
            "customer" => $customer->customer_name,
            "document_number" => $this->document_number,
            "nds" => H::fm($this->headerdata["nds"]),
            "total" => H::fm($this->headerdata["total"])
        );


        $report = new \ZippyERP\ERP\Report('serviceincome.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {

        foreach ($this->detaildata as $value) {
            //поиск  записи  о  товаре   на складе

            $stock = \ZippyERP\ERP\Entity\Stock::getStock($this->headerdata['store'], $value['item_id'], $value['price'], true);
            $stock->updateStock($value['quantity'], $this->document_id, strlen($value['serial_number']) > 0 ? array($value['serial_number']) : array());
        }
        $total = $this->headerdata['total'];
        $customer_id = $this->headerdata["customer"];

        \ZippyERP\ERP\Entity\Customer::AddActivity($customer_id, 0 - $total, $this->document_id);

        if ($this->headerdata['cash'] == true) {
            \ZippyERP\ERP\Entity\Entry::AddEntry("63", "30", $total, $this->document_id, "Оплата  поставщику  наличныыми");
        }

        if ($this->headerdata['nds'] > 0) {
            $total = $total - $this->headerdata['nds'];
            \ZippyERP\ERP\Entity\Entry::AddEntry("644", "63", $this->headerdata['nds'], $this->document_id, "Налоговый кредит");
        }


        \ZippyERP\ERP\Entity\Entry::AddEntry("91", "63", $total, $this->document_id);






        return true;
    }

    public function getRelationBased()
    {
        $list = array();
        $list['TaxInvoiceIncome'] = 'Входящая НН';

        return $list;
    }

}
