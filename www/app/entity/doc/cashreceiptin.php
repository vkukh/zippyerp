<?php

namespace App\Entity\Doc;

use App\Helper as H;
use App\Entity\AccountEntry;
use App\Entity\Entry;

/**
 * Класс-сущность  документ  приходный кассовый  ордер
 *
 */
class CashReceiptIn extends Document
{

    public function generateReport() {
        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "notes" => $this->headerdata['notes'],
            "amount" => H::famt($this->headerdata["amount"])
        );
        $optype = $this->headerdata['optype'];

        if ($optype == H::TYPEOP_CUSTOMER_IN) {
            $header['optype'] = "Оплата покупателя";
        }
        if ($optype == H::TYPEOP_CASH_IN) {
            $header['optype'] = "Возврат с  подотчета";
        }
        if ($optype == H::TYPEOP_BANK_IN) {
            $header['optype'] = "Снятие   со  счета";
        }
        if ($optype == H::TYPEOP_RET_IN) {
            $header['optype'] = "Выручка с  подотчета";
        }
        if ($optype == H::TYPEOP_CUSTOMER_IN_BACK) {

            $header['optype'] = "Возврат поставщика";
        }
        $header['opdetail'] = $this->headerdata["opdetailname"];

        $report = new \App\Report('cashreceiptin.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute() {


        $ret = "";
        $optype = $this->headerdata['optype'];
        if ($optype == H::TYPEOP_CUSTOMER_IN) {

            $ret = AccountEntry::AddEntry(30, 36, $this->headerdata['amount'], $this->document_id);
            $sc = new Entry($this->document_id, 36, 0 - $this->headerdata['amount']);
            $sc->setCustomer($this->headerdata['opdetail']);
            $sc->save();
            $sc = new Entry($this, 30, $this->headerdata['amount']);

            $sc->setExtCode(H::TYPEOP_CUSTOMER_IN);
            $sc->save();
        }
        if ($optype == H::TYPEOP_CUSTOMER_IN_BACK) {
            //сторно
            $ret = AccountEntry::AddEntry(63, 30, 0 - $this->headerdata['amount'], $this->document_id);
            $sc = new Entry($this->document_id, 63, 0 - $this->headerdata['amount']);
            $sc->setCustomer($this->headerdata['opdetail']);

            $sc->save();
            $sc = new Entry($this->document_id, 30, $this->headerdata['amount']);

            $sc->setExtCode(H::TYPEOP_CUSTOMER_IN_BACK);
            $sc->save();
        }
        if ($optype == H::TYPEOP_CASH_IN) {
            $ret = AccountEntry::AddEntry(30, 372, $this->headerdata['amount'], $this->document_id);
            $sc = new Entry($this->document_id, 372, 0 - $this->headerdata['amount']);
            $sc->setEmployee($this->headerdata['opdetail']);
            $sc->save();
            $sc = new Entry($this->document_id, 30, $this->headerdata['amount']);

            $sc->setExtCode(H::TYPEOP_CASH_IN);
            $sc->save();
        }
        if ($optype == H::TYPEOP_CUSTOMER_IN_ADVANCE) {
            $ret = AccountEntry::AddEntry(30, 681, $this->headerdata['amount'], $this->document_id);
            $sc = new Entry($this->document_id, 681, 0 - $this->headerdata['amount']);
            $sc->setCustomer($this->headerdata['opdetail']);
            $sc->save();
            $sc = new Entry($this->document_id, 30, $this->headerdata['amount']);
            $sc->setExtCode(H::TYPEOP_CUSTOMER_IN_ADVANCE);
            $sc->save();
        }
        if ($optype == H::TYPEOP_BANK_IN) {
            $ret = AccountEntry::AddEntry(30, 31, $this->headerdata['amount'], $this->document_id);
            $sc = new Entry($this->document_id, 31, 0 - $this->headerdata['amount']);

            $sc->setExtCode(H::TYPEOP_BANK_OUT);
            $sc = new Entry($this->document_id, 30, $this->headerdata['amount']);

            $sc->setExtCode(H::TYPEOP_BANK_IN);

            $sc->save();
        }
        if ($optype == H::TYPEOP_RET_IN) {
            $ret = AccountEntry::AddEntry(30, 337, $this->headerdata['amount'], $this->document_id);
            $sc = new Entry($this->document_id, 337, 0 - $this->headerdata['amount']);
            $sc->setExtCode($this->headerdata['opdetail']);
            $sc->save();
            $sc = new Entry($this->document_id, 30, $this->headerdata['amount']);
            $sc->setExtCode(H::TYPEOP_RET_IN);
            $sc->save();
        }


        if (strlen($ret) > 0)
            throw new \Exception($ret);
        return true;
    }

    // Список  типов операций
    public static function getTypes() {
        $list = array();
        $list[H::TYPEOP_CUSTOMER_IN] = "Оплата от покупателя";
        $list[H::TYPEOP_CUSTOMER_IN_BACK] = "Возврат от  поставщика";
        $list[H::TYPEOP_BANK_IN] = "Снятие со  счета";
        $list[H::TYPEOP_CASH_IN] = "Приход с подотчета";
        $list[H::TYPEOP_RET_IN] = "Приход с розницы";
        $list[H::TYPEOP_CUSTOMER_IN_ADVANCE] = "Аванс  от покупателя";
        return $list;
    }

}
