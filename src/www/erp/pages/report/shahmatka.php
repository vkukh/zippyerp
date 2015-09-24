<?php

namespace ZippyERP\ERP\Pages\Report;

use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\Date;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Panel;
use \ZippyERP\ERP\Entity\Account;
use \Zippy\Html\Link\RedirectLink;
use \ZippyERP\ERP\Helper as H;
use ZippyERP\System\Application as App;

class Shahmatka extends \ZippyERP\ERP\Pages\Base
{

    private $_updatejs = false;

    public function __construct()
    {
        parent::__construct();

        $this->add(new Form('filter'))->setSubmitHandler($this, 'OnSubmit');
        $this->filter->add(new Date('from', time() - (7 * 24 * 3600)));
        $this->filter->add(new Date('to', time()));

        $this->add(new Panel('detail'))->setVisible(false);
        $this->detail->add(new RedirectLink('print', ""));
        $this->detail->add(new RedirectLink('html', ""));
        $this->detail->add(new RedirectLink('excel', ""));
        $this->detail->add(new Label('preview'));
        $this->detail->add(new ClickLink('loader'))->setAjaxClickHandler($this, "onReport");
    }

    public function OnSubmit($sender)
    {

        //$html = $this->generateReport();
        $this->detail->preview->setText("<center style=\"padding-top:100px;padding-bottom:100px;\"><img title=\"Загрузка...\" src=\"/assets/images/ajax-loader.gif\"></center>", true);

        \ZippyERP\System\Session::getSession()->printform = "";

        //\ZippyERP\System\Session::getSession()->printform = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head><body>" . $html . "</body></html>";

        $reportpage = "ZippyERP/ERP/Pages/ShowDoc";
        $this->detail->print->pagename = $reportpage;
        $this->detail->print->params = array('print', "shaxmatkareport");
        $this->detail->html->pagename = $reportpage;
        $this->detail->html->params = array('html', "shaxmatkareport");

        $this->detail->excel->pagename = $reportpage;
        $this->detail->excel->params = array('xls', "shaxmatkareport");

        $this->detail->setVisible(true);

        $this->_updatejs = true;
    }

    private function generateReport()
    {

        $acclist = Account::find("", "cast(acc_code as char)");

        $detail = array();
        $left = array();
        $top = array('');
        $right = array();
        $bottom = array('Кредит');


        $from = strtotime($this->filter->from->getValue());
        $to = strtotime($this->filter->to->getValue());

        foreach ($acclist as $acc) {

            $data = $acc->getSaldoAndOb($from, $to);  //получаем остатки  и  обороты  на  период
            $left[] = $acc->acc_code;
            $top[] = $acc->acc_code;
            $right[] = H::fm($data['obdt']);
            $bottom[] = H::fm($data['obct']);
        }
        $top[] = 'Дебет';
        $bottom[] = '';

        $detail[] = $top;
        foreach ($acclist as $acc) {
            $arr = array();
            $data = $acc->getSaldoAndOb($from, $to);  //получаем остатки  и  обороты  на  период
            $arr[] = $acc->acc_code;

            foreach ($acclist as $acc2) {
                $arr[] = H::fm(Account::getObBetweenAccount($acc->acc_code, $acc2->acc_code, $from, $to));
            }
            $arr[] = H::fm($data['obdt']);
            $detail[] = $arr;
        }
        $detail[] = $bottom;

        $header = array(
            'from' => date('d.m.Y', $from),
            'to' => date('d.m.Y', $to),
            'size' => count($top) - 1
        );

        $report = new \ZippyERP\ERP\Report('shahmatka.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function onReport($sender)
    {
        $html = $this->generateReport();


        //  $html ="<h1>ddd</h1>";

        $this->detail->preview->setText($html, true);
        \ZippyERP\System\Session::getSession()->printform = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head><body>" . $html . "</body></html>";

        $this->updateAjax(array("preview"));
    }

    protected function beforeRender()
    {
        parent::beforeRender();

        if ($this->_updatejs) {
            App::$app->getResponse()->addJavaScript("$(\"#loader\").click();", true);
            $this->_updatejs = false;
        }
    }

}
