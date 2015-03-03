<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\ERP\Helper as H;

/**
 * Документ Договор
 */
class Contract extends Document
{

    protected function init()
    {
        parent::init();
        $this->intattr1 = 0; //контрагент
    }

    public function generateReport()
    {
        $customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata["customer"]);
        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "customer" => $customer->customer_name,
            "description" => $this->headerdata["description"],
            'startdate' => date('d.m.Y', $this->headerdata["startdate"]),
            'enddate' => date('d.m.Y', $this->headerdata["enddate"]),
            "amount" => H::fm($this->headerdata["amount"])
        );

        $report = new \ZippyERP\ERP\Report('contract.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute()
    {

        return true;
    }

    //список состояний  для   выпадающих списков
    public static function getStatesList()
    {
        $list = array();
        $list[Document::STATE_NEW] = 'Новый';
        $list[Document::STATE_WA] = 'Ждет подтвержения';
        $list[Document::STATE_APPROVED] = 'Утвержден';
        $list[Document::STATE_CLOSED] = 'Закрыт';

        return $list;
    }

}
