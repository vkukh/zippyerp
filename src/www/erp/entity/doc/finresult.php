<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use \ZippyERP\ERP\Util;
use \ZippyERP\ERP\Helper as H;
use \ZippyERP\ERP\Entity\Entry;
use \ZippyERP\ERP\Entity\Account;

/**
 * Класс-сущность  документ финансовые  результаты
 *
 */
class FinResult extends Document
{

    public function generateReport()
    {


        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number
        );

        $report = new \ZippyERP\ERP\Report('finresult.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute()
    {

        $acc702 = Account::load("702");
        $s702 = $acc702->getSaldo($this->document_date);
        Entry::AddEntry(702, 79, abs($s702), $this->document_id, $this->document_date);
        $acc902 = Account::load("902");
        $s902 = $acc902->getSaldo($this->document_date);
        Entry::AddEntry(79, 902, abs($s902), $this->document_id, $this->document_date);

        return true;
    }

}
