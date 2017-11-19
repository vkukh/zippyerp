<?php

namespace ZippyERP\ERP\Entity\Doc;

use ZippyERP\ERP\Entity\Entry;
use ZippyERP\ERP\Entity\MoneyFund;
use ZippyERP\ERP\Entity\SubConto;
use ZippyERP\ERP\Helper as H;
use ZippyERP\ERP\Util;

/**
 * Класс-сущность  документ расходная  накладая для инет магазина
 *
 */
class OnlineIssue extends Document
{

    
    
    public function generateReport()
    {


        $i = 1;
        $detail = array();

        foreach ($this->detaildata as $value) {

            if (isset($detail[$value['item_id']])) {
                $detail[$value['item_id']]['quantity'] += $value['quantity'];
            } else {
                $detail[] = array("no" => $i++,
                    "tovar_name" => $value['itemname'],
                    "measure" => $value['measure_name'],
                    "quantity" => $value['quantity'] / 1000,
                    "price" => H::fm($value['price']),
                    "amount" => H::fm(($value['quantity'] / 1000) * $value['price'])
                );
            }
        }

        $firm = \ZippyERP\System\System::getOptions("firmdetail");

        $header = array('date' => date('d.m.Y', $this->document_date),
            "firmname" => $firm['name'],
            "firmcode" => $firm['code'],
            "document_number" => $this->document_number,
            "totalnds" => $this->headerdata["totalnds"] > 0 ? H::fm($this->headerdata["totalnds"]) : 0,
            "total" => H::fm($this->headerdata["total"]),
            "summa" => Util::ucfirst(Util::money2str($this->headerdata["total"] / 100))
        );

        $report = new \ZippyERP\ERP\Report('onlineissue.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {
        $conn = \ZDB\DB::getConnect();
        $conn->StartTrans();
         
        $types = array();

        //аналитика
        $a281=0;
        foreach ($this->detaildata as $item) {
            $stock = \ZippyERP\ERP\Entity\Stock::getStock($this->headerdata['store'], $item['item_id'], $item['partion'], true);
            $a281 += $item['partion'] * ($item['quantity'] / 1000);
            $sc = new SubConto($this, 281, 0 - $item['partion'] * ($item['quantity'] / 1000));
            $sc->setStock($stock->stock_id);
            $sc->setQuantity(0 - $item['quantity']);
            $sc->setExtCode($item['price'] - $item['partion']); //Для АВС

            $sc->save();

            
        }

 

        $nds = H::nds($this->headerdata["total"]);
        $nds = $nds*($this->headerdata["total"] - $a281);
        
        //себестоимость
        Entry::AddEntry("902", "281", $a281, $this->document_id, $this->document_date);
        
        //затраты на  доставку
        //Entry::AddEntry("93", "661", $value['damount'], $this->document_id, $this->document_date);
        //Entry::AddEntry("93", "30", $value['damount'], $this->document_id, $this->document_date);
        
    
        //НДС
        
        Entry::AddEntry("702", "641", $nds , $this->document_id, $this->document_date);
        $sc = new SubConto($this, 641, 0 - $nds);
        $sc->setExtCode(\ZippyERP\ERP\Consts::TAX_NDS);
        $sc->save();
 


        if ($this->headerdata['paytype'] == 2) {  //наличные

            $cash = MoneyFund::getCash();
            \ZippyERP\ERP\Entity\Entry::AddEntry("30", "702", $this->headerdata["total"], $this->document_id, $this->document_date);
       
        }
        if ($this->headerdata['paytype'] == 3) {   //кредитка

            $bank = MoneyFund::getBankAccount();
            \ZippyERP\ERP\Entity\Entry::AddEntry("31", "702", $this->headerdata["total"], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 31, $this->headerdata["total"]);
            $sc->setMoneyfund($bank->id);
            $sc->save();         
        }


        $conn->CompleteTrans();
        return true;
    }

    

}
