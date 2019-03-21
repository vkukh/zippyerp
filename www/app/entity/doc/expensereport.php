<?php

namespace App\Entity\Doc;

use App\Entity\Entry;
use App\Entity\AccountEntry;
use App\Helper as H;

/**
 * Авансовый отчет
 */
class ExpenseReport extends Document
{

    public function generateReport() {

        $employee = \App\Entity\Employee::load($this->headerdata["employee"]);

        $i = 1;

        $detail = array();
        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "itemname" => $value['itemname'],
                "measure" => $value['measure_name'],
                "quantity" => H::fqty($value['quantity']),
                "price" => H::famt($value['price']),
                "pricends" => H::famt($value['pricends']),
                "amount" => H::famt($value['amount'])
            );
        }

        $header = array('date' => date('d.m.Y', $this->document_date),
            "_detail" => $detail,
            "usends" => H::usends(),
            "employee" => $employee->emp_name,
            "comment" => $this->headerdata["comment"],
            "expenseamount" => $this->headerdata["expenseamount"] > 0 ? H::famt($this->headerdata["expenseamount"]) : 0,
            "expensetype" => $this->headerdata["expensetype"],
            "storetype" => $this->headerdata["storetype"],
            "document_number" => $this->document_number,
            "totalnds" => $this->headerdata["totalnds"] > 0 ? H::famt($this->headerdata["totalnds"]) : 0,
            "total" => H::famt($this->headerdata["total"])
        );


        $report = new \App\Report('expensereport.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute() {


        $employee_id = $this->headerdata["employee"];

        $expensetype = $this->headerdata['expensetype'];
        if (count($this->detaildata) > 0) {
            foreach ($this->detaildata as $value) {
                //поиск  записи  о  товаре   на складе

                $stock = \App\Entity\Stock::getStock($this->headerdata['store'], $value['item_id'], $value['quantity'] * $value['price'], $expensetype, true);
                $sc = new Entry($this->document_id, $expensetype, $value['quantity'] * $value['price'], $value['quantity']);
                $sc->setStock($stock->stock_id);

                $sc->save();
            }


            AccountEntry::AddEntry($expensetype, "372", $this->headerdata['total'] - $this->headerdata['totalnds'], $this->document_id);

            //налоговый кредит
            if ($this->headerdata['totalnds'] > 0) {
                AccountEntry::AddEntry("644", "372", $this->headerdata['totalnds'], $this->document_id);
            }
        }
        if ($this->headerdata["expenseamount"] > 0) {
            AccountEntry::AddEntry($expensetype, "372", $this->headerdata["expenseamount"], $this->document_id);

            $sc = new Entry($this->document_id, 372, 0 - $this->headerdata["expenseamount"]);
            $sc->setEmployee($employee_id);
            $sc->save();
        }





        return true;
    }

    public function getRelationBased() {
        $list = array();
        //  $list['TaxInvoiceIncome'] = 'Вхідна ПН';

        return $list;
    }

}
