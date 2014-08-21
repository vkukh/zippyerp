<?php

namespace ZippyERP\ERP;

/**
 * Класс -набор констант 
 */
class Consts
{

    // типы  ТМЦ

    const ITEM_TYPE_GOODS = 1; //товар
    const ITEM_TYPE_MBP = 2;   //МБП
    const ITEM_TYPE_SERVICE = 3; //Услуга
    const ITEM_TYPE_STUFF = 4; //материалы
    const ITEM_TYPE_PRODUCTION = 5; //Готовая продукция
    // типы складов
    const STORE_TYPE_OPT = 1; //Оптовый
    const STORE_TYPE_RETAIL = 2; //Розничный
    const STORE_TYPE_MOL = 3; //МОЛ
    //мета объекты
    const META_LIST = 1; //справочник
    const META_DOCUMENT = 2; //документ
    const META_REGISTER = 3; //журнал
    const META_USERPAGE = 5; //пользовательская страница
    const META_REPORT = 4; //отчет  
    // типы  сообщений
    const MSG_ITEM_TYPE_DOC = 1; //  документ
    const MSG_ITEM_TYPE_PRJ = 2; //  проект
    const MSG_ITEM_TYPE_TASK = 3; //  задание
    // типы  прикрепленных файлов
    const FILE_ITEM_TYPE_DOC = 1; //  документ
    const FILE_ITEM_TYPE_PRJ = 2; //  проект
    const FILE_ITEM_TYPE_TASK = 3; //  задание

}
