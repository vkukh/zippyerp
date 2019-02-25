<?php

namespace App\Entity\Doc;

use App\Entity\Entry;
use App\Entity\AccountEntry;
use App\Entity\Stock;
use App\Entity\Store;
use App\Helper as H;

/**
 * Класс-сущность  локумент перемещения товаров
 *
 * @table=store_document
 * @view=store_document_view
 * @keyfield=document_id
 */
class MoveItem extends Document
{

    public function Execute() {



        $total = 0;
        foreach ($this->detaildata as $value) {

            //списываем  со склада
            $stockfrom = $value['stock_id'];
            $sc = new Entry($this->document_id, $this->headerdata["typefrom"], 0 - ($value['quantity'] * $value['partion']), 0 - $value['quantity']);
            $sc->setStock($stockfrom);


            $sc->save();

            $stockto = Stock::getStock($this->headerdata['storeto'], $value['item_id'], $value['partion'], $this->headerdata["typeto"], true);
            $sc = new Entry($this->document_id, $this->headerdata["typeto"], $value['quantity'] * $value['partion'], $value['quantity']);
            $sc->setStock($stockto->stock_id);


            $sc->save();

            $total = $total + $value['quantity'] * $value['partion'];
        }

        if ($this->headerdata['typeto'] != $this->headerdata['typefrom']) {
            AccountEntry::AddEntry($this->headerdata['typeto'], $this->headerdata['typefrom'], $total, $this->document_id);
        }


        return true;
    }

    public function generateReport() {





        $i = 1;
        $detail = array();
        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "item_name" => $value['itemname'],
                "price" => H::famt($value['partion']),
                "msr" => $value['msr'],
                "quantity" => H::fqty($value['quantity']));
        }

        $header = array(
            "_detail" => $detail,
            'date' => date('d.m.Y', $this->document_date),
            "from" => Store::load($this->headerdata["storefrom"])->storename,
            "to" => Store::load($this->headerdata["storeto"])->storename,
            "document_number" => $this->document_number
        );
        $report = new \App\Report('moveitem.tpl');

        $html = $report->generate($header);

        return $html;
    }

}
