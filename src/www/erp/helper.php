<?php

namespace ZippyERP\ERP;

use \ZCL\DB\DB;
use ZippyERP\System\System;

/**
 * Класс   со  вспомагательными   функциями
 *   для  работы с  БД
 */
class Helper
{

    private static $meta = array(); //кеширует метаданные

    /**
     * Генерация  иеню  для  типа  метаданных
     *
     * @param mixed $meta_type
     */

    public static function generateMenu($meta_type)
    {
        $conn = \ZCL\DB\DB::getConnect();
        $rows = $conn->Execute("select *  from erp_metadata where meta_type= {$meta_type} and disabled <> 1 order  by  description ");
        $menu = array();
        $groups = array();

        foreach ($rows as $meta_id => $meta_object) {
            if (strlen($meta_object['menugroup']) == 0) {
                $menu[$meta_id] = $meta_object;
            } else {
                if (!isset($groups[$meta_object['menugroup']])) {
                    $groups[$meta_object['menugroup']] = array();
                }
                $groups[$meta_object['menugroup']][$meta_id] = $meta_object;
            }
        }
        switch ($meta_type) {
            case 1 : $dir = "Pages/Doc";
                break;
            case 2 : $dir = "Pages/Report";
                break;
            case 3 : $dir = "Pages/Register";
                break;
            case 4 : $dir = "Pages/Reference";
                break;
            case 5 : $dir = "Pages/CustomPage";
                break;
        }
        $textmenu = "";
        foreach ($menu as $item) {
            $textmenu .= "<li><a href=\"/?p=ZippyERP/ERP/{$dir}/{$item['meta_name']}\">{$item['description']}</a></li>";
        }
        foreach ($groups as $gname => $group) {
            $textmenu .= "<li class=\"dropdown-submenu\"><a tabindex=\"-1\" href=\"#\">$gname</a><ul class=\"dropdown-menu\">";

            foreach ($group as $item) {
                $textmenu .= "<li><a href=\"/?p=ZippyERP/ERP/{$dir}/{$item['meta_name']}\">{$item['description']}</a></li>";
            }
            $textmenu .= "</ul></li>";
        }

        return $textmenu;
    }

    /**
     * список  групп документов
     *
     */
    public static function getDocGroups()
    {
        $conn = \ZCL\DB\DB::getConnect();
        $groups = array();

        $rs = $conn->Execute('SELECT distinct menugroup FROM  erp_metadata where meta_type =' . 1);
        foreach ($rs as $row) {
            if (strlen($row['menugroup']) > 0) {
                $groups[$row['menugroup']] = $row['menugroup'];
            }
        }
        return $groups;
    }

    /**
     * список единиц измерения
     *
     */
    public static function getMeasureList()
    {
        $list = array();
        $conn = DB::getConnect();
        $sql = "select measure_id,measure_name from  erp_item_measures ";
        $rs = $conn->Execute($sql);
        foreach ($rs as $row) {
            $list[$row["measure_id"]] = $row["measure_name"];
        }

        return $list;
    }

    /**
     * список  групп товаров
     *
     */
    public static function getItemGroupList()
    {
        $list = array();
        $conn = DB::getConnect();
        $sql = "select group_id,group_name from  erp_item_group ";
        $rs = $conn->Execute($sql);
        foreach ($rs as $row) {
            $list[$row["group_id"]] = $row["group_name"];
        }

        return $list;
    }

    /**
     * возварщает запись  метаданных
     *
     * @param mixed $id
     */
    public static function getMetaType($id)
    {
        if (is_array(self::$meta[$id]) == false) {
            $conn = DB::getConnect();
            $sql = "select * from  erp_metadata where meta_id = " . $id;
            self::$meta[$id] = $conn->GetRow($sql);
        }

        return self::$meta[$id];
    }

    /**
     * возвращает описание  мета-обьекта
     *
     * @param mixed $metaname
     */
    public static function getMetaNotes($metaname)
    {
        $conn = DB::getConnect();
        $sql = "select notes from  erp_metadata where meta_name = '{$metaname}' ";
        return $conn->GetOne($sql);
    }

