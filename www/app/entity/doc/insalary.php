<?php

namespace App\Entity\Doc;

use App\Entity\Entry;
use App\Entity\AccountEntry;
use App\Helper as H;
use App\System;

/**
 *   документ начисление зарплаты
 *
 */
class InSalary extends Document
{

    public function generateReport() {

        $totamount = $totmil = $totecb = $totndfl = 0;

        $i = 1;
        $detail = array();

        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "emp_name" => $value['emp_name'],
                "salary" => H::famt($value['salary']),
                "vacation" => H::famt($value['vacation']),
                "sick" => H::famt($value['sick']),
                "ndfl" => H::famt($value['taxfl']),
                "ecb" => H::famt($value['taxecb']),
                "mil" => H::famt($value['taxmil']),
                "amount" => H::famt($value['amount'])
            );
            $totamount += $value['amount'];
            $totndfl += $value['taxfl'];
            $totecb += $value['taxecb'];
            $totmil += $value['taxmil'];
        }

        $mlist = H::getMonth();

        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "_detail" => $detail,
            "totamount" => H::famt($totamount),
            "totndfl" => H::famt($totndfl),
            "totecb" => H::famt($totecb),
            "totmil" => H::famt($totmil),
            "sdate" => $mlist[$this->headerdata["month"]] . ' ' . $this->headerdata["year"]
        );

        $report = new \App\Report('insalary.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute() {
        $tax = System::getOptions("tax");

        $a66 = 0;
        $aexp = array(); // счета  затрат

        $a641 = 0;
        $a642 = 0;
        $a65 = 0;


        foreach ($this->detaildata as $emp) {
            $salary = $emp['salary'] + $emp['vacation'] + $emp['sick'];
            if ($salary > 0) {
                $sc = new Entry($this->document_id, 66, 0 - $salary);
                $a66 += $salary;
                $a65 += $emp['taxecb'];
                $aexp[$emp['expense']] += $salary;

                $sc->setEmployee($emp['employee_id']);

                $sc->save();
            }

            if ($emp['taxfl'] > 0) {
                $sc = new Entry($this->document_id, 66, $emp['taxfl']);
                $a66 -= $emp['taxfl'];
                $sc->setEmployee($emp['employee_id']);
                $a641 += $emp['taxfl'];
                $sc->setExtCode(H::TAX_NDFL);
                $sc->save();
            }
            if ($emp['taxmil'] > 0) {
                $sc = new Entry($this->document_id, 66, $emp['taxmil']);
                $a66 -= $emp['taxmil'];
                $sc->setEmployee($emp['employee_id']);
                $a642 += $emp['taxmil'];
                $sc->setExtCode(H::TAX_MIL);
                $sc->save();
            }
        }

        //затраты
        foreach ($aexp as $expacc => $value) {
            AccountEntry::AddEntry($expacc, "66", $value, $this->document_id);
        }

        //ЕСБ
        if ($a65 > 0) {
            $sc = new Entry($this->document_id, 65, 0 - $a65);
            $sc->setExtCode(H::TAX_ECB);
            $sc->save();

            AccountEntry::AddEntry("91", "65", $a65, $this->document_id);
        }

        //НДФЛ
        if ($a641 > 0) {
            $sc = new Entry($this->document_id, 641, 0 - $a641);
            $sc->setExtCode(H::TAX_NDFL);
            $sc->save();

            AccountEntry::AddEntry("66", "641", $a641, $this->document_id);
        }

        //Военный сбор
        if ($a642 > 0) {
            $sc = new Entry($this->document_id, 642, 0 - $a642);
            $sc->setExtCode(H::TAX_MIL);
            $sc->save();

            AccountEntry::AddEntry("66", "642", $a642, $this->document_id, $this->document_date);
        }


        return true;
    }

    public function getRelationBased() {
        $list = array();
        $list['OutSalary'] = 'Выплата зарплаты';

        return $list;
    }

}
