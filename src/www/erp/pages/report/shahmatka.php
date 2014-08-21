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

class Shahmatka extends \ZippyERP\ERP\Pages\Base
{

    public function __construct()
    {
        parent::__construct();

        $this->add(new Form('filter'))->setSubmitHandler($this, 'OnSubmit');
        $this->filter->add(new Date('from', time() - (7 * 24 * 3600)));
        $this->filter->add(new Date('to', time()));

        $this->add(new Panel('detail'))->setVisible(false);
        $this->detail->add(new RedirectLink('print', ""));
        $this->detail->add(new RedirectLink('pdf', ""))->setVisible(false);
        $this->detail->add(new RedirectLink('word', ""));
        $this->detail->add(new RedirectLink('excel', ""));
        $this->detail->add(new Label('preview'));
    }

    public function OnSubmit($sender)
    {

        $html = $this->generateReport();
        $this->detail->preview->setText($html, true);

        \ZippyERP\System\Session::getSession()->accountreport = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head><body>" . $html . "</body></html>";
        $reportpage = "ZippyERP/ERP/Pages/ShowDoc";
        $this->detail->print->pagename = $reportpage;
        $this->detail->print->params = array('print', "shaxmatkareport");
        $this->detail->pdf->pagename = $reportpage;
        $this->detail->pdf->params = array('pdf', "shaxmatkareport");
        $this->detail->word->pagename = $reportpage;
        $this->detail->word->params = array('doc', "shaxmatkareport");
        $this->detail->excel->pagename = $reportpage;
        $this->detail->excel->params = array('xls', "shaxmatkareport");

        $this->detail->setVisible(true);
    }

    private function generateReport()
    {

        $acclist = Account::find("", "acc_code");

        $detail = array();
        $left = array();
        $top = array();
        $right = array();
        $bottom = array();


        $from = strtotime($this->filter->from->getValue());
        $to = strtotime($this->filter->to->getValue());

        foreach ($acclist as $acc) {

            $data = $acc->getSaldoAndOb($from, $to);  //получаем остатки  и  обороты  на  период
            $left[] = $acc->acc_code;
            $top[] = $acc->acc_code;
            $right[] = number_format($data['obdt'] / 100, 2);
            $bottom[] = number_format($data['obct'] / 100, 2);
            $arr = array();
            foreach ($acclist as $acc2) {
                $arr[] = number_format(Account::getObBetweenAccount($acc->acc_id, $acc2->acc_id, $from, $to) / 100, 2);
            }
            $detail[] = $arr;
        }

        $header = array(
            'from' => date('d.m.Y', $from),
            'to' => date('d.m.Y', $to)
        );

        $reportgen = new \ZCL\RepGen\RepGen(_ROOT . 'templates/erp/templates/shahmatka.html', $header);

        $html = $reportgen->generatePivot($detail, $left, $top, $right, $bottom);
        return $html;
    }

}