    /**
     * Возвращает  ссписок  денежных счетов
     *
     * @param mixed $bank  true  если  банк иначе  касса
     */
    public static function getMoneyFundsList($bank)
    {
        $list = array();
        $conn = DB::getConnect();
        $sql = "select id, title from  erp_moneyfunds ";
        if ($bank)
            $sql .= " where bank > 0";
        else
            $sql .= " where coalesce(bank,0) == 0";
        $rs = $conn->Execute($sql);
        foreach ($rs as $row) {
            $list[$row["id"]] = $row["title"];
        }

        return $list;
    }

    /**
     * Запись  файла   в БД
     *
     * @param mixed $file
     * @param mixed $itemid   ID  объекта
     * @param mixed $itemtype  тип  объекта (документ - 0 )
     */
    public static function addFile($file, $itemid, $comment, $itemtype = 0)
    {
        $conn = DB::getConnect();
        $filename = $file['name'];

        $comment = $conn->qstr($comment);
        $filename = $conn->qstr($filename);
        $sql = "insert  into erp_files (item_id,filename,description,item_type) values ({$itemid},{$filename},{$comment},{$itemtype}) ";
        $conn->Execute($sql);
        $id = $conn->Insert_ID();

        $data = file_get_contents($file['tmp_name']);
        $data = $conn->qstr($data);
        $sql = "insert  into erp_filesdata (file_id,filedata) values ({$id},{$data}) ";
        $conn->Execute($sql);
    }

    /**
     * список  файдов  пррепленных  к  объекту
     *
     * @param mixed $item_id
     * @param mixed $item_type
     */
    public static function getFileList($item_id, $item_type = 0)
    {
        $conn = \ZCL\DB\DB::getConnect();
        $rs = $conn->Execute("select * from erp_files where item_id={$item_id} and item_type={$item_type} ");
        $list = array();
        foreach ($rs as $row) {
            $item = new \ZippyERP\ERP\DataItem();
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
    public static function deleteFile($file_id)
    {
        $conn = \ZCL\DB\DB::getConnect();
        $conn->Execute("delete  from  erp_files  where  file_id={$file_id}");
        $conn->Execute("delete  from  erp_filesdata  where  file_id={$file_id}");
    }

    /**
     * Возвращает  файл  и  его  содержимое
     *
     * @param mixed $file_id
     */
    public static function loadFile($file_id)
    {
        $conn = \ZCL\DB\DB::getConnect();
        $rs = $conn->Execute("select filename,filedata from erp_files join erp_filesdata on erp_files.file_id = erp_filesdata.file_id  where erp_files.file_id={$file_id}  ");
        foreach ($rs as $row) {
            return $row;
        }

        return null;
    }

    /**
     * Форматирует  вывод  денежной суммы
     *
     * @param mixed $value     сумма   в   копейках
     */
    public static function fm($value)
    {
        return number_format($value / 100, 2, '.', '');
    }

    /**
     * Форматирует  вывод  денежной суммы   в  тысячах
     *
     * @param mixed $value     сумма   в   копейках
     */
    public static function fm_t1($value)
    {
        if(abs($value) < 1000) $value =0;
        return number_format($value / 100000, 1, '.', '');
    }

    /**
     * Возвращает  НДС
     *
     * @param mixed $revert   возвращает  обратную  величину (наприме  если   20% (0.2)  возвращает 16.67% (0.1667) )
     */
    public static function nds($revert = false)
    {
        $common = System::getOptions("common");
        //
        $nds = $common['nds'] / 100;
        if ($revert) {
            $nds = 1 - 100 / (100 + $common['nds']);
        }
        return $nds;
    }

    /**
     * Вставляет пробелы  между символами строки
     *
     * @param mixed $data
     */
    public static function addSpaces($string)
    {
        $_data = "";
        $strlen = mb_strlen($string);
        while ($strlen) {
            $_data .= (" " . mb_substr($string, 0, 1, 'UTF-8'));  ;
            $string = mb_substr($string, 1, $strlen, 'UTF-8');
            $strlen = mb_strlen($string, 'UTF-8');
        }

        return trim($_data);
    }

    /**
    * вовращает  список  лет
    */
    public  static function  getYears(){
      $list = array();
      for($i=2016;$i<=2030;$i++) $list[$i]= $i;
      return  $list;
    }
   /**
    * вовращает  список  месяцев
    */
    public  static function  getMonth(){
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
      return  $list;
    }

}
