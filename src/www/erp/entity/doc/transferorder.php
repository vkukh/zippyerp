<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\ERP\Helper as H;

/**
 * Класс-сущность  документ  Платежное поручение
 *
 */
class TransferOrder extends Document
{

    public function generateReport()
    {
        $myfirm = \ZippyERP\System\System::getOptions("firmdetail");
        $myaccount = \ZippyERP\ERP\Entity\MoneyFund::load($this->headerdata['bankaccount']);
        $mybank = \ZippyERP\ERP\Entity\Bank::load($myaccount->bank);

        $customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata['customer']);
        $cbank = \ZippyERP\ERP\Entity\Bank::load($customer->bank);

        $header = array(
            'date' => date('d.m.Y', $this->document_date),
            'myname' => $myfirm['name'],
            'mycode' => $myfirm['code'],
            'mybankaccount' => $myaccount->bankaccount,
            'mybank' => $mybank->bank_name,
            'mybankcode' => $mybank->mfo,
            'cname' => $customer->customer_name,
            'ccode' => $customer->code,
            'cbankaccount' => $customer->bankaccount,
            'cbank' => $cbank->bank_name,
            'cbankcode' => $cbank->mfo,
            "document_number" => $this->document_number,
            "document_date" => date('Y.m.d', $this->document_date),
            "notes" => $this->headerdata['notes'],
            "amount" => H::fm($this->amount),
            "amountstr" => \ZippyERP\ERP\Util::ucfirst(\ZippyERP\ERP\Util::money2str($this->amount / 100))
        );

        $report = new \ZippyERP\ERP\Report('transferorder.tpl');

        $html = $report->generate($header, array());

        return $html;
    }

    public function Execute()
    {

        return true;
    }

}
