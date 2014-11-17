<?php

namespace ZippyERP\ERP\Entity\Doc;

use ZippyERP\ERP\Entity\Account;
use ZippyERP\ERP\Entity\Entry;
use ZippyERP\ERP\Entity\Customer;
use \ZippyERP\ERP\Helper as H;

/**
 * Класс-сущность  документ банковская выписка
 * 
 */
class BankStatement extends Document
{

    const IN = 1;   // Счет приход
    const OUT = 2;  // Счет расход, Платежное поручение
    const CASHIN = 3;      // приходный кассовый  ордер
    const CASHOUT = 4;     // расходный кассовый  ордер
    const TAX = 5;   // Оплата  налогов

    public function generateReport()
    {




        $types = $this->getTypes();
        $i = 1;
        $detail = array();
        $total = 0;
        foreach ($this->detaildata as $value) {

            $detail[] = array(
                "type" => $types[$value['optype']],
                "cust" => $value['customername'],
                "amount" => H::fm($value['amount']),
                "comment" => $value['comment']);
        };



        $header = array(
            'date' => date('d.m.Y', $this->document_date),
            'bankaccount' => \ZippyERP\ERP\Entity\MoneyFund::load($this->headerdata['bankaccount'])->title,
            "document_number" => $this->document_number
                // "amountstr" => \ZippyERP\ERP\Util::ucfirst(\ZippyERP\ERP\Util::money2str(H::fm($total )))
        );

        $report = new \ZippyERP\ERP\Report('bankstatement.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {
        $conn = \ZCL\DB\DB::getConnect();
        $conn->BeginTrans();
        try {

            foreach ($this->detaildata as $value) {
                if ($value['noentry'] === 'true')
                    continue;

                $sql = "insert  into erp_moneyfunds_activity (document_id,id_moneyfund,amount) values ({$this->document_id},{$this->headerdata['bankaccount']},{$value['amount']})";
                $conn->Execute($sql);

                if ($value['optype'] == self::OUT) {
                    Entry::AddEntry('63', "31", $value['amount'], $this->document_id, $value['comment']);
                    Customer::AddActivity($value['customer'], $value['amount'], $this->document_id);
                    $value['amount'] = 0 - $value['amount'];
                }
                if ($value['optype'] == self::IN) {
                    Entry::AddEntry('31', "36", $value['amount'], $this->document_id, $value['comment']);
                    Customer::AddActivity($value['customer'], 0 - $value['amount'], $this->document_id);
                }
                if ($value['optype'] == self::TAX) {
                    //Entry::AddEntry('64', "31", $value['amount'], $this->document_id, $value['comment']);
                    Customer::AddActivity($value['customer'], $value['amount'], $this->document_id);
                    $value['amount'] = 0 - $value['amount'];
                }
                if ($value['optype'] == self::CASHIN) {
                    Entry::AddEntry('30', "31", $value['amount'], $this->document_id, $value['comment']);
                    $sql = "insert  into erp_moneyfunds_activity (document_id,id_moneyfund,amount) values ({$this->document_id},0,{$value['amount']})";
                    $conn->Execute($sql);
                }
                if ($value['optype'] == self::CASHOUT) {
                    Entry::AddEntry('31', "30", $value['amount'], $this->document_id, $value['comment']);
                    $value['amount'] = 0 - $value['amount'];
                    $sql = "insert  into erp_moneyfunds_activity (document_id,id_moneyfund,amount) values ({$this->document_id},0,{$value['amount']})";
                    $conn->Execute($sql);
                }
                // Движение  по  денежным  счетам



                $this->AddConnectedDoc($value['doc']);

                //проставляем  оплату
                //  $doc = Document::find($value['doc']);
                //   if ($doc instanceof Document) {
                //      $doc->intattr2 = $doc->intattr2 + $value['amount'];
                //       $doc->save();
                // }
            }
            $conn->CommitTrans();
        } catch (\Exception $ee) {
            $conn->RollbackTrans();
            throw new \Exception($ee->message);
        }
    }

    // Список  типов операций
    public static function getTypes()
    {
        $list = array();
        $list[self::IN] = "Оплата от покупателя";
        $list[self::OUT] = "Оплата поставщику";
        $list[self::CASHIN] = "Поступление наличности";
        $list[self::CASHOUT] = "Снятие наличности";
        $list[self::TAX] = "Оплата  налогов";
        return $list;
    }

}
