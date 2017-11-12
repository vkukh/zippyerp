<?php

namespace ZippyERP\Shop\Pages;

use \ZippyERP\Shop\Helper;
use \ZippyERP\System\System;

class SalesReport extends Base
{

    public $currentorder = null;
    public $productlist = array();

    public function __construct() {
        parent::__construct();

        $reportform = $this->add(new \Zippy\Html\Form\Form('reportform'));
        $reportform->onSubmit($this, 'OnReport');
        $this->reportform->add(new \Zippy\Html\Form\Date('from'))->setDate(strtotime('-7 day'));
        $this->reportform->add(new \Zippy\Html\Form\Date('to'))->setDate(time());
        $this->reportform->add(new \Zippy\Html\Form\DropDownChoice('group', \ZippyERP\Shop\Entity\ProductGroup::findArray("groupname", "pcnt>0", "groupname")));

        $this->add(new \Zippy\Html\Label('report'))->setVisible(false);
        ;

        $this->add(new \Zippy\Html\Link\RedirectLink('print', '\ZippyERP\Shop\Pages\ShowReport', array('print')))->setVisible(false);
        $this->add(new \Zippy\Html\Link\RedirectLink('xls', '\ZippyERP\Shop\Pages\ShowReport', array('xls')))->setVisible(false);
    }

    public function OnReport($sender) {
        $from = $this->reportform->from->getDate();
        $to = $this->reportform->to->getDate();
        $group = $this->reportform->group->getValue();

        $header = array('datefrom' => date('d.m.Y', $from), 'dateto' => date('d.m.Y', $to));

        $conn = \ZCL\DB\DB::getConnect();
        $where = "  status =2 and closed >= " . $conn->DBDate($from) . " and closed <= " . $conn->DBDate($to) . "   ";
        if ($group > 0)
            $where .= " and group_id = {$group}";
        $sql = "select d.`productname`,d.`price`,d.`partion`,sum(d.`quantity`) as qty                 from  `shop_orders` o join `shop_orderdetails_view` d on o.`order_id` = d.`order_id`   where {$where}   group   by d.`productname`,d.`price` order by  productname                  ";
        $rs = $conn->Execute($sql);

        $total = 0;
        $ptotal = 0;

        $list = array();
        foreach ($rs as $row) {
            $row['profit'] = Helper::fm(($row['price'] - $row['partion']) * $row["qty"]);
            $row['amount'] = Helper::fm($row['price'] * $row["qty"]);
            $row['price'] = Helper::fm($row['price']);

            $list[] = $row;
            $total = $total + $row['amount'];
            $ptotal = $ptotal + $row['profit'];
        }
        $header["list"] = $list;
        $header["total"] = $total;
        $header["ptotal"] = $ptotal;

        $from = $this->reportform->from->getDate();
        $to = $this->reportform->to->getText();

        $template = @file_get_contents(_ROOT . 'templates/shop/reports/sellreport.tpl');

        $m = new \Mustache_Engine();
        $html = $m->render($template, $header);

        $this->print->setVisible(true);
        $this->xls->setVisible(true);
        $this->report->setVisible(true);
        $this->report->setText($html, true);
        $html = "<html><body>{$html}</body></html>";


        \ZippyERP\System\Session::getSession()->sellreport = $html;
    }

}
