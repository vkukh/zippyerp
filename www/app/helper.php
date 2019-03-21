<?php

namespace App;

use \App\Entity\User;
use \App\System;
use \ZCL\DB\DB as DB;

/**
 * Вспомагательный  класс  для  работы  с  бизнес-данными
 */
class Helper
{

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
    const TYPEOP_CUSTOMER_IN_BACK = 308;   // Возврат  поставщику
    const TYPEOP_CUSTOMER_OUT_BACK = 309;   // Возврат от  покупателя
    const TYPEOP_CASH_SALARY = 310;   // Выплата зарплаты
    const TYPEOP_CUSTOMER_IN_PREV = 311;   // предоплата от покупателя
    const TYPEOP_CUSTOMER_OUT_PREV = 312;   // предоплата поставщику
    const TYPEOP_COMMON_EXPENCES = 313;   // различные накладные  расходы
    const TYPEOP_CUSTOMER_IN_ADVANCE = 314;   // различные накладные  расходы

    private static $meta = array(); //кеширует метаданные

    /**
     * Выполняет  логин  в  системму
     *
     * @param mixed $login
     * @param mixed $password
     * @return  boolean
     */

    public static function login($login, $password = null) {

        $user = User::findOne("  userlogin=  " . User::qstr($login));

        if ($user == null)
            return false;
            
        if ($user->active != 1)
            return false;
            
            
        if ($user->userpass == $password)
            return $user;
        if (strlen($password) > 0) {
            $b = password_verify($password, $user->userpass);
            return $b ? $user : false;
        }
        return false;
    }

    /**
     * Проверка  существования логина
     *
     * @param mixed $login
     */
    public static function existsLogin($login) {
        $list = User::find("  userlogin= " . User::qstr($login));

        return count($list) > 0;
    }

    public static function generateMenu($meta_type) {
        $conn = \ZDB\DB::getConnect();
        $rows = $conn->Execute("select *  from metadata where meta_type= {$meta_type} and disabled <> 1 order  by  description ");
        $menu = array();
        $groups = array();
        $textmenu = "";
        $aclview = explode(',', System::getUser()->aclview);
        foreach ($rows as $meta_object) {
            $meta_id = $meta_object['meta_id'];

            if (!in_array($meta_id, $aclview) && System::getUser()->acltype == 2)
                continue;

            if (strlen($meta_object['menugroup']) == 0) {
                $menu[$meta_id] = $meta_object;
            } else {
                if (!isset($groups[$meta_object['menugroup']])) {
                    $groups[$meta_object['menugroup']] = array();
                }
                $groups[$meta_object['menugroup']][$meta_id] = $meta_object;
            }
            if ($meta_object->smart == 1) {
                
            }
        }
        switch ($meta_type) {
            case 1 :
                $dir = "Pages/Doc";
                break;
            case 2 :
                $dir = "Pages/Report";
                break;
            case 3 :
                $dir = "Pages/Register";
                break;
            case 4 :
                $dir = "Pages/Reference";
                break;
            case 5 :
                $dir = "Shop/Pages";
                break;
        }
        $textmenu = "";

        foreach ($menu as $item) {
            $textmenu .= "<li><a class=\"dropdown-item\" href=\"/?p=App/{$dir}/{$item['meta_name']}\">{$item['description']}</a></li>";
        }
        foreach ($groups as $gname => $group) {
            $textmenu .= "<li  ><a class=\"dropdown-item  dropdown-toggle\"     href=\"#\">$gname 
             
            </a>
            <ul class=\"dropdown-menu\">";

            foreach ($group as $item) {
                $textmenu .= "<li ><a class=\"dropdown-item\"   href=\"/?p=App/{$dir}/{$item['meta_name']}\">{$item['description']}</a></li>";
            }
            $textmenu .= "</ul></li>";
        }

        return $textmenu;
    }

    public static function generateSmartMenu() {
        $conn = \ZDB\DB::getConnect();

        $rows = $conn->Execute("select *  from  metadata where smartmenu =1 ");
        $textmenu = "";
        $aclview = explode(',', System::getUser()->aclview);

        foreach ($rows as $item) {

            if (!in_array($item['meta_id'], $aclview) && System::getUser()->acltype == 2)
                continue;


            switch ((int) $item['meta_type']) {
                case 1 :
                    $dir = "Pages/Doc";
                    break;
                case 2 :
                    $dir = "Pages/Report";
                    break;
                case 3 :
                    $dir = "Pages/Register";
                    break;
                case 4 :
                    $dir = "Pages/Reference";
                    break;
                case 5 :
                    $dir = "Shop/Pages";
                    break;
            }

            $textmenu .= " <a class=\"btn btn-sm btn-outline-primary mr-2\" href=\"/?p=App/{$dir}/{$item['meta_name']}\">{$item['description']}</a> ";
        }

        return $textmenu;
    }

