<?php

namespace ZippyERP\ERP\Pages\Report;

use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\Date;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Panel;
use \ZippyERP\ERP\Entity\Account;
use \ZippyERP\ERP\Entity\SubConto;
use \Zippy\Html\Link\RedirectLink;
use \ZippyERP\ERP\Helper as H;
use \ZippyERP\System\System;
use \Carbon\Carbon;

/**
 * Декларация по единому налогу
 */
class Declonetax extends \ZippyERP\ERP\Pages\Base
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
        $detail = array();
        $firm = System::getOptions("firmdetail");
        $common = System::getOptions("common");

        $header['firm'] = $firm['name'];

        $report = new \ZippyERP\ERP\Report('declonetax.tpl');

        $html = $report->generate($header, array());

        return $html;
    }

}
