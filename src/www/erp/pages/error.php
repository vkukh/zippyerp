<?php

namespace ZippyERP\ERP\Pages;

class Error extends Base
{

        public function __construct($error = '')
        {
                parent::__construct();

                $this->add(new \Zippy\Html\Label('msg', $error));
        }

}

