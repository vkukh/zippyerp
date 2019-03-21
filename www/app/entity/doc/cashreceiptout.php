<?php

namespace App\Entity\Doc;

use App\Helper as H;
use App\Entity\Entry;
use App\Entity\AccountEntry;

/**
 * Класс-сущность  документ  расходный кассовый  ордер
 *
 */
class CashReceiptOut extends Document
{

    public function generateReport() {
        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "notes" => $this->headerdata['notes'],
            "amount" => H::famt($this->headerdata["amount"])
        );
        $optype = $this->headerdata['optype'];

        if ($optype == H::TYPEOP_CUSTOMER_OUT) {

            $header['optype'] = "Оплата поставщику";
        }
        if ($optype == H::TYPEOP_CASH_OUT) {

            $header['optype'] = "В  подотчет";
        }
        if ($optype == H::TYPEOP_BANK_OUT) {

            $header['optype'] = "Перечислить на счет";
        }
        if ($optype == H::TYPEOP_CUSTOMER_OUT_BACK) {

            $header['optype'] = "Возврат покупателю";
        }
        $header['opdetail'] = $this->headerdata["opdetailname"];
        $report = new \App\Report('cashreceiptout.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute() {

        $optype = $this->headerdata['optype'];
        if ($optype == H::TYPEOP_CUSTOMER_OUT) {
            AccountEntry::AddEntry(63, 30, $this->headerdata['amount'], $this->document_id);
            $sc = new Entry($this->document_id, 63, $this->headerdata['amount']);
            $sc->setCustomer($this->headerdata['opdetail']);
            $sc->save();
            $sc = new Entry($this->document_id, 30, 0 - $this->headerdata['amount']);

            $sc->setExtCode(H::TYPEOP_CUSTOMER_OUT);
            $sc->save();
        }
        if ($optype == H::TYPEOP_CUSTOMER_OUT_BACK) {
            //сторно
            AccountEntry::AddEntry(30, 36, 0 - $this->headerdata['amount'], $this->document_id);
            $sc = new Entry($this->document_id, 36, $this->headerdata['amount']);
            $sc->setCustomer($this->headerdata['opdetail']);
            $sc->save();
            $sc = new Entry($this->document_id, 30, 0 - $this->headerdata['amount']);

            $sc->setExtCode(C::TYPEOP_CUSTOMER_OUT_BACK);
            $sc->save();
        }
        if ($optype == H::TYPEOP_CASH_OUT) {
            AccountEntry::AddEntry(372, 30, $this->headerdata['amount'], $this->document_id);
            $sc = new Entry($this->document_id, 372, $this->headerdata['amount']);
            $sc->setEmployee($this->headerdata['opdetail']);
            $sc->save();
            $sc = new Entry($this->document_id, 30, 0 - $this->headerdata['amount']);

            $sc->setExtCode(H::TYPEOP_CASH_OUT);
            $sc->save();
        }
        if ($optype == H::TYPEOP_COMMON_EXPENCES) {
            AccountEntry::AddEntry(94, 30, $this->headerdata['amount'], $this->document_id);
            $sc = new Entry($this->document_id, 94, $this->headerdata['amount']);
            $sc->save();
            $sc = new Entry($this->document_id, 30, 0 - $this->headerdata['amount']);

            $sc->setExtCode(H::TYPEOP_COMMON_EXPENCES);
            $sc->save();
        }
        if ($optype == H::TYPEOP_BANK_OUT) {
            AccountEntry::AddEntry(31, 30, $this->headerdata['amount'], $this->document_id);
            $sc = new Entry($this->document_id, 31, $this->headerdata['amount']);

            $sc->setExtCode(H::TYPEOP_BANK_OUT);
            $sc->save();
            $sc = new Entry($this->document_id, 30, 0 - $this->headerdata['amount']);

            $sc->setExtCode(H::TYPEOP_BANK_OUT);
            $sc->save();
        }


        return true;
    }

    // Список  типов операций
    public static function getTypes() {
        $list = array();
        $list[H::TYPEOP_CUSTOMER_OUT] = "Оплата поставщику";
        $list[H::TYPEOP_CUSTOMER_OUT_BACK] = "Возврат покупателю";
        $list[H::TYPEOP_BANK_OUT] = "Пополнение  счета";
        $list[H::TYPEOP_CASH_OUT] = "Выдача в подотчет";
        $list[H::TYPEOP_COMMON_EXPENCES] = "Общие накладные расзоды";
        return $list;
    }

}
