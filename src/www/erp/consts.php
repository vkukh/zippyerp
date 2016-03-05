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
    //Типы  налогов  и сборов   65
    const TAX_PENS = 105;
    const TAX_SOCIAL = 106;
    const TAX_BEZRAB = 107;
    const TAX_ECB = 108;  //ФОП
    //Типы  начислений и удержаний с зарплаты
    const SAL_BASE = 201;   //основная зарплата
    const SAL_INDEX = 202;  //  индексация
    const SAL_BOLN = 203;  //  больничный
    const SAL_OTP = 204;  //  отпускные
    const SAL_AVANS = 250;   //аванс
    const SAL_INCOME = 251;   //подоходный ФЛ


    /**
     * Список  типов  налогов
     *
     */

    public static function getTaxesList()
    {
        $list = array();
        $list[TAX_NDS] = "НДС";
        $list[TAX_INCOME] = "Налог на прибыль";
         $list[TAX_ONE] = "Единый налог";
         $list[SAL_INCOME] = "НДФЛ";

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

}
