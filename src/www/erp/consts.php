<?php

namespace ZippyERP\ERP;

/**
 * Класс - набор констант не  привязанных к  сущностям
 */
class Consts
{

    //мета объекты
    const META_LIST = 1; //справочник
    const META_DOCUMENT = 2; //документ
    const META_REGISTER = 3; //журнал
    const META_CUSTOMPAGE = 5; //пользовательская страница
    const META_REPORT = 4; //отчет
    // типы  сообщений
    const MSG_ITEM_TYPE_DOC = 1; //  документ
    const MSG_ITEM_TYPE_PRJ = 2; //  проект
    const MSG_ITEM_TYPE_TASK = 3; //  задание
    const MSG_ITEM_TYPE_CONTACT = 4; //  контакт
    // типы  прикрепленных файлов
    const FILE_ITEM_TYPE_DOC = 1; //  документ
    const FILE_ITEM_TYPE_PRJ = 2; //  проект
    const FILE_ITEM_TYPE_TASK = 3; //  задание
    const FILE_ITEM_TYPE_CONTACT = 4; //  контакт
    //Типы  налогов  и сборов   641
    const TAX_NDS = 101;
    const TAX_INCOME = 102;   // подоходный
    const TAX_PROFIT = 103;   // на прибыль
    //const TAX_EXCISE = 104;   // акциз
    const TAX_OTHER = 105;
    const TAX_ONE = 106;  //единый  налог
    const TAX_PENS = 105;
    const TAX_SOCIAL = 106;
    const TAX_BEZRAB = 107;
    const TAX_ECB = 108;  //ФОП
    const TAX_MIL = 109;  //военный сбор
    const TAX_NDFL = 110;   //подоходный ФЛ
    //Типы  начислений и удержаний с зарплаты
    // const SAL_BASE = 201;   //основная зарплата
    // const SAL_INDEX = 202;  //  индексация
    // const SAL_BOLN = 203;  //  больничный
    //  const SAL_OTP = 204;  //  отпускные
    //  const SAL_AVANS = 250;   //аванс
    const TYPEOP_CUSTOMER_IN = 301;   // Оплата от покупателя
    const TYPEOP_BANK_IN = 302;   // Снятие  со  счета
    const TYPEOP_CASH_IN = 303;   // Из  подотчета
    const TYPEOP_RET_IN = 304;   // Из  магазина
    const TYPEOP_CUSTOMER_OUT = 305;   // Оплата поставщику
    const TYPEOP_BANK_OUT = 306;   // Перечисление на счет
    const TYPEOP_CASH_OUT = 307;   // В  подотчет
    const TYPEOP_CUSTOMER_IN_BACK = 308;   // Возврат от  поставщика
    const TYPEOP_CUSTOMER_OUT_BACK = 309;   // Возврат  покупателю
    const TYPEOP_CASH_SALARY = 310;   // Выплата зарплаты
    const TYPEOP_CUSTOMER_IN_PREV = 311;   // предоплата от покупателя
    const TYPEOP_CUSTOMER_OUT_PREV = 312;   // предоплата поставщику
    const TYPEOP_COMMON_EXPENCES = 313;   // различные накладные  расходы

    /**
     * Список  типов  налогов
     *
     */

    public static function getTaxesList()
    {
        $list = array();
        $list[self::TAX_NDS] = "НДС";
        $list[self::TAX_INCOME] = "Налог на прибыль";
        $list[self::TAX_ONE] = "Единый налог";
        $list[self::TAX_NDFL] = "НДФЛ";
        $list[self::TAX_MIL] = "Военный сбор";
        $list[self::TAX_ECB] = "ECB";

        return $list;
    }

    /**
     * Список  типов  затрат
     *
     */
    public static function getExpensesList()
    {


        $list = array();
        $list[23] = "Прямые производственные затраты";
        $list[91] = "Общепроизводственные затраты";
        $list[92] = "Административные затраты";
        $list[93] = "Затраты  на сбыт";

        return $list;
    }

    /**
     * Список  всех  кодов
     *
     */
    public static function getCodesList()
    {
        $list = array();
        $list[self::TAX_NDS] = "НДС";
        $list[self::TAX_INCOME] = "Налог на прибыль";
        $list[self::TAX_ONE] = "Единый налог";
        $list[self::TAX_NDFL] = "НДФЛ";
        $list[self::TAX_MIL] = "Военный сбор";
        $list[self::TYPEOP_CUSTOMER_IN] = "Оплата покупателя";
        $list[self::TYPEOP_BANK_IN] = "Снятие  со  счета";
        $list[self::TYPEOP_CASH_IN] = "Приход  с  подотчета";
        $list[self::TYPEOP_RET_IN] = "Приход с розницы";
        $list[self::TYPEOP_CUSTOMER_OUT] = "Оплата поставщику";
        $list[self::TYPEOP_BANK_OUT] = "Пополнение  счета";
        $list[self::TYPEOP_CASH_OUT] = "Расход на подотчета";

        return $list;
    }

}
