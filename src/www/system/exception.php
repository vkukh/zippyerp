<?php

namespace ZippyERP\System;

/**
 * Класс  исключения для  CMS
 */
class Exception extends \Exception
{

        public function __construct($message, $code = 0)
        {
                parent::__construct($message, $code);
        }

}

