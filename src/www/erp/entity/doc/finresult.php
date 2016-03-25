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

        $acc701 = Account::load("701");
        $s701 = $acc701->getSaldo($this->document_date);
        Entry::AddEntry(701, 79, abs($s701), $this->document_id, $this->document_date);
        $acc702 = Account::load("702");
        $s702 = $acc702->getSaldo($this->document_date);
        Entry::AddEntry(702, 79, abs($s702), $this->document_id, $this->document_date);
        $acc703 = Account::load("703");
        $s703 = $acc703->getSaldo($this->document_date);
        Entry::AddEntry(703, 79, abs($s703), $this->document_id, $this->document_date);
        $acc71 = Account::load("71");
        $s71 = $acc71->getSaldo($this->document_date);
        Entry::AddEntry(71, 79, abs($s71), $this->document_id, $this->document_date);



        $acc901 = Account::load("901");
        $s901 = $acc901->getSaldo($this->document_date);
        Entry::AddEntry(79, 901, abs($s901), $this->document_id, $this->document_date);
        $acc902 = Account::load("902");
        $s902 = $acc902->getSaldo($this->document_date);
        Entry::AddEntry(79, 902, abs($s902), $this->document_id, $this->document_date);
        $acc903 = Account::load("903");
        $s903 = $acc903->getSaldo($this->document_date);
        Entry::AddEntry(79, 903, abs($s903), $this->document_id, $this->document_date);
        $acc91 = Account::load("91");
        $s91 = $acc91->getSaldo($this->document_date);
        Entry::AddEntry(79, 91, abs($s91), $this->document_id, $this->document_date);
        $acc92 = Account::load("92");
        $s92 = $acc92->getSaldo($this->document_date);
        Entry::AddEntry(79, 92, abs($s92), $this->document_id, $this->document_date);
        $acc93 = Account::load("93");
        $s93 = $acc93->getSaldo($this->document_date);
        Entry::AddEntry(79, 93, abs($s93), $this->document_id, $this->document_date);
        $acc94 = Account::load("94");
        $s94 = $acc94->getSaldo($this->document_date);
        Entry::AddEntry(79, 94, abs($s94), $this->document_id, $this->document_date);
        $acc97 = Account::load("97");
        $s97 = $acc97->getSaldo($this->document_date);
        Entry::AddEntry(79, 97, abs($s97), $this->document_id, $this->document_date);

        return true;
    }

}
