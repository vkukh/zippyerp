<?php

namespace App\Entity\Doc;

use App\Entity\Entry;
use App\Entity\AccountEntry;
use App\Helper as H;
use App\Util;

/**
 * Класс-сущность  документ  списание ТМЦ
 *
 */
class ItemExpence extends Document
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
                    "amount" => H::famt($value['quantity'] * $value['price'])
                );
            }
        }

        $firm = \App\System::getOptions("firmdetail");


        $header = array('date' => date('d.m.Y', $this->document_date),
            "_detail" => $detail,
            "firmname" => $firm['firmname'],
            "document_number" => $this->document_number,
            "total" => H::famt($this->headerdata["total"]),
            "summa" => Util::ucfirst(Util::money2str($this->headerdata["total"]))
        );

        $report = new \App\Report('itemexpence.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute() {
        $conn = \ZDB\DB::getConnect();
        $conn->StartTrans();
        $itemtypes = array();
        foreach ($this->detaildata as $row) {

            $sc = new Entry($this->document_id, $row['acc_code'], 0 - $row['amount'], 0 - $row['quantity']);
            $sc->setStock($row['stock_id']);
            $sc->save();
            if ($itemtypes[$row['acc_code']] > 0) {
                $itemtypes[$row['acc_code']] += $row['amount'];
            } else {
                $itemtypes[$row['acc_code']] = $row['amount'];
            }
        }

        foreach ($itemtypes as $c => $v) {
            AccountEntry::AddEntry($this->headerdata["types"], $c, $v, $this->document_id);
        }

        $conn->CompleteTrans();
        return true;
    }

}
