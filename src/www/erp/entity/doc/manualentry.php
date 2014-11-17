<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\ERP\Helper as H;

/**
 * Класс-сущность  документ ручная хоз. операция
 * 
 */
class ManualEntry extends Document
{

    protected function init()
    {
        parent::init();
    }

    public function generateReport()
    {

        $header = array(
            'date' => date('d.m.Y', $this->document_date),
            "description" => $this->headerdata["description"],
            "document_number" => $this->document_number
        );

        $i = 1;
        $detail = array();

        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "dt" => $value['acc_d'],
                "ct" => $value['acc_c'],
                "amount" => H::fm($value['amount']),
                "comment" => $value['comment']);
        }

        $report = new \ZippyERP\ERP\Report('manualentry.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {
        foreach ($this->detaildata as $value) {

            \ZippyERP\ERP\Entity\Entry::AddEntry($value['acc_d'], $value['acc_c'], $value['amount'], $this->document_id, $value['comment']);
        }
    }

}