    public static function loadEmail($template, $keys = array()) {
        global $logger;

        $templatepath = _ROOT . 'templates/email/' . $template . '.tpl';
        if (file_exists(strtolower($templatepath)) == false) {

            $logger->error($templatepath . " is wrong");
            return "";
        }
        $template = @file_get_contents(strtolower($templatepath));

        $m = new \Mustache_Engine();
        $template = $m->render($template, $keys);


        return $template;
    }

    /**
     * возвращает описание  мета-обьекта
     *
     * @param mixed $metaname
     */
    public static function getMetaNotes($metaname) {
        $conn = DB::getConnect();
        $sql = "select notes from  metadata where meta_name = '{$metaname}' ";
        return $conn->GetOne($sql);
    }

    public static function sendLetter($template, $email, $subject = "") {


        $_config = parse_ini_file(_ROOT . 'config/config.ini', true);


        $mail = new \PHPMailer();
        $mail->setFrom($_config['common']['emailfrom'], 'Биржа jobber');
        $mail->addAddress($email);
        $mail->Subject = $subject;
        $mail->msgHTML($template);
        $mail->CharSet = "UTF-8";
        $mail->IsHTML(true);


        $mail->send();
        /*

          $from_name = '=?utf-8?B?' . base64_encode("Биржа jobber") . '?=';
          $subject = '=?utf-8?B?' . base64_encode($subject) . '?=';
          mail(
          $email,
          $subject,
          $template,
          "From: " . $from_name." <{$_config['common']['emailfrom']}>\r\n".
          "Content-type: text/html; charset=\"utf-8\""
          );
         */
    }

    /**
     * Запись  файла   в БД
     *
     * @param mixed $file
     * @param mixed $itemid ID  объекта
     * @param mixed $itemtype тип  объекта (документ - 0 )
     */
    public static function addFile($file, $itemid, $comment, $itemtype = 0) {
        $conn = DB::getConnect();
        $filename = $file['name'];

        $comment = $conn->qstr($comment);
        $filename = $conn->qstr($filename);
        $sql = "insert  into files (item_id,filename,description,item_type) values ({$itemid},{$filename},{$comment},{$itemtype}) ";
        $conn->Execute($sql);
        $id = $conn->Insert_ID();

        $data = file_get_contents($file['tmp_name']);
        $data = $conn->qstr($data);
        $sql = "insert  into filesdata (file_id,filedata) values ({$id},{$data}) ";
        $conn->Execute($sql);
    }

    /**
     * список  файдов  пррепленных  к  объекту
     *
     * @param mixed $item_id
     * @param mixed $item_type
     */
    public static function getFileList($item_id, $item_type = 0) {
        $conn = \ZDB\DB::getConnect();
        $rs = $conn->Execute("select * from files where item_id={$item_id} and item_type={$item_type} ");
        $list = array();
        foreach ($rs as $row) {
            $item = new \App\DataItem();
            $item->file_id = $row['file_id'];
            $item->filename = $row['filename'];
            $item->description = $row['description'];


            $list[] = $item;
        }

        return $list;
    }

    /**
     * удаление  файла
     *
     * @param mixed $file_id
     */
    public static function deleteFile($file_id) {
        $conn = \ZDB\DB::getConnect();
        $conn->Execute("delete  from  files  where  file_id={$file_id}");
        $conn->Execute("delete  from  filesdata  where  file_id={$file_id}");
    }

    /**
     * Возвращает  файл  и  его  содержимое
     *
     * @param mixed $file_id
     */
    public static function loadFile($file_id) {
        $conn = \ZDB\DB::getConnect();
        $rs = $conn->Execute("select filename,filedata from files join filesdata on files.file_id = filesdata.file_id  where files.file_id={$file_id}  ");
        foreach ($rs as $row) {
            return $row;
        }

        return null;
    }

    /**
     * возварщает список  документов
     *
     * @param mixed $id
     */
    public static function getDocTypes() {
        $conn = \ZDB\DB::getConnect();
        $groups = array();

        $rs = $conn->Execute('SELECT description,meta_id FROM   metadata where meta_type = 1 order by description');
        foreach ($rs as $row) {
            $groups[$row['meta_id']] = $row['description'];
        }
        return $groups;
    }

    /**
     * возварщает запись  метаданных
     *
     * @param mixed $id
     */
    public static function getMetaType($id) {
        if (is_array(self::$meta[$id]) == false) {
            $conn = DB::getConnect();
            $sql = "select * from   metadata where meta_id = " . $id;
            self::$meta[$id] = $conn->GetRow($sql);
        }

        return self::$meta[$id];
    }

