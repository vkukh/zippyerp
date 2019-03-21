<?php

namespace App\Entity\Doc;

use App\Entity\AccountEntry;
use App\Entity\Entry;
use App\Helper as H;
use App\Util;

/**
 * Класс-сущность  документ возвратная  накладная в рознице
 *
 */
class RetRetIssue extends Document
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
                    "msr" => $value['msr'],
                    "amount" => H::famt($value['amount'])
                );
            }
        }

        $firm = \App\System::getOptions("firmdetail");
        $customer = \App\Entity\Customer::load($this->customer_id);

        $header = array('date' => date('d.m.Y', $this->document_date),
            "_detail" => $detail,
            "firmname" => $firm['firmname'],
            "customername" => $this->customer_name . ', тел. ' . $customer->phone,
            "order" => $this->headerdata["order"],
            "document_number" => $this->document_number,
            "total" => H::famt($this->headerdata["total"])
        );



        $report = new \App\Report('retretissue.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute() {
        $types = array();
        $common = \App\System::getOptions("common");
         $total = $this->headerdata['total'];
        AccountEntry::AddEntry("282", "902", $total, $this->document_id);
         
        
        //аналитика
        foreach ($this->detaildata as $stock) {


            $sc = new Entry($this->document_id, 282, $stock['price'] * $stock['quantity'], $stock['quantity']);
            $sc->setStock($stock["stock_id"]);
            //$sc->setExtCode($stock['price']* $stock['quantity']  ); //Для АВС 

           // $sc->setCustomer($this->customer_id);
            $sc->save();
        }
   
        AccountEntry::AddEntry(704, "36", $total, $this->document_id);
       // $sc = new Entry($this->document_id, 36, 0 - $value);
       // $sc->setCustomer($this->customer_id);
       // $sc->save();


       

        AccountEntry::AddEntry("36", "337", $total, $this->document_id);
            $sc = new Entry($this->document_id, 36, $total);
           // $sc->setCustomer($this->customer_id);
            $sc->save();
       
        

        //налоговый кредит  
            if (H::usends()) {
                $nds = $total * H::nds(true);
                AccountEntry::AddEntry("704", "6412",0- $nds, $this->document_id);
            }





        return true;
    }

}
