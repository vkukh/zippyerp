<?php

namespace ZippyERP\ERP\Entity\Doc;

use ZippyERP\ERP\Entity\Entry;
use ZippyERP\ERP\Entity\Stock;
use ZippyERP\ERP\Entity\SubConto;
use ZippyERP\ERP\Helper as H;

/**
 * Класс-сущность  документ переоценка  в  рознице
 *
 */
class RevaluationRet extends Document
{

    public function generateReport()
    {


        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "summa" => H::fm($this->headerdata['summa']),
            "store" => $this->headerdata['storename']
        );
        $i = 1;
        $detail = array();
        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "item_name" => $value['itemname'],
                "measure" => $value['measure_name'],
                "price" => H::fm($value['price']),
                "newprice" => H::fm($value['newprice']));
        }


        $report = new \ZippyERP\ERP\Report('revaluationret.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {
        $diffall = 0;
        foreach ($this->detaildata as $value) {

            $diffall = $diffall + ($value['quantity'] / 1000) * ($value['newprice'] - $value['price']);

            $stock = Stock::load($value["stock_id"]);
            $newstock = Stock::getStock($stock->store_id, $stock->item_id, $value['newprice'], true);


            $sc = new SubConto($this, 282, 0 - ($value['quantity'] / 1000) * $stock->price);
            $sc->setStock($stock->stock_id);
            $sc->setQuantity(0 - $value['quantity']);

            $sc->save();
            $sc = new SubConto($this, 282, ($value['quantity'] / 1000) * $newstock->price);
            $sc->setStock($newstock->stock_id);
            $sc->setQuantity($value['quantity']);

            $sc->save();
        }


        Entry::AddEntry("282", "285", $diffall, $this->document_id, $this->document_date);

        $sc = new SubConto($this, 285, 0 - $diffall);
        $sc->setExtCode($this->headerdata["store"]);

        $sc->save();
        return true;
    }

}
