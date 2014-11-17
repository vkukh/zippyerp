<?php

namespace ZippyERP\ERP\Entity\Doc;

/**
 * Класс-сущность  документ  расходный кассовый  ордер
 * 
 */
class CashReceiptOut extends Document
{

    const TYPEOP_CUSTOMER = 1;   // Оплата заказа
    const TYPEOP_BANK = 2;   // Перечислени на счет
    const TYPEOP_CASH = 3;   // В  подотчет

    public function generateReport()
    {
        return "";
    }

    public function Execute()
    {
        $optype = $this->header['optype'];
        if ($optype == self::TYPEOP_CUSTOMER) {
            
        }
        if ($optype == self::TYPEOP_CASH) {
            
        }
        if ($optype == self::TYPEOP_BANK) {
            
        }

        return true;
    }

    // Список  типов операций
    public static function getTypes()
    {
        $list = array();
        $list[self::TYPEOP_CUSTOMER] = "Оплата поставщика";
        $list[self::TYPEOP_BANK] = "Пополнение  счета";
        $list[self::TYPEOP_CASH] = "Расход на подотчета";
        return $list;
    }

}
