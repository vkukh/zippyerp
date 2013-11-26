<?php

namespace ZippyERP\ERP\Entity\Doc;

/**
 * Класс-сущность  локумент ручная хоз. операция
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
                            "dt" => $value['acc_d_code'],
                            "ct" => $value['acc_c_code'],
                            "amount" => number_format($value['amount'] / 100, 2),
                            "comment" => $value['comment']);
                }

                $reportgen = new \ZCL\RepGen\RepGen(_ROOT . 'templates/erp/templates/manualentry.html', $header);

                $html = $reportgen->generateSimple($detail);
                return $html;
        }

        public function ExecuteImpl()
        {
                foreach ($this->detaildata as $value) {

                        \ZippyERP\ERP\Entity\Entry::AddEntry($value['acc_d'], $value['acc_c'], $value['amount'], $this->document_id, $value['comment']);
                }
        }

}