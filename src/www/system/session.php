<?php

namespace ZippyERP\System;

/**
 * Класс  для  хранения  в сессии  пользовательских  данных
 *
 */
class Session
{

    private $values = array();
    public $filter = array();
    public $printform;

    public function __construct()
    {
        
    }

    public function __set($name, $value)
    {
        $this->values[$name] = $value;
    }

    public function __get($name)
    {
        return @$this->values[$name];
    }

    /**
     * Возвращает  инстанс  сессии
     * @return Session
     */
    public static function getSession()
    {
        if ($_SESSION['ZippyERP_session'] instanceof Session) {
            
        } else {
          $_SESSION['ZippyERP_session'] = new Session();    
        }
        
        return $_SESSION['ZippyERP_session'];
    }

}
