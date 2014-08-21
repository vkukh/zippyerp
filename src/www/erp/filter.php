<?php

namespace ZippyERP\ERP;

/**
 *   Класс для  хранения   в сессии  параметров
 * отбора  для форм-фильтров 
 */
class Filter
{

    private $data = array();

    public final function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public final function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * Возвращает  фильтр  из  сесии  по имени  фильтра
     * 
     * @param mixed $name
     * @return Filter
     */
    public static function getFilter($name)
    {
        $filter = \ZippyERP\System\System::getSession()->filter[$name];

        if (!isset($filter)) {
            $filter = new Filter();
            \ZippyERP\System\System::getSession()->filter[$name] = $filter;
        }

        return $filter;
    }

}
