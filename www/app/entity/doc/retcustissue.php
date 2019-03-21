<?php

namespace App\Entity\Doc;

use App\Entity\Entry;
use App\Entity\AccountEntry;
use App\Helper as H;
use App\Util;

/**
 * Класс-сущность  документ возврат  поставщику
 *
 */
class RetCustIssue extends Document
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
                    "msr" => $value['msr'],
                    "price" => $value['price'],
                    "pricends" => H::famt($value['pricends']),
                    "amount" => H::famt($value['amount']  )
                );
            }
        }


        $customer = \App\Entity\Customer::load($this->customer_id);

        $header = array('date' => date('d.m.Y', $this->document_date),
            "_detail" => $detail,
            "firmname" => $firm['firmname'],
            "customername" => $this->customer_name . ', тел. ' . $customer->phone,
          "usends" => $this->headerdata["isnds"],
          "document_number" => $this->document_number,
          "totalnds" => H::famt($this->headerdata["totalnds"]),
          "total" => $this->amount
        );


        $report = new \App\Report('retcustissue.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute() {
        $conn = \ZDB\DB::getConnect();
        $conn->StartTrans();
          $types = array();
        foreach ($this->detaildata as $row) {
            $acc_code = $row['acc_code'];
            $sc = new Entry($this->document_id,$acc_code, 0 - $row['amount'], 0 - $row['quantity']);
            $sc->setStock($row['stock_id']);
            // $sc->setExtCode(0 - $row['amount'])); //Для АВС 

            $sc->setCustomer($this->customer_id);
            $sc->save();
            
                 //группируем по синтетическим счетам

            
   
            if (isset($types[$acc_code])) {
                $types[$acc_code] = $types[$acc_code] + $row['price'] * $row['quantity'];
            } else {
                $types[$acc_code] = $row['price'] * $row['quantity'];
            }     
            
            foreach ($types as $acc => $value) {
                AccountEntry::AddEntry("63", $acc, $value, $this->document_id);
                $sc = new Entry($this->document_id, 63,  $value);
                $sc->setCustomer($this->customer_id);
                $sc->save();
            }     
            
            
        }

        if ($this->headerdata['cash'] == true) {
                  AccountEntry::AddEntry( 30,63,   $this->headerdata['total'], $this->document_id);

                $sc = new Entry($this->document_id, 63, 0 - $value['amount']);
                $sc->setCustomer($value['customer']);
                $sc->save();
                $sc = new Entry($this->document_id, 30, $this->headerdata['totalnds']);

                $sc->setExtCode(H::TYPEOP_CUSTOMER_IN_BACK);
                $sc->save();          
        }

        
        
        if ($this->headerdata['totalnds'] > 0) {
            AccountEntry::AddEntry("644", "63", 0-$this->headerdata['totalnds'], $this->document_id);
            //AccountEntry::AddEntry("6412", "644", $this->headerdata['totalnds'], $this->document_id );
            $sc = new Entry($this->document_id, 644, 0-$this->headerdata['totalnds']);

            $sc->save();
        }        
        
        $conn->CompleteTrans();
        return true;
    }

    public function getRelationBased() {
        $list = array();
        $list['Warranty'] = 'Гарантийный талон';
        $list['ReturnIssue'] = 'Возвратная накладная';

        return $list;
    }

}
