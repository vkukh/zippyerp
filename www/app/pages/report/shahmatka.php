<?php

namespace App\Pages\Report;

use Zippy\Html\Form\Date;
use Zippy\Html\Form\Form;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Link\RedirectLink;
use Zippy\Html\Panel;
use App\Entity\Account;
use App\Helper as H;
use App\Application as App;

/**
 * Шахматная ведомость
 */
class Shahmatka extends \App\Pages\Base
{

    public function __construct() {
        parent::__construct();

        $this->add(new Form('filter'))->onSubmit($this, 'OnSubmit');
        $this->filter->add(new Date('from', time() - (7 * 24 * 3600)));
        $this->filter->add(new Date('to', time()));
        $this->add(new ClickLink('autoclick'))->onClick($this, 'OnAutoLoad', true);

        $this->add(new Panel('detail'))->setVisible(false);
        $this->detail->add(new RedirectLink('print', ""));
        $this->detail->add(new RedirectLink('html', ""));
        $this->detail->add(new RedirectLink('excel', ""));
        $this->detail->add(new Label('preview'));
        \App\Session::getSession()->issubmit = false;
    }

    public function OnSubmit($sender) {

        if (\App\ACL::checkShowReport('Shahmatka') == false) {
            $this->setWarn('Недостаточно  прав  для просмотра');
            return;
        }


        //$html = $this->generateReport();
        $reportpage = "App/Pages/ShowReport";
        $reportname = "shaxmatkareport";


        \App\Session::getSession()->printform = "";
        \App\Session::getSession()->issubmit = true;


        $this->detail->print->pagename = $reportpage;
        $this->detail->print->params = array('print', $reportname);
        $this->detail->html->pagename = $reportpage;
        $this->detail->html->params = array('html', $reportname);

        $this->detail->excel->pagename = $reportpage;
        $this->detail->excel->params = array('xls', $reportname);

        $this->detail->setVisible(true);
        $this->detail->preview->setText("Загрузка...");
    }

    private function generateReport() {

        $acclist = Account::find("", "acc_code ");

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
            //  $right[] = H::famt($data['obdt']);
            $bottom[] = array('cell' => H::famt($data['obct']), 'bold' => true);
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

                $arr[] = array('cell' => H::famt(Account::getObBetweenAccount($acc->acc_code, $acc2->acc_code, $from, $to)));
            }
            $arr[] = array('cell' => H::famt($data['obdt']), 'bold' => true);

            $detail[] = array('row' => $arr);
        }
        $detail[] = array('row' => $bottom);

        $header = array(
            "_detail" => $detail,
            'from' => date('d.m.Y', $from),
            'to' => date('d.m.Y', $to),
            'size' => count($top) - 1
        );
        //  $detail = array();
        //  $detail[] =  array('row'=>array(array('cell'=>'fff'),array('cell'=>'ffddddf')));
        //   $detail[] =  array('row'=>array(array('cell'=>'fddssdff'),array('cell'=>'ffddddf')));


        $report = new \App\Report('shahmatka.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function OnAutoLoad($sender) {

        if (\App\Session::getSession()->issubmit === true) {
            $html = $this->generateReport();
            \App\Session::getSession()->printform = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head><body>" . $html . "</body></html>";
            $this->detail->preview->setText($html, true);
            $this->updateAjax(array('preview'));
        }
    }

    protected function beforeRender() {
        parent::beforeRender();

        App::addJavaScript("\$('#autoclick').click()", true);
    }

}
