<?php

namespace App\Entity\Doc;

use App\Entity\AccountEntry;
use App\Entity\Entry;
use App\Helper as H;

/**
 * Класс-сущность  документ приходная  накладая
 *
 */
class GoodsReceipt extends Document
{

    public function generateReport() {


        $i = 1;

        $detail = array();
        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "itemname" => $value['itemname'],
                "itemcode" => $value['item_code'],
                "quantity" => H::fqty($value['quantity']),
                "price" => H::famt($value['price']),
                "pricends" => H::famt($value['pricends']),
                "msr" => $value['msr'],
                "amount" => H::famt($value['amount'])
            );
        }

        $header = array('date' => date('d.m.Y', $this->document_date),
            "_detail" => $detail,
            "usends" => $this->headerdata["isnds"],
            "customer_name" => $this->customer_name,
            "document_number" => $this->document_number,
            "totalnds" => H::famt($this->headerdata["totalnds"]),
            "total" => H::famt($this->headerdata["total"])
        );


        $report = new \App\Report('goodsreceipt.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute() {
        $types = array();
        $common = \App\System::getOptions("common");

        //аналитика
        foreach ($this->detaildata as $item) {
            $stock = \App\Entity\Stock::getStock($this->headerdata['store'], $item['item_id'], $item['price'], $item['itemtype'], true);

            if ($item['itemtype'] == 15) {   //инвестиции
            } else {
                $sc = new Entry($this->document_id, $stock->acc_code, $item['price'] * $item['quantity'], $item['quantity']);
                $sc->setStock($stock->stock_id);
                $sc->setExtCode($item['price'] * $item['quantity']); //Для АВС 

                $sc->setCustomer($this->customer_id);
                $sc->save();
            }

            //группируем по синтетическим счетам
            if (isset($types[$stock->acc_code])) {
                $types[$stock->acc_code] = $types[$stock->acc_code] + $item['price'] * $item['quantity'];
            } else {
                $types[$stock->acc_code] = $item['price'] * $item['quantity'];
            }
        }

        foreach ($types as $acc => $value) {
            AccountEntry::AddEntry($acc, "63", $value, $this->document_id);
            $sc = new Entry($this->document_id, 63, 0 - $value);
            $sc->setCustomer($this->customer_id);
            $sc->save();
        }

        $total = $this->headerdata['total'];

        if ($this->headerdata['cash'] == true) {


            AccountEntry::AddEntry("63", "30", $total, $this->document_id);
            $sc = new Entry($this->document_id, 63, $total);
            $sc->setCustomer($this->customer_id);
            $sc->save();
            $sc = new Entry($this->document_id, 30, 0 - $total);
            $sc->setExtCode(H::TYPEOP_CUSTOMER_IN);
            $sc->save();
        }

        if ($this->headerdata['cash'] != true) {  //предоплата или долг
            AccountEntry::AddEntry("63", "371", $total, $this->document_id);
            $sc = new Entry($this->document_id, 63, $total);
            $sc->setCustomer($this->customer_id);
            $sc->save();
            $sc = new Entry($this->document_id, 371, 0 - $total);
            $sc->setCustomer($this->customer_id);
            $sc->save();
        }

        //налоговый кредит  
        if ($this->headerdata['totalnds'] > 0) {
            AccountEntry::AddEntry("644", "63", $this->headerdata['totalnds'], $this->document_id);
            //AccountEntry::AddEntry("6412", "644", $this->headerdata['totalnds'], $this->document_id );
            $sc = new Entry($this->document_id, 644, $this->headerdata['totalnds']);

            $sc->save();
        }





        return true;
    }

    public function getRelationBased() {
        $list = array();

        $list['TaxInvoiceIncome'] = 'Входящая НН';
        // $list['ReturnGoodsReceipt'] = 'Возврат поставщику';

        return $list;
    }

}
