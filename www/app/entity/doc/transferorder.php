<?php

namespace App\Entity\Doc;

use App\Helper as H;

/**
 * Класс-сущность  документ  Платежное поручение
 *
 */
class TransferOrder extends Document
{

    public function generateReport() {
        $firmdetail = \App\System::getOptions("firmdetail");

        $mybank = \App\Entity\Bank::load($firmdetail['bank']);

        $customer = \App\Entity\Customer::load($this->customer_id);
        $cbank = \App\Entity\Bank::load($customer->bank);


        $header = array(
            'date' => date('d.m.Y', $this->document_date),
            'myname' => $firmdetail['name'],
            'mycode' => $firmdetail['edrpou'],
            'mybankaccount' => $firmdetail['bankaccount'],
            'mybank' => $mybank->bank_name,
            'mybankcode' => $mybank->mfo,
            'cname' => $customer->customer_name,
            'ccode' => $customer->edrpou,
            'cbankaccount' => $customer->bankaccount,
            'cbank' => $cbank->bank_name,
            'cbankcode' => $cbank->mfo,
            "document_number" => $this->document_number,
            "document_date" => date('Y.m.d', $this->document_date),
            "notes" => $this->headerdata['notes'],
            "amount" => H::famt($this->amount),
            "amountstr" => \App\Util::ucfirst(\App\Util::money2str($this->amount / 100))
        );

        $report = new \App\Report('transferorder.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute() {

        return true;
    }

}
