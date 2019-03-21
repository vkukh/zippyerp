<?php

namespace App\Entity\Doc;

use App\Entity\AccountEntry;
use App\Entity\Entry;
use App\Helper as H;

/**
 * Класс-сущность  наряд
 *
 *
 */
class Task extends Document
{

    public function generateReport() {

        $i = 1;

        $detail = array();
        foreach ($this->detaildata as $value) {
            if ($value['eq_id'] > 0)
                continue;
            if ($value['employee_id'] > 0)
                continue;
            if ($value['item5_id'] > 0)
                continue;
            $detail[] = array("no" => $i++,
                "servicename" => $value['service_id'] > 0 ? $value['service_name'] : $value['itemname'],
                "quantity" => H::fqty($value['quantity']),
                "price" => H::famt($value['price']),
                "pricends" => H::famt($value['pricends']),
                "amount" => H::famt($value['quantity'] * $value['price'])
            );
        }
        $detail2 = array();
        foreach ($this->detaildata as $value) {
            if ($value['eq_id'] > 0) {
                $detail2[] = array(
                    "eq_name" => $value['eq_name'],
                    "code" => $value['code']
                );
            }
        }
        $detail3 = array();
        foreach ($this->detaildata as $value) {
            if ($value['employee_id'] > 0) {
                $detail3[] = array(
                    "emp_name" => $value['emp_name']
                );
            }
        }

        $i = 1;

        $detail5 = array();
        foreach ($this->detaildata as $value) {
            if ($value['eq_id'] > 0)
                continue;
            if ($value['employee_id'] > 0)
                continue;
            if (strlen($value['item5_id']) == 0)
                continue;
            $detail5[] = array("no" => $i++,
                "itemname" => $value['itemname'],
                "quantity" => H::fqty($value['quantity']),
                "price" => H::famt($value['price']),
                "amount" => H::famt($value['quantity'] * $value['price'])
            );
        }

        $header = array('date' => date('d.m.Y', $this->document_date),
            "usends" => H::usends(),
            "customer" => $this->customer_name,
            "startdate" => date('d.m.Y', $this->headerdata["start_date"]),
            "document_number" => $this->document_number,
            "totalnds" => $this->headerdata["totalnds"],
            "total" => $this->amount,
            "_detail" => $detail,
            "_detail2" => $detail2,
            "_detail5" => $detail5,
            "_detail3" => $detail3
        );
        $report = new \App\Report('task.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute() {
        $conn = \ZDB\DB::getConnect();
        $conn->StartTrans();
        $ndsser = 0;
        $ndsit = 0;
        foreach ($this->detaildata as $row) {

            if (strlen($row['item5_id']) == 0) {

                //комплектующие
                if ($row['item_id'] > 0) {
                    AccountEntry::AddEntry("902", $row['acc_code'], $row['price'] * $row['quantity'], $this->document_id);
                    AccountEntry::AddEntry("36", "702", $row['amount'], $this->document_id);

                    $sc = new Entry($this->document_id, $row['acc_code'], 0 - $row['amount'], 0 - $row['quantity']);

                    $sc->setStock($row['stock_id']);
                    $sc->setCustomer($this->customer_id);
                    $sc->setExtCode($row['price'] * $row['quantity']); //Для АВС 

                    $sc->save();
                    $sc = new Entry($this->document_id, "36", $row['amount'], 0 - $row['quantity']);

                    $sc->setStock($row['stock_id']);
                    $sc->setCustomer($this->customer_id);
                    $sc->save();
                    $nds = $row['quantity'] * ($row['pricends'] - $row['price']);
                    if ($nds > 0)
                        $ndsit += $nds;
                }
                //работы
                if ($row['service_id'] > 0) {
                    AccountEntry::AddEntry("36", "703", $row['amount'], $this->document_id);

                    $sc = new Entry($this->document_id, "", 0 - $row['amount'], 0 - $row['quantity']);
                    $sc->setService($row['service_id']);
                    $sc->setExtCode($row['amount']); //Для АВС 

                    $sc->setCustomer($this->customer_id);
                    $sc->save();
                    $nds = $row['quantity'] * ($row['pricends'] - $row['price']);
                    if ($nds > 0)
                        $ndsser += $nds;
                }
                //сотрудники
                if ($row['employee_id'] > 0) {
                    $sc = new Entry($this->document_id, $row['acc_code'], 0 - $row['amount'], 0 - $row['quantity']);

                    $sc->setEmployee($row['employee_id']);
                    $sc->save();
                }
            }
            //материалы 
            if ($row['item5_id'] > 0) {
                AccountEntry::AddEntry("91", $row['acc_code'], $row['amount'], $this->document_id);

                $sc = new Entry($this->document_id, $row['acc_code'], 0 - $row['amount'], 0 - $row['quantity']);
                $sc->setStock($row['stock_id']);
                $sc->save();
            }
        }   //foreach

        if ($ndsit > 0) {
            AccountEntry::AddEntry("702", "6412", $ndsit, $this->document_id);
        }
        if ($ndsser > 0) {
            AccountEntry::AddEntry("703", "6412", $ndsser, $this->document_id);
        }

        $total = $this->headerdata['total'];


        if ($this->headerdata['cash'] == true) {


            AccountEntry::AddEntry("30", "36", $total, $this->document_id);
            $sc = new Entry($this->document_id, 36, 0 - $total);
            $sc->setCustomer($this->customer_id);
            $sc->save();
        }

        if ($this->headerdata['cash'] != true) {  //предоплата или долг
            AccountEntry::AddEntry("681", "36", $total, $this->document_id);
            $sc = new Entry($this->document_id, 681, $total);
            $sc->setCustomer($this->customer_id);
            $sc->save();
            $sc = new Entry($this->document_id, 36, 0 - $total);
            $sc->setCustomer($this->customer_id);
        }
        $conn->CompleteTrans();

        return true;
    }

}
