<?php

namespace App\Entity\Doc;

use App\Entity\Entry;
use App\Entity\MoneyFund;
use App\Entity\SubConto;
use App\Helper as H;

/**
 *   документ выплаита зарплаты
 *
 */
class OutSalary extends Document
{

    public function generateReport() {


        $i = 1;
        $detail = array();

        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "emp_name" => $value['fullname'],
                "payed" => H::famt($value['payed']),
                "amount" => H::famt($value['amount'])
            );
        }

        $mlist = H::getMonth();

        $header = array('date' => date('d.m.Y', $this->document_date),
            "_detail" => $detail,
            "document_number" => $this->document_number,
            "sdate" => $mlist[$this->headerdata["month"]] . ' ' . $this->headerdata["year"],
            "expensesname" => $this->headerdata["expensesname"]
        );

        $report = new \App\Report('outsalary.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute() {
        $a66 = 0;

        foreach ($this->detaildata as $emp) {

            $sc = new SubConto($this, 66, $emp['payed']);
            $a66 += $emp['payed'];
            $sc->setEmployee($emp['employee_id']);
            $sc->save();
        }
        $sc = new SubConto($this, 30, 0 - $a66);

        $sc->setMoneyfund(MoneyFund::getCash()->id);
        $sc->setExtCode(H::TYPEOP_CASH_SALARY);
        $sc->save();

        Entry::AddEntry("66", "30", $a66, $this->document_id, $this->document_date);


        return true;
    }

    public function getRelationBased() {
        $list = array();

        return $list;
    }

}
