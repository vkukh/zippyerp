<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\ERP\Entity\MoneyFund;
use \ZippyERP\ERP\Entity\Entry;
use \ZippyERP\ERP\Entity\Customer;
use \ZippyERP\ERP\Entity\Employee;
use \ZippyERP\ERP\Entity\SubConto;

/**
 * Класс-сущность  документ  приходный кассовый  ордер
 *
 */
class CashReceiptIn extends Document
{

    const TYPEOP_CUSTOMER = 1;   // Оплата заказа
    const TYPEOP_BANK = 2;   // Снятие  со  счета
    const TYPEOP_CASH = 3;   // Из  подотчета
    const TYPEOP_RET = 4;   // Из  магазина

    public function generateReport()
    {
        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "notes" => $this->headerdata['notes'],
            "amount" => \ZippyERP\ERP\Helper::fm($this->headerdata["amount"])
        );
        $optype = $this->headerdata['optype'];

        if ($optype == self::TYPEOP_CUSTOMER) {
            $header['optype'] = "Оплата от покупателя";
        }
        if ($optype == self::TYPEOP_CASH) {
            $header['optype'] = "Возврат из подотчета";
        }
        if ($optype == self::TYPEOP_BANK) {
            $header['optype'] = "Снятие с банковского счета";
        }
        if ($optype == self::TYPEOP_RET) {
            $header['optype'] = "Выручка   с розницы";
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
        if ($optype == self::TYPEOP_CUSTOMER) {

            $ret = Entry::AddEntry(30, 36, $this->headerdata['amount'], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 36, 0 - $this->headerdata['amount'] );
            $sc->setCustomer($this->headerdata['opdetail']);
            $sc->save();
        }
        if ($optype == self::TYPEOP_CASH) {
            $ret = Entry::AddEntry(30, 372, $this->headerdata['amount'], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 372, 0 - $this->headerdata['amount']);
            $sc->setEmployee($this->headerdata['opdetail']);
            $sc->save();
        }
        if ($optype == self::TYPEOP_BANK) {
            $ret = Entry::AddEntry(30, 31, $this->headerdata['amount'], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 31, 0 - $this->headerdata['amount']);
            $sc->setMoneyfund($this->headerdata['opdetail']);
            $sc->save();
        }
        if ($optype == self::TYPEOP_RET) {
            $store_id = $this->headerdata['opdetail']; // магазин
            $ret = Entry::AddEntry(30, 702, $this->headerdata['amount'], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 702, 0 - $this->headerdata['amount']);
            $sc->setExtCode($this->headerdata['opdetail']);
            $sc->save();

            $store = \ZippyERP\ERP\Entity\Store::load($store_id);
            if ($store->store_type == \ZippyERP\ERP\Entity\Store::STORE_TYPE_RET_SUM) {
                $nds = \ZippyERP\ERP\Helper::nds(true);
                Entry::AddEntry(702, 643, $nds * $this->headerdata['amount'], $this->document_id, $this->document_date);
            }
        }
        //касса
        $sc = new SubConto($this, 30, $this->headerdata['amount']);
        $sc->setMoneyfund($cash->id);
        $sc->save();

        if (strlen($ret) > 0)
            throw new \Exception($ret);
        return true;
    }

    // Список  типов операций
    public static function getTypes()
    {
        $list = array();
        $list[self::TYPEOP_CUSTOMER] = "Оплата покупателя";
        $list[self::TYPEOP_BANK] = "Снятие  со  счета";
        $list[self::TYPEOP_CASH] = "Приход  с  подотчета";
        $list[self::TYPEOP_RET] = "Приход с розницы";
        return $list;
    }

}
