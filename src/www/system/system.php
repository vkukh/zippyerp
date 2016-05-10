<?php

namespace ZippyERP\System;

/**
 * Класс  содержащи  методы  работы   с  наиболее  важными  
 * системмными  данными
 */
class System
{
     private  static $_options = array();   //  для кеширования отчета
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
     * Возвращает набор  параметром  по  имени набора
     * 
     * @param mixed $group
     */
    public static function getOptions($group)
    {
        
        if(isset(self::$_options[$group])){
            return self::$_options[$group]; 
        }
        $conn = \ZDB\DB\DB::getConnect();

        $rs = $conn->GetOne("select optvalue from system_options where optname='{$group}' ");
        if (strlen($rs) > 0) {
            self::$_options[$group] = @unserialize($rs);
        }

        return self::$_options[$group];
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
        $conn = \ZDB\DB\DB::getConnect();

        $conn->Execute(" delete from system_options where  optname='{$group}' ");
        $conn->Execute(" insert into system_options (optname,optvalue) values ('{$group}'," . $conn->qstr($options) . " ) ");
        self::$_options[$group] =  $options;
    }

}
