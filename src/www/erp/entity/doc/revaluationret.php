<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use \ZippyERP\ERP\Util;
use \ZippyERP\ERP\Helper as H;
use \ZippyERP\ERP\Entity\Entry;
use \ZippyERP\ERP\Entity\Account;
use \ZippyERP\ERP\Entity\Stock;
use Carbon\Carbon;

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
            "storename" => $this->headerdata['storename']
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
        $diff = 0;
        foreach ($this->detaildata as $value) {
            $diff = $diff + $value['quantity'] * ($value['newprice'] - $value['price']);
        }


        Entry::AddEntry("282", "285", $diff, $this->document_id);

        return true;
    }

}
