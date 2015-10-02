<?php

namespace ZippyERP\ERP\Entity\Doc;

use ZippyERP\ERP\Entity\Account;
use ZippyERP\ERP\Entity\Entry;
use ZippyERP\ERP\Entity\Customer;
use \ZippyERP\ERP\Helper as H;
use \ZippyERP\ERP\Entity\SubConto;

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


        foreach ($this->detaildata as $value) {
            if ($value['noentry'] === 'true') //не выполнять проводки
                continue;



            if ($value['optype'] == self::OUT) {
                Entry::AddEntry('63', "31", $value['amount'], $this->document_id, $this->document_date);
                $sc = new SubConto($this, 63, $value['amount']);
                $sc->setCustomer($value['customer']);
                $sc->save();
                $sc = new SubConto($this, 31, 0 - $value['amount']);
                $sc->setMoneyfund($this->headerdata['bankaccount']);
                $sc->save();
            }
            if ($value['optype'] == self::IN) {
                Entry::AddEntry('31', "36", $value['amount'], $this->document_id, $this->document_date);
                $sc = new SubConto($this, 36, 0 - $value['amount']);
                $sc->setCustomer($value['customer']);
                $sc->save();
                $sc = new SubConto($this, 31, $value['amount']);
                $sc->setMoneyfund($this->headerdata['bankaccount']);
                $sc->save();
            }
            if ($value['optype'] == self::TAX) {
                //Entry::AddEntry('643', "644", $value['amount'], $this->document_id, $this->document_date);
                Entry::AddEntry('643', "31", $value['amount'], $this->document_id, $this->document_date);
                $sc = new SubConto($this, 643, $value['amount']);
                $sc->setCustomer($value['customer']);
                $sc->save();


                $sc = new SubConto($this, 31, 0 - $value['amount']);
                $sc->setMoneyfund($this->headerdata['bankaccount']);
                $sc->setExtCode($value['tax']); // код налога
                $sc->save();
            }
            if ($value['optype'] == self::CASHIN) {
                $cash = MoneyFund::getCash();
                Entry::AddEntry('30', "31", $value['amount'], $this->document_id, $this->document_date);
                $sc = new SubConto($this, 31, 0 - $value['amount']);
                $sc->setMoneyfund($this->headerdata['bankaccount']);
                $sc->save();
            }
            if ($value['optype'] == self::CASHOUT) {
                $cash = MoneyFund::getCash();
                Entry::AddEntry('31', "30", $value['amount'], $this->document_id, $this->document_date);
                $sc = new SubConto($this, 31, $value['amount']);
                $sc->setMoneyfund($this->headerdata['bankaccount']);
                $sc->save();
            }




           // $this->AddConnectedDoc($value['doc']);
        }
    }
        // Список  типов операций
        public static function getTypes() {
            $list = array();
            $list[self::IN] = "Оплата от покупателя";
            $list[self::OUT] = "Оплата поставщику";
            $list[self::CASHIN] = "Поступление наличности";
            $list[self::CASHOUT] = "Снятие наличности";
            $list[self::TAX] = "Оплата  налогов";
            return $list;
        }
    }
