<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;

/**
 * Класс-сущность  локумент приходная  накладая
 * 
 */
class SalesInvoice extends Document
{

    public function generateReport()
    {
        $i = 1;
        $detail = array();
        $total = 0;
        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "tovar_name" => $value['tovarname'],
                "measure" => $value['measure_name'],
                "serial_number" => $value['serial_number'],
                "quantity" => $value['quantity'],
                "price" => number_format($value['price'] / 100, 2),
                "amount" => number_format($value['quantity'] * $value['price'] / 100, 2)
            );
            $total += $value['quantity'] * $value['price'] / 100;
        }

        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "nds" => number_format($this->headerdata["nds"] / 100, 2),
            "total" => number_format($total + $this->headerdata["nds"] / 100, 2)
        );

        $reportgen = new \ZCL\RepGen\RepGen(_ROOT . 'templates/erp/templates/salesinvoice.html', $header);




        $html = $reportgen->generateSimple($detail);

        return $html;
    }

    public function Execute()
    {
        foreach ($this->detaildata as $value) {
            $stock = \ZippyERP\ERP\Entity\Stock::getStock($this->headerdata['store'], $value['item_id'], $value['partion'], true);
            $stock->updateStock(0 - $value['quantity'],  $this->document_id, strlen($value['serial_number']) > 0 ? array($value['serial_number']) : array());
        }
        return true;
    }

}
