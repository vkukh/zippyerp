<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use \ZippyERP\ERP\Util;
use \ZippyERP\ERP\Entity\Employee;
use \ZippyERP\ERP\Entity\Entry;
use \ZippyERP\ERP\Entity\SubConto;
use \ZippyERP\ERP\Entity\MoneyFund;
use \ZippyERP\ERP\Helper as H;

/**
 *   документ выплаита зарплаты
 *
 */
class OutSalary extends Document
{

    public function generateReport()
    {


        $i = 1;
        $detail = array();

        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "emp_name" => $value['fullname'],
                "payed" => H::fm($value['payed']),
                "amount" => H::fm($value['amount'])
            );
        }

        $mlist = H::getMonth();

        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "sdate" => $mlist[$this->headerdata["month"]] . ' ' . $this->headerdata["year"],
            "expensesname" => $this->headerdata["expensesname"]
        );

        $report = new \ZippyERP\ERP\Report('outsalary.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {
        $a66 = 0;

        foreach ($this->detaildata as $emp) {

            $sc = new SubConto($this, 66, $emp['payed']);
            $a66 += $emp['payed'];
            $sc->setEmployee($emp['employee_id']);
            $sc->save();
        }
        $sc = new SubConto($this, 30, 0 - $a66);

        $sc->setMoneyfund(MoneyFund::getCash()->id);
        $sc->setExtCode(\ZippyERP\ERP\Consts::TYPEOP_CASH_SALARY);
        $sc->save();

        Entry::AddEntry("66", "30", $a66, $this->document_id, $this->document_date);



        return true;
    }

    public function getRelationBased()
    {
        $list = array();

        return $list;
    }

}
