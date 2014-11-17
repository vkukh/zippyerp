<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\ERP\Entity\MoneyFund;
use \ZippyERP\ERP\Entity\Entry;

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
        return "";
    }

    public function Execute()
    {

        $mf = MoneyFund::getFirst('ftype=0');
        MoneyFund::AddActivity($mf->id, $this->headerdata['amount'], $this->document_id);
        $ret = "";
        $optype = $this->headerdata['optype'];
        if ($optype == self::TYPEOP_CUSTOMER) {
            $ret = Entry::AddEntry(30, 36, $this->headerdata['amount'], $this->document_id, 'Наличный расчет');
        }
        if ($optype == self::TYPEOP_CASH) {
            
        }
        if ($optype == self::TYPEOP_BANK) {
            $ret = Entry::AddEntry(30, 31, $this->headerdata['amount'], $this->document_id, 'Оприходование с  банка');
        }
        if ($optype == self::TYPEOP_RET) {
            $ret = Entry::AddEntry(30, 702, $this->headerdata['amount'], $this->document_id, 'Оприходование выручки');
        }
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
