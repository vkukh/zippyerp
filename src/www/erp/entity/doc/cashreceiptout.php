<?php

namespace ZippyERP\ERP\Entity\Doc;

use ZippyERP\ERP\Consts as C;
use ZippyERP\ERP\Entity\Entry;
use ZippyERP\ERP\Entity\MoneyFund;
use ZippyERP\ERP\Entity\SubConto;

/**
 * Класс-сущность  документ  расходный кассовый  ордер
 *
 */
class CashReceiptOut extends Document
{

    public function generateReport()
    {
        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "notes" => $this->headerdata['notes'],
            "amount" => \ZippyERP\ERP\Helper::fm($this->headerdata["amount"])
        );
        $optype = $this->headerdata['optype'];

        if ($optype == C::TYPEOP_CUSTOMER_OUT) {

            $header['optype'] = "Оплата постачальнику";
        }
        if ($optype == C::TYPEOP_CASH_OUT) {

            $header['optype'] = "В  підзвіт";
        }
        if ($optype == C::TYPEOP_BANK_OUT) {

            $header['optype'] = "Переказ на  роахунок";
        }
        if ($optype == C::TYPEOP_CUSTOMER_OUT_BACK) {

            $header['optype'] = "Повернення покупцеві";
        }
        $header['opdetail'] = $this->headerdata["opdetailname"];
        $report = new \ZippyERP\ERP\Report('cashreceiptout.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute()
    {
        $mf = MoneyFund::getCash();
        $optype = $this->headerdata['optype'];
        if ($optype == C::TYPEOP_CUSTOMER_OUT) {
            Entry::AddEntry(63, 30, $this->headerdata['amount'], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 63, $this->headerdata['amount']);
            $sc->setCustomer($this->headerdata['opdetail']);
            $sc->save();
            $sc = new SubConto($this, 30, 0 - $this->headerdata['amount']);
            $sc->setMoneyfund($mf->id);
            $sc->setExtCode(C::TYPEOP_CUSTOMER_OUT);
            $sc->save();
        }
        if ($optype == C::TYPEOP_CUSTOMER_OUT_BACK) {
            //сторно
            Entry::AddEntry(30, 36, 0 - $this->headerdata['amount'], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 36, $this->headerdata['amount']);
            $sc->setCustomer($this->headerdata['opdetail']);
            $sc->save();
            $sc = new SubConto($this, 30, 0 - $this->headerdata['amount']);
            $sc->setMoneyfund($mf->id);
            $sc->setExtCode(C::TYPEOP_CUSTOMER_OUT_BACK);
            $sc->save();
        }
        if ($optype == C::TYPEOP_CASH_OUT) {
            Entry::AddEntry(372, 30, $this->headerdata['amount'], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 372, $this->headerdata['amount']);
            $sc->setEmployee($this->headerdata['opdetail']);
            $sc->save();
            $sc = new SubConto($this, 30, 0 - $this->headerdata['amount']);
            $sc->setMoneyfund($mf->id);
            $sc->setExtCode(C::TYPEOP_CASH_OUT);
            $sc->save();
        }
        if ($optype == C::TYPEOP_COMMON_EXPENCES) {
            Entry::AddEntry(94, 30, $this->headerdata['amount'], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 94, $this->headerdata['amount']);
            $sc->save();
            $sc = new SubConto($this, 30, 0 - $this->headerdata['amount']);
            $sc->setMoneyfund($mf->id);
            $sc->setExtCode(C::TYPEOP_COMMON_EXPENCES);
            $sc->save();
        }
        if ($optype == C::TYPEOP_BANK_OUT) {
            Entry::AddEntry(31, 30, $this->headerdata['amount'], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 31, $this->headerdata['amount']);
            $sc->setMoneyfund($this->headerdata['opdetail']);
            $sc->setExtCode(C::TYPEOP_BANK_OUT);
            $sc->save();
            $sc = new SubConto($this, 30, 0 - $this->headerdata['amount']);
            $sc->setMoneyfund($mf->id);
            $sc->setExtCode(C::TYPEOP_BANK_OUT);
            $sc->save();
        }


        return true;
    }

    // Список  типов операций
    public static function getTypes()
    {
        $list = array();
        $list[C::TYPEOP_CUSTOMER_OUT] = "Оплата постачальнику";
        $list[C::TYPEOP_CUSTOMER_OUT_BACK] = "Повернення покупцеві";
        $list[C::TYPEOP_BANK_OUT] = "Поповнення рахунку";
        $list[C::TYPEOP_CASH_OUT] = "Видаьтки для пілзвіту";
        $list[C::TYPEOP_COMMON_EXPENCES] = "Загальні накладні видатки";
        return $list;
    }

}
