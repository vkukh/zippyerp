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
    //Типы  налогов  и сборов
    const TAX_NDS = 101;

    public static function getTaxesList()
    {
        $list = array();
        $list[TAX_NDS] = "НДС";


        return $list;
    }

}
