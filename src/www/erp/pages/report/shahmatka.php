<?php

namespace ZippyERP\ERP\Pages\Report;

use Zippy\Html\Form\Date;
use Zippy\Html\Form\Form;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Link\RedirectLink;
use Zippy\Html\Panel;
use ZippyERP\ERP\Entity\Account;
use ZippyERP\ERP\Helper as H;
use Zippy\WebApplication as App;

/**
 * Шахматная ведомость
 */
class Shahmatka extends \ZippyERP\ERP\Pages\Base
{

    private $_updatejs = false;

    public function __construct() {
        parent::__construct();

        $this->add(new Form('filter'))->onSubmit($this, 'OnSubmit');
        $this->filter->add(new Date('from', time() - (7 * 24 * 3600)));
        $this->filter->add(new Date('to', time()));

        $this->add(new Panel('detail'))->setVisible(false);
        $this->detail->add(new RedirectLink('print', ""));
        $this->detail->add(new RedirectLink('html', ""));
        $this->detail->add(new RedirectLink('excel', ""));
        $this->detail->add(new Label('preview'));
        $this->detail->add(new ClickLink('loader'))->onClick($this, "onReport", true);
    }

    public function OnSubmit($sender) {

        //$html = $this->generateReport();
        $reportpage = "ZippyERP/ERP/Pages/ShowReport";
        $reportname = "shaxmatkareport";


        \ZippyERP\System\Session::getSession()->printform = "";

        //\ZippyERP\System\Session::getSession()->printform = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head><body>" . $html . "</body></html>";


        $this->detail->print->pagename = $reportpage;
        $this->detail->print->params = array('print', $reportname);
        $this->detail->html->pagename = $reportpage;
        $this->detail->html->params = array('html', $reportname);

        $this->detail->excel->pagename = $reportpage;
        $this->detail->excel->params = array('xls', $reportname);

        $this->detail->setVisible(true);

        $this->_updatejs = true;
    }

    private function generateReport() {

        $acclist = Account::find("acc_code>=10 and acc_code<1000", "cast(acc_code as char)");

        $detail = array();
        //$left = array();
        $top = array(array('cell' => ''));
        //$right = array();
        $bottom = array(array('cell' => 'Кредит', 'bold' => true));


        $from = strtotime($this->filter->from->getValue());
        $to = strtotime($this->filter->to->getValue());

        foreach ($acclist as $acc) {

            $data = $acc->getSaldoAndOb($from, $to);  //получаем остатки  и  обороты  на  период
            //  $left[] = $acc->acc_code;
            $top[] = array('cell' => $acc->acc_code, 'right' => true, 'bold' => true);
            //  $right[] = H::fm($data['obdt']);
            $bottom[] = array('cell' => H::fm($data['obct']), 'bold' => true);
        }
        $top[] = array('cell' => 'Дебет', 'bold' => true);
        $bottom[] = array('cell' => '');
        ;


        $detail[] = array('row' => $top);
        foreach ($acclist as $acc) {
            $arr = array();
            $data = $acc->getSaldoAndOb($from, $to);  //получаем остатки  и  обороты  на  период
            $arr[] = array('cell' => $acc->acc_code, 'right' => true, 'bold' => true);

            foreach ($acclist as $acc2) {

                $arr[] = array('cell' => H::fm(Account::getObBetweenAccount($acc->acc_code, $acc2->acc_code, $from, $to)));
            }
            $arr[] = array('cell' => H::fm($data['obdt']), 'bold' => true);

            $detail[] = array('row' => $arr);
        }
        $detail[] = array('row' => $bottom);

        $header = array(
            'from' => date('d.m.Y', $from),
            'to' => date('d.m.Y', $to),
            'size' => count($top) - 1
        );
        //  $detail = array();
        //  $detail[] =  array('row'=>array(array('cell'=>'fff'),array('cell'=>'ffddddf')));
        //   $detail[] =  array('row'=>array(array('cell'=>'fddssdff'),array('cell'=>'ffddddf')));


        $report = new \ZippyERP\ERP\Report('shahmatka.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function onReport($sender) {
        $html = $this->generateReport();

        $this->detail->preview->setText($html, true);
        \ZippyERP\System\Session::getSession()->printform = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head><body>" . $html . "</body></html>";


        $this->updateAjax(array(), "$('#preview').attr('src','/?p=ZippyERP/ERP/Pages/ShowReport&arg=preview/shaxmatkareport')");
    }

    protected function beforeRender() {
        parent::beforeRender();

        if ($this->_updatejs) {
            App::$app->getResponse()->addJavaScript("$(\"#loader\").click();", true);
            $this->_updatejs = false;
        }
    }

}
