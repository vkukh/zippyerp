<?php

namespace ZippyERP\System;

/**
 * Класс  содержащи  методы  работы   с  наиболее  важными  
 * системмными  данными
 */
class System
{

    /**
     * Возвращает  текущего  юзера
     * @return  User
     */
    public static function getUser()
    {
        $user = Session::getSession()->user;
        if ($user == null) {
            $user = new User();
            self::setUser($user);
        }
        return $user;
    }

    /**
     * Устанавливавет  текущего  юзера  в  системме
     * 
     * @param User $user
     */
    public static function setUser(User $user)
    {
        Session::getSession()->user = $user;
    }

    /**
     * Возвращает  сессию
     * @return  Session
     */
    public static function getSession()
    {

        return Session::getSession();
    }

    /**
     * Возвращает  имя  текущей  темы (шаблона)
     * @return  string
     */
    public static function getTheme()
    {
        if (Session::getSession()->theme == null) {
            $options = System::getOptions();
            if (strlen($options['theme']) > 0) {
                Session::getSession()->theme = $options['theme'];
            } else {
                Session::getSession()->theme = $GLOBALS['_config']['common']['theme'];
            }
        }
        Session::getSession()->theme = $GLOBALS['_config']['common']['theme'];

        return Session::getSession()->theme;
    }

    /**
     * устанавливает  текущую  тему
     *  
     * @param mixed $theme
     */
    public static function setTheme($theme)
    {
        Session::getSession()->theme = $theme;
    }

    /**
     * Возвращает текущий  язык
     * @return  string
     */
    public static function getLang()
    {
        if (Session::getSession()->lang == null) {
            Session::getSession()->lang = $GLOBALS['_config']['common']['lang'];
        }
        return Session::getSession()->lang;
    }

    public static function getOptions($module = "system",$option ='')
    {
        $options = array();
        $conn = \ZCL\DB\DB::getConnect();

        $rs = $conn->Execute("select optname,optvalue from system_options where module='{$module}' ");
        foreach ($rs as $row) {
            $options[$row['optname']] = $row['optvalue'];
        }
        if(strlen($option) ==0) return $options;
        else  $options[$option];
    }

    public static function setOptions($options, $module = "system")
    {

        $conn = \ZCL\DB\DB::getConnect();

        foreach ($options as $key => $option) {
            $conn->Execute(" delete from system_options where module='{$module}' and optname='{$key}' ");
            $conn->Execute(" insert into system_options (module,optname,optvalue) values ('{$module}','{$key}'," . $conn->qstr($option) . " ) ");
        }
    }

}
