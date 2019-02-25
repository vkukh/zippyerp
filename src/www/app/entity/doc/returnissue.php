<?php

namespace App\Entity\Doc;

use App\Entity\AccountEntry;
use App\Entity\Entry;
use App\Helper as H;
use App\Util;

/**
 * Класс-сущность  документ возвратная  накладная
 *
 */
class ReturnIssue extends Document
{

    public function generateReport() {


        $i = 1;
        $detail = array();

        foreach ($this->detaildata as $value) {

            if (isset($detail[$value['item_id']])) {
                $detail[$value['item_id']]['quantity'] += $value['quantity'];
            } else {
                $detail[] = array("no" => $i++,
                    "tovar_name" => $value['itemname'],
                    "tovar_code" => $value['item_code'],
                    "quantity" => H::fqty($value['quantity']),
                    "price" => H::famt($value['price']),
                    "pricends" => H::famt($value['pricends']),
                    "msr" => $value['msr'],
                    "amount" => H::famt($value['amount'])
                );
            }
        }

        $firm = \App\System::getOptions("firmdetail");
        $customer = \App\Entity\Customer::load($this->customer_id);

        $header = array('date' => date('d.m.Y', $this->document_date),
            "_detail" => $detail,
            "usends" => H::usends(),
            "firmname" => $firm['firmname'],
            "customername" => $this->customer_name . ', тел. ' . $customer->phone,
            "order" => $this->headerdata["order"],
            "document_number" => $this->document_number,
            "totalnds" => H::famt($this->headerdata["totalnds"]),
            "total" => H::famt($this->headerdata["total"])
        );



        $report = new \App\Report('returnissue.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute() {
        $types = array();
        $common = \App\System::getOptions("common");

        
        AccountEntry::AddEntry("704", "36", $this->headerdata['total'], $this->document_id);
       
        //аналитика
        foreach ($this->detaildata as $stock) {
            $acc_code = $stock['acc_code'];

 
                $sc = new Entry($this->document_id, $acc_code, $stock['price'] * $stock['quantity'], $stock['quantity']);
                $sc->setStock($stock["stock_id"]);
                //$sc->setExtCode($stock['price'] * $stock['quantity']); //Для АВС 

                $sc->setCustomer($this->customer_id);
                $sc->save();
       

            //группируем по синтетическим счетам
          
            if (is_array($acc_code)) {
                $types[$acc_code]['amount'] = $types[$acc_code]['amount'] + $stock['pricends'] * $stock['quantity'];
                $types[$acc_code]['pamount'] = $types[$acc_code]['pamount'] + $stock['price'] * $stock['quantity'];
                // $types[$acc_code]['namount'] = $types[$acc_code]['namount'] + $stock['nds'];
            } else {
                $types[$acc_code] = array();
                $types[$acc_code]['amount'] = $stock['pricends'] * $stock['quantity'];
                $types[$acc_code]['pamount'] = $stock['price'] * $stock['quantity'];
                // $types[$acc_code]['namount'] = $stock['nds'];
            }
        }

        foreach ($types as $acc => $value) {
            if ($acc == "281") {    //товары
                AccountEntry::AddEntry("281", "902", $value['pamount'], $this->document_id);
 
            }
            if ($acc == "26") {    //готовая продукция
                AccountEntry::AddEntry("26", "901", $value['pamount'], $this->document_id);
     
            }
        }

        $total = $this->headerdata['total'];
 
        if ($this->headerdata['cash'] == true) {


            AccountEntry::AddEntry("36", "30", $total, $this->document_id);
            $sc = new Entry($this->document_id, 36, $total);
            $sc->setCustomer($this->customer_id);
            $sc->save();
        }

        if ($this->headerdata['cash'] != true) {  //предоплата или долг
            AccountEntry::AddEntry("36", "681", $total, $this->document_id);
            $sc = new Entry($this->document_id, 36, $total);
            $sc->setCustomer($this->customer_id);
            $sc->save();
            $sc = new Entry($this->document_id, 681, 0 - $total);
            $sc->setCustomer($this->customer_id);
            $sc->save();
        }

        //налоговый кредит  
        if ($this->headerdata['totalnds'] > 0) {
            AccountEntry::AddEntry("704", "643", 0-$this->headerdata['totalnds'], $this->document_id);
            //AccountEntry::AddEntry("643", "6412", $this->headerdata['totalnds'], $this->document_id );
            $sc = new Entry($this->document_id, 643,  $this->headerdata['totalnds']);

            $sc->save();
        }





        return true;
    }

}
