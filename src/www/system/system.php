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

    /**
     * Возвращает набор  параметром  по  имени набора
     * 
     * @param mixed $group
     */
    public static function getOptions($group)
    {
        $options = array();
        $conn = \ZCL\DB\DB::getConnect();

        $rs = $conn->GetOne("select optvalue from system_options where optname='{$group}' ");
        if (strlen($rs) > 0) {
            $options = @unserialize($rs);
        }

        return $options;
    }

    /**
     * Записывает набор  параметров  по имени набора
     * 
     * @param mixed $group
     * @param mixed $options
     */
    public static function setOptions($group, $options)
    {
        $options = serialize($options);
        $conn = \ZCL\DB\DB::getConnect();

        $conn->Execute(" delete from system_options where  optname='{$group}' ");
        $conn->Execute(" insert into system_options (optname,optvalue) values ('{$group}'," . $conn->qstr($options) . " ) ");
    }

}
