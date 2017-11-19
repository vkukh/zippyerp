<?php

namespace ZippyERP\ERP\Entity\Doc;

use ZippyERP\ERP\Consts as C;
use ZippyERP\ERP\Entity\Entry;
use ZippyERP\ERP\Entity\SubConto;
use ZippyERP\ERP\Entity\MoneyFund;
use ZippyERP\ERP\Helper as H;

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
    const IN_BACK = 6; //возврат от поставщика
    const OUT_BACK = 7; //возврат покупателю
    const OUT_COMMON = 8; //Общие накладные расходы
    const OUT_CARD = 9; //Оплата кредиткой

    public function generateReport()
    {


        $types = $this->getTypes();

        $detail = array();

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
            if ($value['optype'] == self::CARD) //оплата кредиткой. Проводки в накладной
                continue;


            // оплата  поставщику
            if ($value['optype'] == self::OUT) {
                $acc = 63;
                if ($value['prepayment'] == 'true') {  //предоплата
                    $acc = 371;
                }


                Entry::AddEntry($acc, 31, $value['amount'], $this->document_id, $this->document_date);

                $sc = new SubConto($this, $acc, $value['amount']);
                $sc->setCustomer($value['customer']);
                $sc->save();
                $sc = new SubConto($this, 31, 0 - $value['amount']);
                $sc->setMoneyfund($this->headerdata['bankaccount']);
                if ($value['prepayment'] == 'true') {
                    $sc->setExtCode(C::TYPEOP_CUSTOMER_OUT_PREV);
                } else {
                    $sc->setExtCode(C::TYPEOP_CUSTOMER_OUT);
                }

                $sc->save();

                if ($value['nds'] > 0) {
                    Entry::AddEntry(644, $acc, $value['nds'], $this->document_id, $this->document_date);
                    $sc = new SubConto($this, $acc, 0 - $value['nds']);
                    $sc->setCustomer($value['customer']);
                    $sc->save();
                }
            }
            // возврат  от поставщика
            if ($value['optype'] == self::IN_BACK) {
                $acc = 63;

                Entry::AddEntry($acc, 31, 0 - $value['amount'], $this->document_id, $this->document_date);

                $sc = new SubConto($this, $acc, 0 - $value['amount']);
                $sc->setCustomer($value['customer']);
                $sc->save();
                $sc = new SubConto($this, 31, $value['amount']);
                $sc->setMoneyfund($this->headerdata['bankaccount']);
                $sc->setExtCode(C::TYPEOP_CUSTOMER_IN_BACK);
                $sc->save();
            }

            // оплата  от покупателя
            if ($value['optype'] == self::IN) {

                $acc = 36;
                if ($value['prepayment'] == 'true') {  //предоплата
                    $acc = 681;
                }

                Entry::AddEntry('31', $acc, $value['amount'], $this->document_id, $this->document_date);
                $sc = new SubConto($this, $acc, 0 - $value['amount']);
                $sc->setCustomer($value['customer']);
                $sc->save();
                $sc = new SubConto($this, 31, $value['amount']);
                $sc->setMoneyfund($this->headerdata['bankaccount']);
                if ($value['prepayment'] == 'true') {
                    $sc->setExtCode(C::TYPEOP_CUSTOMER_IN_PREV);
                } else {
                    $sc->setExtCode(C::TYPEOP_CUSTOMER_IN);
                }

                $sc->save();

                if ($value['nds'] > 0) {
                    Entry::AddEntry(36, 643, $value['nds'], $this->document_id, $this->document_date);
                    $sc = new SubConto($this, 36, $value['nds']);
                    $sc->setCustomer($value['customer']);
                    $sc->save();
                }
            }

            // возврат покупателю
            if ($value['optype'] == self::OUT_BACK) {

                $acc = 36;


                Entry::AddEntry('31', $acc, 0 - $value['amount'], $this->document_id, $this->document_date);
                $sc = new SubConto($this, $acc, $value['amount']);
                $sc->setCustomer($value['customer']);
                $sc->save();
                $sc = new SubConto($this, 31, 0 - $value['amount']);
                $sc->setMoneyfund($this->headerdata['bankaccount']);
                $sc->setExtCode(C::TYPEOP_CUSTOMER_OUT_BACK);
                $sc->save();
            }

            // оплата  налогов
            if ($value['optype'] == self::TAX) {

                $acc = 641;
                if ($value['tax'] > 200)
                    $acc = 651;
                Entry::AddEntry($acc, "31", $value['amount'], $this->document_id, $this->document_date);

                $sc = new SubConto($this, $acc, $value['amount']);
                $sc->setExtCode($value['tax']); // код налога
                $sc->save();
                $sc = new SubConto($this, 31, 0 - $value['amount']);
                $sc->setMoneyfund($this->headerdata['bankaccount']);
                $sc->setExtCode($value['tax']); // код налога
                $sc->save();
            }

            //накладные  расходы
            if ($value['optype'] == self::OUT_COMMON) {


                Entry::AddEntry(94, "31", $value['amount'], $this->document_id, $this->document_date);

                $sc = new SubConto($this, 94, $value['amount']);
                $sc->save();
                $sc = new SubConto($this, 31, 0 - $value['amount']);
                $sc->setMoneyfund($this->headerdata['bankaccount']);
                $sc->setExtCode(C::TYPEOP_COMMON_EXPENCES);
                $sc->save();
            }

            // снятие  наличности
            if ($value['optype'] == self::CASHIN) {
                $cash = MoneyFund::getCash();
                Entry::AddEntry('30', "31", $value['amount'], $this->document_id, $this->document_date);
                $sc = new SubConto($this, 31, 0 - $value['amount']);
                $sc->setMoneyfund($this->headerdata['bankaccount']);
                $sc->setExtCode(C::TYPEOP_BANK_IN);
                $sc->save();
                $sc = new SubConto($this, 30, $value['amount']);
                $sc->setMoneyfund($cash->id);
                $sc->setExtCode(C::TYPEOP_BANK_IN);
                $sc->save();
            }
            // опприходование  наличности
            if ($value['optype'] == self::CASHOUT) {
                $cash = MoneyFund::getCash();
                Entry::AddEntry('31', "30", $value['amount'], $this->document_id, $this->document_date);
                $sc = new SubConto($this, 31, $value['amount']);
                $sc->setMoneyfund($this->headerdata['bankaccount']);
                $sc->setExtCode(C::TYPEOP_BANK_OUT);
                $sc->save();
                $sc = new SubConto($this, 30, 0 - $value['amount']);
                $sc->setMoneyfund($cash->id);
                $sc->setExtCode(C::TYPEOP_BANK_IN);
                $sc->save();
            }


            // $this->AddConnectedDoc($value['doc']);
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
        $list[self::IN_BACK] = "Возврат от  поставщика";
        $list[self::OUT_BACK] = "Возврат покупателю";
        $list[self::OUT_COMMON] = "Общие накладные расходы";
        $list[self::OUT_CARD] = "Оплата кредиткой";
        return $list;
    }

}
