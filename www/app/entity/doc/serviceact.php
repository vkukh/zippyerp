<?php

namespace App\Entity\Doc;

use App\Entity\AccountEntry;
use App\Entity\Entry;
use App\Helper as H;

/**
 * Класс-сущность  локумент акт  о  выполненных работах
 *
 *
 */
class ServiceAct extends Document
{

    public function generateReport() {

        $i = 1;

        $detail = array();
        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "servicename" => $value['service_name'],
                "quantity" => $value['quantity'],
                "price" => H::famt($value['price']),
                "pricends" => H::famt($value['pricends']),
                "amount" => H::famt($value['quantity'] * $value['price'])
            );
        }

        $header = array('date' => date('d.m.Y', $this->document_date),
            "_detail" => $detail,
            "usends" => H::usends(),
            "customer" => $this->customer_name,
            "document_number" => $this->document_number,
            "totalnds" => H::famt($this->headerdata["totalnds"]),
            "total" => H::famt($this->amount)
        );
        $report = new \App\Report('serviceact.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute() {
        $conn = \ZDB\DB::getConnect();
        $conn->StartTrans();

        $total = $this->headerdata['total'];

        AccountEntry::AddEntry("36", "703", $total, $this->document_id);
        foreach ($this->detaildata as $service) {
            $sc = new Entry($this->document_id, "", 0 - $service['amount'], 0);
            $sc->setService($service['service_id']);
            $sc->setExtCode($service['amount']); //Для АВС 

            $sc->setCustomer($this->customer_id);
            $sc->save();
        }


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
            $sc->save();
        }


        //налоговый обязательства
        if ($this->headerdata['totalnds'] > 0) {
            AccountEntry::AddEntry("36", "643", $this->headerdata['totalnds'], $this->document_id);
            //$sc = new Entry($this->document_id, 36, 0 -  $this->headerdata['totalnds']);
            //$sc->setCustomer($this->customer_id);
            //$sc->save();         
        }


        $conn->CompleteTrans();

        return true;
    }

}
