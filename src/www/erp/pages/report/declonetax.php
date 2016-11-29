<?php

namespace ZippyERP\ERP\Pages\Report;

use ZippyERP\System\System;

/**
 * Декларация по единому налогу
 */
class Declonetax extends \ZippyERP\System\Pages\Base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function OnSubmit($sender)
    {

    }

    public function generateReport($header)
    {

        $header = array();
        //$detail = array();
        $firm = System::getOptions("firmdetail");
        //$common = System::getOptions("common");

        $header['firm'] = $firm['name'];

        $report = new \ZippyERP\ERP\Report('declonetax.tpl');

        $html = $report->generate($header, array());

        return $html;
    }

}
