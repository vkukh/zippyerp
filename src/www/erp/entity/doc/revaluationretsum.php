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
 * Класс-сущность  документ переоценка  в  суммовом  учете
 * 
 */
class RevaluationRetSum extends Document
{

    public function generateReport()
    {



        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "summa" => H::fm($this->headerdata['summa']),
            "actual" => H::fm($this->headerdata['actual']),
            "storename" => $this->headerdata['storename']
        );

        switch ($this->headerdata['type']) {
            case 1: $header['typename'] = "Переоценка";
                break;
            case 2: $header['typename'] = "Списание недостач";
                break;
            case 3: $header['typename'] = "Оприходование излишков";
                break;
        }

        $report = new \ZippyERP\ERP\Report('revaluationretsum.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute()
    {
        $diff = $this->headerdata['summa'] - $this->headerdata['actual'];

        $stock_id = $this->headerdata['stock_id'];

        $stock = Stock::load($stock_id);
        //обновляем в  магазине
        $stock->updateStock($this->document_id, $diff);


        $a282 = Account::load(282);

        Entry::AddEntry("902", "282", $diff, $this->document_id);

        return true;
    }

}
