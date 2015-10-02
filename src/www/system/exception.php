<?php

namespace ZippyERP\System;

/**
 * Класс  исключения для  ERP
 */
class Exception extends \Exception
{

    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }

}
