<?php

namespace ZippyERP\ERP\Entity\Doc;

use ZippyERP\ERP\Entity\Entry;
use ZippyERP\ERP\Entity\SubConto;
use ZippyERP\ERP\Helper as H;

/**
 * Авансовый отчет
 */
class ExpenseReport extends Document
{

    public function generateReport() {

        $employee = \ZippyERP\ERP\Entity\Employee::load($this->headerdata["employee"]);

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

        $header = array('date' => date('d.m.Y', $this->document_date),
            "employee" => $employee->shortname,
            "comment" => $this->headerdata["comment"],
            "expenseamount" => $this->headerdata["expenseamount"] > 0 ? H::fm($this->headerdata["expenseamount"]) : 0,
            "expensetype" => $this->headerdata["expensetype"],
            "storetype" => $this->headerdata["storetype"],
            "document_number" => $this->document_number,
            "totalnds" => $this->headerdata["totalnds"] > 0 ? H::fm($this->headerdata["totalnds"]) : 0,
            "total" => H::fm($this->headerdata["total"])
        );


        $report = new \ZippyERP\ERP\Report('expensereport.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute() {
        $conn = \ZDB\DB::getConnect();
        $conn->StartTrans();

        $employee_id = $this->headerdata["employee"];

        $expensetype = $this->headerdata['expensetype'];
        if (count($this->detaildata) > 0) {
            foreach ($this->detaildata as $value) {
                //поиск  записи  о  товаре   на складе

                $stock = \ZippyERP\ERP\Entity\Stock::getStock($this->headerdata['store'], $value['item_id'], $value['price'], true);
                $sc = new SubConto($this, $expensetype, ($value['quantity'] / 1000) * $stock->price);
                $sc->setStock($stock->stock_id);
                $sc->setQuantity($value['quantity']);
                $sc->save();
            }


            Entry::AddEntry($expensetype, "372", $this->headerdata['total'] - $this->headerdata['totalnds'], $this->document_id, $this->document_date);

            //налоговый кредит
            if ($this->headerdata['totalnds'] > 0) {
                Entry::AddEntry("644", "641", $this->headerdata['totalnds'], $this->document_id, $this->document_date);
            }
        }
        if ($this->headerdata["expenseamount"] > 0) {
            Entry::AddEntry($expensetype, "372", $this->headerdata["expenseamount"], $this->document_id, $this->document_date);

            $sc = new SubConto($this, 372, 0 - $this->headerdata["expenseamount"]);
            $sc->setEmployee($employee_id);
            $sc->save();
        }


        $conn->CompleteTrans();


        return true;
    }

    public function getRelationBased() {
        $list = array();
        //  $list['TaxInvoiceIncome'] = 'Вхідна ПН';

        return $list;
    }

}
