<?php

namespace ZippyERP\ERP\Entity\Doc;

use ZippyERP\ERP\Entity\Entry;
use ZippyERP\ERP\Entity\SubConto;
use ZippyERP\ERP\Helper as H;
use ZippyERP\System\System;

/**
 *   документ начисление зарплаты
 *
 */
class InSalary extends Document
{

    public function generateReport()
    {

        $totamount = $totmil = $totecb = $totndfl = 0;

        $i = 1;
        $detail = array();

        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "emp_name" => $value['shortname'],
                "salary" => H::fm($value['salary']),
                "vacation" => H::fm($value['vacation']),
                "sick" => H::fm($value['sick']),
                "ndfl" => H::fm($value['taxfl']),
                "ecb" => H::fm($value['taxecb']),
                "mil" => H::fm($value['taxmil']),
                "amount" => H::fm($value['amount'])
            );
            $totamount += $value['amount'];
            $totndfl += $value['taxfl'];
            $totecb += $value['taxecb'];
            $totmil += $value['taxmil'];
        }

        $mlist = H::getMonth();

        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "totamount" => H::fm($totamount),
            "totndfl" => H::fm($totndfl),
            "totecb" => H::fm($totecb),
            "totmil" => H::fm($totmil),
            "sdate" => $mlist[$this->headerdata["month"]] . ' ' . $this->headerdata["year"]
        );

        $report = new \ZippyERP\ERP\Report('insalary.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {
        $tax = System::getOptions("tax");

        $a66 = 0;
        $aexp = array(); // счета  затрат

        $a641 = 0;
        $a642 = 0;
        $a65 = 0;


        foreach ($this->detaildata as $emp) {
            $salary = $emp['salary'] + $emp['vacation'] + $emp['sick'];
            if ($salary > 0) {
                $sc = new SubConto($this, 66, 0 - $salary);
                $a66 += $salary;
                $a65 += $emp['taxecb'];
                $aexp[$emp['exptype']] += $salary;

                $sc->setEmployee($emp['employee_id']);

                $sc->save();
            }

            if ($emp['taxfl'] > 0) {
                $sc = new SubConto($this, 66, $emp['taxfl']);
                $a66 -= $emp['taxfl'];
                $sc->setEmployee($emp['employee_id']);
                $a641 += $emp['taxfl'];
                $sc->setExtCode(\ZippyERP\ERP\Consts::TAX_NDFL);
                $sc->save();
            }
            if ($emp['taxmil'] > 0) {
                $sc = new SubConto($this, 66, $emp['taxmil']);
                $a66 -= $emp['taxmil'];
                $sc->setEmployee($emp['employee_id']);
                $a642 += $emp['taxmil'];
                $sc->setExtCode(\ZippyERP\ERP\Consts::TAX_MIL);
                $sc->save();
            }
        }

        //затраты
        foreach ($aexp as $expacc => $value) {
            Entry::AddEntry($expacc, "66", $value, $this->document_id, $this->document_date);
        }

        //ЕСБ
        if ($a65 > 0) {
            $sc = new SubConto($this, 65, 0 - $a65);
            $sc->setExtCode(\ZippyERP\ERP\Consts::TAX_ECB);
            $sc->save();

            Entry::AddEntry("91", "65", $a65, $this->document_id, $this->document_date);
        }

        //НДФЛ
        if ($a641 > 0) {
            $sc = new SubConto($this, 641, 0 - $a641);
            $sc->setExtCode(\ZippyERP\ERP\Consts::TAX_NDFL);
            $sc->save();

            Entry::AddEntry("66", "641", $a641, $this->document_id, $this->document_date);
        }

        //Военный сбор
        if ($a642 > 0) {
            $sc = new SubConto($this, 642, 0 - $a642);
            $sc->setExtCode(\ZippyERP\ERP\Consts::TAX_MIL);
            $sc->save();

            Entry::AddEntry("66", "642", $a642, $this->document_id, $this->document_date);
        }


        return true;
    }

    public function getRelationBased()
    {
        $list = array();
        $list['OutSalary'] = 'Виплата зарплати';

        return $list;
    }

}
