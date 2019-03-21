<?php

namespace App\Entity\Doc;

use App\Entity\Entry;
use App\Entity\AccountEntry;
use App\System;
use App\Helper as H;

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

    public function generateReport() {


        $types = $this->getTypes();

        $detail = array();

        foreach ($this->detaildata as $value) {

            $detail[] = array(
                "type" => $types[$value['optype']],
                "cust" => $value['customername'],
                "amount" => H::famt($value['amount']),
                "comment" => $value['comment']);
        };

        $firm = System::getOptions('firmdetail');
        $header = array(
            'date' => date('d.m.Y', $this->document_date),
            'bankaccount' => $firm['bankaccount'],
            "document_number" => $this->document_number
                // "amountstr" => \App\Util::ucfirst(\ZippyERP\ERP\Util::money2str(H::famt($total )))
        );

        $report = new \App\Report('bankstatement.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute() {


        foreach ($this->detaildata as $value) {
            if ($value['noentry'] === 'true') //не выполнять проводки
                continue;

            if ($value['optype'] == self::OUT_CARD) //оплата кредиткой. Проводки в накладной
                AccountEntry::AddEntry(31, "338", $value['amount'], $this->document_id);
            $sc = new Entry($this->document_id, "338", 0 - $value['amount']);
            $sc->setCustomer($value['customer']);  //склад  то  есть магазин
            $sc->save(); {
                
            }



            // оплата  поставщику
            if ($value['optype'] == self::OUT) {
                $acc = 63;
                if ($value['prepayment'] == 'true') {  //предоплата
                    $acc = 371;
                }


                AccountEntry::AddEntry($acc, 31, $value['amount'], $this->document_id);

                $sc = new Entry($this->document_id, $acc, $value['amount']);
                $sc->setCustomer($value['customer']);
                $sc->save();
                $sc = new Entry($this->document_id, 31, 0 - $value['amount']);

                if ($value['prepayment'] == 'true') {
                    $sc->setExtCode(H::TYPEOP_CUSTOMER_OUT_PREV);
                } else {
                    $sc->setExtCode(H::TYPEOP_CUSTOMER_OUT);
                }

                $sc->save();

                if ($value['nds'] > 0) {
                    AccountEntry::AddEntry(644, $acc, $value['nds'], $this->document_id);
                    $sc = new Entry($this->document_id, $acc, 0 - $value['nds']);
                    $sc->setCustomer($value['customer']);
                    $sc->save();
                }
            }
            // возврат    поставщику
            if ($value['optype'] == self::IN_BACK) {
                $acc = 63;

                AccountEntry::AddEntry( 31,$acc,   $value['amount'], $this->document_id);

                $sc = new Entry($this->document_id, $acc, 0 - $value['amount']);
                $sc->setCustomer($value['customer']);
                $sc->save();
                $sc = new Entry($this->document_id, 31, $value['amount']);

                $sc->setExtCode(H::TYPEOP_CUSTOMER_IN_BACK);
                $sc->save();
            }

            // оплата  от покупателя
            if ($value['optype'] == self::IN) {

                $acc = 36;
                if ($value['prepayment'] == 'true') {  //предоплата
                    $acc = 681;
                }

                AccountEntry::AddEntry('31', $acc, $value['amount'], $this->document_id);
                $sc = new Entry($this->document_id, $acc, 0 - $value['amount']);
                $sc->setCustomer($value['customer']);
                $sc->save();
                $sc = new Entry($this->document_id, 31, $value['amount']);

                if ($value['prepayment'] == 'true') {
                    $sc->setExtCode(H::TYPEOP_CUSTOMER_IN_PREV);
                } else {
                    $sc->setExtCode(H::TYPEOP_CUSTOMER_IN);
                }

                $sc->save();

                if ($value['nds'] > 0) {
                    AccountEntry::AddEntry(36, 643, $value['nds'], $this->document_id);
                    $sc = new Entry($this->document_id, 36, $value['nds']);
                    $sc->setCustomer($value['customer']);
                    $sc->save();
                }
            }

            // возврат от покупателя
            if ($value['optype'] == self::OUT_BACK) {

                $acc = 681;


                AccountEntry::AddEntry('31', $acc, 0 - $value['amount'], $this->document_id);
                $sc = new Entry($this->document_id, $acc, $value['amount']);
                $sc->setCustomer($value['customer']);
                $sc->save();
                $sc = new Entry($this->document_id, 31, 0 - $value['amount']);

                $sc->setExtCode(H::TYPEOP_CUSTOMER_OUT_BACK);
                $sc->save();
            }

            // оплата  налогов
            if ($value['optype'] == self::TAX) {

                $acc = 641;
                if ($value['tax'] > 200)
                    $acc = 651;
                AccountEntry::AddEntry($acc, "31", $value['amount'], $this->document_id);

                $sc = new Entry($this->document_id, $acc, $value['amount']);
                $sc->setExtCode($value['tax']); // код налога
                $sc->save();
                $sc = new Entry($this->document_id, 31, 0 - $value['amount']);

                $sc->setExtCode($value['tax']); // код налога
                $sc->save();
            }

            //накладные  расходы
            if ($value['optype'] == self::OUT_COMMON) {


                AccountEntry::AddEntry(94, "31", $value['amount'], $this->document_id);

                $sc = new Entry($this->document_id, 94, $value['amount']);
                $sc->save();
                $sc = new Entry($this->document_id, 31, 0 - $value['amount']);

                $sc->setExtCode(H::TYPEOP_COMMON_EXPENCES);
                $sc->save();
            }

            // снятие  наличности
            if ($value['optype'] == self::CASHIN) {
                $cash = MoneyFund::getCash();
                AccountEntry::AddEntry('30', "31", $value['amount'], $this->document_id);
                $sc = new Entry($this->document_id, 31, 0 - $value['amount']);

                $sc->setExtCode(H::TYPEOP_BANK_IN);
                $sc->save();
                $sc = new Entry($this->document_id, 30, $value['amount']);

                $sc->setExtCode(H::TYPEOP_BANK_IN);
                $sc->save();
            }
            // опприходование  наличности
            if ($value['optype'] == self::CASHOUT) {

                AccountEntry::AddEntry('31', "30", $value['amount'], $this->document_id);
                $sc = new Entry($this->document_id, 31, $value['amount']);

                $sc->setExtCode(H::TYPEOP_BANK_OUT);
                $sc->save();
                $sc = new Entry($this->document_id, 30, 0 - $value['amount']);

                $sc->setExtCode(H::TYPEOP_BANK_IN);
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
        $list[self::CASHIN] = "Поступила наличность";
        $list[self::CASHOUT] = "Снятие наличности";
        $list[self::TAX] = "Оплата налогов";
        $list[self::IN_BACK] = "Возврат от поставщика";
        $list[self::OUT_BACK] = "Возврат покупателю";
        $list[self::OUT_COMMON] = "Общие накладные расходы";
        $list[self::OUT_CARD] = "Оприходоание оплат кредиткой";
        return $list;
    }

}
