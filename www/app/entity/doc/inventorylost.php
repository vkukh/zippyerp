<?php

namespace App\Entity\Doc;

use App\Entity\Entry;
use App\Entity\AccountEntry;
use App\Helper as H;

/**
 *   документ  ТМЦ (потери)
 *
 */
class InventoryLost extends Document
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
            "document_number" => $this->document_number,
            "_detail" => $detail,
            "storename" => $this->headerdata["storename"],
            "expensesname" => $this->headerdata["expensesname"]
        );

        $report = new \App\Report('inventorylost.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute() {
        $conn = \ZDB\DB::getConnect();
        $conn->StartTrans();

        $types = array();

        //аналитика
        foreach ($this->detaildata as $item) {
            $stock = \App\Entity\Stock::getStock($this->headerdata['store'], $item['item_id'], $item['partion'], 0 - $item['quantity'], $item['acc_code'], true);

            $sc = new Entry($this->document_id, $item['type'], 0 - $item['partion'] * $item['quantity'], 0 - $item['quantity']);
            $sc->setStock($stock->stock_id);

            $sc->save();

            //группируем по синтетическим счетам
            if (is_array($types[$item['acc_code']])) {
                $types[$item['acc_code']]['amount'] = $types[$item['acc_code']]['amount'] + $item['partion'] * ($item['quantity'] );
            } else {
                $types[$item['acc_code']] = array();

                $types[$item['acc_code']]['amount'] = $item['partion'] * ($item['quantity'] );
            }
        }

        foreach ($types as $acc => $value) {
            AccountEntry::AddEntry($this->headerdata["expenses"], $acc, $value['amount'], $this->document_id);
        }

        $conn->CompleteTrans();
        return true;
    }

}
