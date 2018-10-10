<?php

namespace ZippyERP\ERP\Entity\Doc;

use ZippyERP\ERP\Entity\Entry;
use ZippyERP\ERP\Entity\SubConto;
use ZippyERP\ERP\Helper as H;

/**
 *   документ  оприходование  с  производства
 *
 */
class InventoryProd extends Document
{

    public function generateReport() {


        $i = 1;
        $detail = array();

        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "tovar_name" => $value['itemname'],
                "measure" => $value['measure_name'],
                "quantity" => $value['quantity'] / 1000,
                "price" => H::fm($value['price']),
                "amount" => H::fm(($value['quantity'] / 1000) * $value['price'])
            );
        }

        //$firm = \ZippyERP\System\System::getOptions("firmdetail");
        // $customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata["customer"]);
        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "storename" => $this->headerdata["storename"],
            "gainsname" => $this->headerdata["gainsname"]
        );

        $report = new \ZippyERP\ERP\Report('inventorygain.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute() {
        $conn = \ZDB\DB::getConnect();
        $conn->StartTrans();

        $types = array();

        //аналитика
        foreach ($this->detaildata as $item) {
            $stock = \ZippyERP\ERP\Entity\Stock::getStock($this->headerdata['store'], $item['item_id'], $item['price'], true);

            $sc = new SubConto($this, $item['type'], $item['price'] * ($item['quantity'] / 1000));
            $sc->setStock($stock->stock_id);
            $sc->setQuantity($item['quantity']);
            $sc->save();

            //группируем по синтетическим счетам
            if (is_array($types[$item['type']])) {
                $types[$item['type']]['amount'] = $types[$item['type']]['amount'] + $item['partion'] * ($item['quantity'] / 1000);
            } else {
                $types[$item['type']] = array();

                $types[$item['type']]['amount'] = $item['price'] * ($item['quantity'] / 1000);
            }
        }

        foreach ($types as $acc => $value) {
            Entry::AddEntry($acc, 23, $value['amount'], $this->document_id, $this->document_date);
        }

        $conn->CompleteTrans();
        return true;
    }

}
