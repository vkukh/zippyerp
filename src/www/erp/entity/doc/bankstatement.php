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
        $this->created = time();
        $this->document_date = time();
    }

    public function generateReport()
    {





        $i = 1;
        $detail = array();
        $total = 0;
        foreach ($this->detaildata as $value) {
            $acc = Account::load($value['acc'])->acc_code;
            $detail[] = array(
                "type" => $value['type'] == 1 ? "Приход" : "Расход",
                "acc_code" => $acc,
                "amount" => number_format($value['amount'] / 100, 2),
                "comment" => $value['comment']);

            $total += $value['amount'];
        };



        $header = array(
            'date' => date('d.m.Y', $this->document_date),
            'bankaccount' => \ZippyERP\ERP\Entity\MoneyFund::load($this->headerdata['bankaccount'])->title,
            "document_number" => $this->document_number,
            "total" => number_format($total / 100, 2)
                // "amountstr" => \ZippyERP\ERP\Util::ucfirst(\ZippyERP\ERP\Util::money2str(number_format($total / 100, 2)))
        );

        $reportgen = new \ZCL\RepGen\RepGen(_ROOT . 'templates/erp/templates/bankstatement.html', $header);

        $html = $reportgen->generateSimple($detail);
        return $html;
    }

    public function Execute()
    {
        foreach ($this->detaildata as $value) {

            $acc = Account::load($value['acc']);
            if (substr($acc->acc_code, 0, 2) == "30")
                continue; // касса  проводится  кассовыми  ордерами

            if ($value['type'] == 1) {   // приход
                Entry::AddEntry("31", $value['acc'], $value['amount'], $this->document_id, $value['comment']);
            } else {
                Entry::AddEntry($value['acc'], "31", $value['amount'], $this->document_id, $value['comment']);
                $value['amount'] = 0 - $value['amount'];
            }
            // Движение  по  денежным  счетам
            $conn = \ZCL\DB\DB::getConnect();

            $sql = "insert  into erp_moneyfunds_activity (document_id,id_moneyfund,amount) values ({$this->document_id},{$this->headerdata['bankaccount']},{$value['amount']})";
            $conn->Execute($sql);
        }
    }

    public function loadBasedOn($id)
    {
        
    }

}