    /**
     * логгирование
     * 
     * @param mixed $msg
     */
    public static function log($msg) {
        global $logger;
        $logger->debug($msg);
    }

    /**
     * Возвращает склад  по  умолчанию
     * 
     */
    public static function getDefStore() {
        $common = System::getOptions("common");
        if ($common['defstore'] > 0) {
            return $common['defstore'];
        }

        \App\System::setErrorMsg('Не настроен склад  по  умолчанию');
        \App\Application::RedirectHome();
    }

    //форматирование для количеств
    public static function fqty($qty) {
        if (strlen($qty) == 0)
            return '';
        $digit = 0;
        $common = System::getOptions("common");
        if ($common['qtydigits'] > 0) {
            $digit = $common['qtydigits'];
        }
        if ($digit == 0) {
            return round($qty);
        } else {
            return number_format($qty, $digit, '.', '');
        }
    }

    //форматирование для сумм 
    public static function famt($amount) {
        if (strlen($amount) == 0)
            return '';
        $digit = 0;
        $common = System::getOptions("common");
        if ($common['amdigits'] > 0) {
            $digit = $common['amdigits'];
        }
        if ($digit == 0) {
            return round($amount);
        } else {
            return number_format($amount, $digit, '.', '');
        }
    }

    /**
     * Форматирует  вывод  денежной суммы   в  тысячах
     *
     * @param mixed $value сумма   в   копейках
     */
    public static function fm_t1($value) {
        if (abs($value) < 1000)
            $value = 0;
        return number_format($value / 1000, 1, '.', '');
    }

    /**
     * Возвращает  НДС
     *
     * @param mixed $revert возвращает  обратную  величину (наприме  если   20% (0.2)  возвращает 16.67% (0.1667) )
     */
    public static function nds($revert = false) {
        $tax = System::getOptions("tax");
        //
        $nds = $tax['nds'] / 100;
        if ($revert) {
            $nds = 1 - 100 / (100 + $tax['nds']);
        }
        return $nds;
    }

    /**
     * проверяем настройки используется ли  НДС
     * 
     */
    public static function usends() {
        $common = System::getOptions("common");

        return $common['hasnds'] > 0;
    }

    //список  единиц измерения
    public static function getMeasures() {
        $conn = \ZDB\DB::getConnect();
        $msrlist = array();

        $rs = $conn->Execute('SELECT * FROM `item_measures` ');
        foreach ($rs as $row) {
            $msrlist[$row['measure_id']] = $row['measure_name'];
        }
        return $msrlist;
    }

    /**
     * Список  всех  кодов
     *
     */
    public static function getCodesList() {
        $list = array();
        $list[self::TAX_NDS] = "НДС";
        $list[self::TAX_INCOME] = "Налог на прибыль";
        $list[self::TAX_ONE] = "Єдиний податок";
        $list[self::TAX_NDFL] = "НДФЛ";
        $list[self::TAX_MIL] = "Военный сбор";
        $list[self::TYPEOP_CUSTOMER_IN] = "Оплата покупателя";
        $list[self::TYPEOP_BANK_IN] = "Снятие  со счета";
        $list[self::TYPEOP_CASH_IN] = "Приход с  полотчета";
        $list[self::TYPEOP_RET_IN] = "Доход с розницы";
        $list[self::TYPEOP_CUSTOMER_OUT] = "Оплата поставщику";
        $list[self::TYPEOP_BANK_OUT] = "Пополнение счета";
        $list[self::TYPEOP_CASH_OUT] = "Выдано на подотчет";

        return $list;
    }

    /**
     * вовращает  список  месяцев
     */
    public static function getMonth() {
        $list = array();
        $list[1] = "Январь";
        $list[2] = "Февраль";
        $list[3] = "Март";
        $list[4] = "Апрель";
        $list[5] = "Май";
        $list[6] = "Июнь";
        $list[7] = "Июль";
        $list[8] = "Август";
        $list[9] = "Сентябрь";
        $list[10] = "Октябрь";
        $list[11] = "Ноябрь";
        $list[12] = "Декабрь";
        return $list;
    }

    /**
     * Список  типов  налогов
     *
     */
    public static function getTaxesList() {
        $list = array();
        $list[self::TAX_NDS] = "НДС";
        $list[self::TAX_INCOME] = "Налог на прибыдь";
        $list[self::TAX_ONE] = "Единий налог";
        $list[self::TAX_NDFL] = "ПДФЛ";
        $list[self::TAX_MIL] = "Вренный сбор";
        $list[self::TAX_ECB] = "ЕCB";

        return $list;
    }

    /**
     * вовращает  список  лет
     */
    public static function getYears() {
        $list = array();
        for ($i = 2018; $i <= 2030; $i++)
            $list[$i] = $i;
        return $list;
    }

}
