<?php

namespace ZippyERP\ERP\Entity\Doc;

use ZippyERP\ERP\Consts as C;
use ZippyERP\ERP\Entity\Entry;
use ZippyERP\ERP\Entity\MoneyFund;
use ZippyERP\ERP\Entity\SubConto;

/**
 * Класс-сущность  документ  приходный кассовый  ордер
 *
 */
class CashReceiptIn extends Document
{

    public function generateReport()
    {
        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "notes" => $this->headerdata['notes'],
            "amount" => \ZippyERP\ERP\Helper::fm($this->headerdata["amount"])
        );
        $optype = $this->headerdata['optype'];

        if ($optype == C::TYPEOP_CUSTOMER_IN) {
            $header['optype'] = "Оплата покупця";
        }
        if ($optype == C::TYPEOP_CASH_IN) {
            $header['optype'] = "Повернення з підзвіту";
        }
        if ($optype == C::TYPEOP_BANK_IN) {
            $header['optype'] = "Зняття з банкіського рахунку";
        }
        if ($optype == C::TYPEOP_RET_IN) {
            $header['optype'] = "Выручка з роздрібу";
        }
        if ($optype == C::TYPEOP_CUSTOMER_IN_BACK) {

            $header['optype'] = "Повернення постачальнику";
        }
        $header['opdetail'] = $this->headerdata["opdetailname"];

        $report = new \ZippyERP\ERP\Report('cashreceiptin.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute()
    {

        $cash = MoneyFund::getCash();

        $ret = "";
        $optype = $this->headerdata['optype'];
        if ($optype == C::TYPEOP_CUSTOMER_IN) {

            $ret = Entry::AddEntry(30, 36, $this->headerdata['amount'], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 36, 0 - $this->headerdata['amount']);
            $sc->setCustomer($this->headerdata['opdetail']);
            $sc->save();
            $sc = new SubConto($this, 30, $this->headerdata['amount']);
            $sc->setMoneyfund($cash->id);
            $sc->setExtCode(C::TYPEOP_CUSTOMER_IN);
            $sc->save();
        }
        if ($optype == C::TYPEOP_CUSTOMER_IN_BACK) {
            //сторно
            $ret = Entry::AddEntry(63, 30, 0 - $this->headerdata['amount'], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 63, 0 - $this->headerdata['amount']);
            $sc->setCustomer($this->headerdata['opdetail']);

            $sc->save();
            $sc = new SubConto($this, 30, $this->headerdata['amount']);
            $sc->setMoneyfund($cash->id);
            $sc->setExtCode(C::TYPEOP_CUSTOMER_IN_BACK);
            $sc->save();
        }
        if ($optype == C::TYPEOP_CASH_IN) {
            $ret = Entry::AddEntry(30, 372, $this->headerdata['amount'], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 372, 0 - $this->headerdata['amount']);
            $sc->setEmployee($this->headerdata['opdetail']);
            $sc->save();
            $sc = new SubConto($this, 30, $this->headerdata['amount']);
            $sc->setMoneyfund($cash->id);
            $sc->setExtCode(C::TYPEOP_CASH_IN);
            $sc->save();
        }
        if ($optype == C::TYPEOP_BANK_IN) {
            $ret = Entry::AddEntry(30, 31, $this->headerdata['amount'], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 31, 0 - $this->headerdata['amount']);
            $sc->setMoneyfund($this->headerdata['opdetail']);
            $sc->setExtCode(C::TYPEOP_BANK_OUT);
            $sc = new SubConto($this, 30, $this->headerdata['amount']);
            $sc->setMoneyfund($cash->id);
            $sc->setExtCode(C::TYPEOP_BANK_IN);

            $sc->save();
        }
        if ($optype == C::TYPEOP_RET_IN) {
            $store_id = $this->headerdata['opdetail']; // магазин
            $ret = Entry::AddEntry(30, 702, $this->headerdata['amount'], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 702, 0 - $this->headerdata['amount']);
            $sc->setExtCode($this->headerdata['opdetail']);
            $sc->save();
            $sc = new SubConto($this, 30, $this->headerdata['amount']);
            $sc->setMoneyfund($cash->id);
            $sc->setExtCode(C::TYPEOP_RET_IN);
            $sc->save();

            $store = \ZippyERP\ERP\Entity\Store::load($store_id);
            if ($store->store_type == \ZippyERP\ERP\Entity\Store::STORE_TYPE_RET_SUM) {
                $nds = \ZippyERP\ERP\Helper::nds(true);
                Entry::AddEntry(702, 643, $nds * $this->headerdata['amount'], $this->document_id, $this->document_date);
            }
        }


        if (strlen($ret) > 0)
            throw new \Exception($ret);
        return true;
    }

    // Список  типов операций
    public static function getTypes()
    {
        $list = array();
        $list[C::TYPEOP_CUSTOMER_IN] = "Оплата покупцеві";
        $list[C::TYPEOP_CUSTOMER_IN_BACK] = "Повернення від  постачальника";
        $list[C::TYPEOP_BANK_IN] = "Зняття  з рахунку";
        $list[C::TYPEOP_CASH_IN] = "Прибуток  з  пілзвіту";
        $list[C::TYPEOP_RET_IN] = "Прибуток з роздрібу";
        return $list;
    }

}
