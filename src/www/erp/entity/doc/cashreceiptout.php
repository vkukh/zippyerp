<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\ERP\Entity\MoneyFund;
use \ZippyERP\ERP\Entity\Entry;
use \ZippyERP\ERP\Entity\Customer;
use \ZippyERP\ERP\Entity\SubConto;

/**
 * Класс-сущность  документ  расходный кассовый  ордер
 *
 */
class CashReceiptOut extends Document
{

    const TYPEOP_CUSTOMER = 1;   // Оплата заказа
    const TYPEOP_BANK = 2;   // Перечисление на счет
    const TYPEOP_CASH = 3;   // В  подотчет

    public function generateReport()
    {
        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "notes" => $this->headerdata['notes'],
            "amount" => \ZippyERP\ERP\Helper::fm($this->headerdata["amount"])
        );
        $optype = $this->headerdata['optype'];

        if ($optype == self::TYPEOP_CUSTOMER) {

            $header['optype'] = "Оплата поставщику";
        }
        if ($optype == self::TYPEOP_CASH) {

            $header['optype'] = "В  подотчет";
        }
        if ($optype == self::TYPEOP_BANK) {

            $header['optype'] = "Перечисление на счет";
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
        if ($optype == self::TYPEOP_CUSTOMER) {
            $ret = Entry::AddEntry(63, 30, $this->headerdata['amount'], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 63, $this->headerdata['amount']);
            $sc->setCustomer($this->headerdata['opdetail']);
            $sc->save();
        }
        if ($optype == self::TYPEOP_CASH) {
            $ret = Entry::AddEntry(372, 30, $this->headerdata['amount'], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 372, $this->headerdata['amount']);
            $sc->setEmployee($this->headerdata['opdetail']);
            $sc->save();
        }
        if ($optype == self::TYPEOP_BANK) {
            $ret = Entry::AddEntry(31, 30, $this->headerdata['amount'], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 31, $this->headerdata['amount']);
            $sc->setMoneyfund($this->headerdata['opdetail']);
            $sc->save();
        }
        //касса
        $sc = new SubConto($this, 30, 0 - $this->headerdata['amount']);
        $sc->setMoneyfund($this->headerdata['opdetail']);
        $sc->save();
        return true;
    }

    // Список  типов операций
    public static function getTypes()
    {
        $list = array();
        $list[self::TYPEOP_CUSTOMER] = "Оплата поставщику";
        $list[self::TYPEOP_BANK] = "Пополнение  счета";
        $list[self::TYPEOP_CASH] = "Расход на подотчета";
        return $list;
    }

}
