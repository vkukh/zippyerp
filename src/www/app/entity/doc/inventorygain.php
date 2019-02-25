<?php

namespace App\Entity\Doc;

use App\Entity\Entry;
use App\Entity\AccountEntry;
use App\Helper as H;

/**
 *   документ  оприходование  излишков
 *
 */
class InventoryGain extends Document
{

    public function generateReport() {


        $i = 1;
        $detail = array();

        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "tovar_name" => $value['itemname'],
                "measure" => $value['msr'],
                "quantity" => H::fqty($value['quantity']),
                "price" => H::famt($value['price']),
                "amount" => H::famt(($value['quantity'] ) * $value['price'])
            );
        }

        $header = array('date' => date('d.m.Y', $this->document_date),
            "_detail" => $detail,
            "document_number" => $this->document_number,
            "storename" => $this->headerdata["storename"],
            "gainsname" => $this->headerdata["gainsname"]
        );

        $report = new \App\Report('inventorygain.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute() {
        $conn = \ZDB\DB::getConnect();
        $conn->StartTrans();

        $types = array();

        //аналитика
        foreach ($this->detaildata as $item) {
            $stock = \App\Entity\Stock::getStock($this->headerdata['store'], $item['item_id'], $item['partion'], $item['type'], true);

            $sc = new Entry($this->document_id, $item['type'], $item['partion'] * ($item['quantity']), $item['quantity']);
            $sc->setStock($stock->stock_id);

            $sc->save();

            //группируем по синтетическим счетам
            if (is_array($types[$item['type']])) {
                $types[$item['type']]['amount'] = $types[$item['type']]['amount'] + $item['partion'] * ($item['quantity'] );
            } else {
                $types[$item['type']] = array();

                $types[$item['type']]['amount'] = $item['partion'] * ($item['quantity'] );
            }
        }

        foreach ($types as $acc => $value) {
            AccountEntry::AddEntry($acc, $this->headerdata["gains"], $value['amount'], $this->document_id);
        }

        $conn->CompleteTrans();
        return true;
    }

}
