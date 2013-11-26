<?php

namespace ZippyERP\ERP\Entity\Doc;

use ZippyERP\ERP\Entity\Account;
use ZippyERP\ERP\Entity\Entry;

/**
 * Класс-сущность  документ банковская выписка
 * 
 */
class BankStatement extends Document
{

        protected function init()
        {
                parent::init();
        }

        public function generateReport()
        {

                $header = array(
                    'date' => date('d.m.Y', $this->document_date),
                    "document_number" => $this->document_number
                );

                $reportgen = new \ZCL\RepGen\RepGen(_ROOT . 'templates/erp/templates/bankstatement.html', $header);


                $i = 1;
                $detail = array();
                foreach ($this->detaildata as $value) {
                        $acc = Account::load($value['acc'])->acc_code;
                        $detail[] = array(
                            "type" => $value['type'] == 1 ? "Приход" : "Расход",
                            "acc_code" => $acc,
                            "amount" => number_format($value['amount'] / 100, 2),
                            "comment" => $value['comment']);
                }

                $html = $reportgen->generateSimple($detail);
                return $html;
        }

        public function ExecuteImpl()
        {
                foreach ($this->detaildata as $value) {
                        if ($value['type'] == 1) {   // приход
                                Entry::AddEntry("31", $value['acc'], $value['amount'], $this->document_id, $value['comment']);
                        } else {
                                Entry::AddEntry($value['acc'], "31", $value['amount'], $this->document_id, $value['comment']);
                        }
                }
        }

}