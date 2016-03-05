<?php

namespace ZippyERP\ERP\Pages\Report;

use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\Date;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Panel;
use \Zippy\Html\Form\DropDownChoice;
use \ZippyERP\ERP\Entity\Account;
use \Zippy\Html\Link\RedirectLink;
use \ZippyERP\ERP\Helper as H;

class AccountActivity extends \ZippyERP\ERP\Pages\Base
{

    public function __construct()
    {
        parent::__construct();
        $this->add(new Form('filter'))->setSubmitHandler($this, 'OnSubmit');
        $this->filter->add(new Date('from', time() - (7 * 24 * 3600)));
        $this->filter->add(new Date('to', time()));
        $this->filter->add(new DropDownChoice('acc', \ZippyERP\ERP\Entity\Account::findArrayEx("acc_code not in (select acc_pid  from erp_account_plan)", "acc_code")));

        $this->add(new Panel('detail'))->setVisible(false);
        $this->detail->add(new RedirectLink('print', ""));
        $this->detail->add(new RedirectLink('html', ""));
        $this->detail->add(new RedirectLink('word', ""));
        $this->detail->add(new RedirectLink('excel', ""));
        $this->detail->add(new Label('preview'));
    }

    public function OnSubmit($sender)
    {
        if ($this->filter->acc->getValue() == 0) {
            $this->setError('Не  выбран  счет');
            return;
        }

        $html = $this->generateReport();
        $reportpage = "ZippyERP/ERP/Pages/ShowReport";
        $reportname = "obsaldoreport";

        $this->detail->preview->setAttribute('src', "/?p={$reportpage}&arg=preview/{$reportname}");


        \ZippyERP\System\Session::getSession()->printform = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head><body>" . $html . "</body></html>";


        $this->detail->print->pagename = $reportpage;
        $this->detail->print->params = array('print', $reportname);
        $this->detail->html->pagename = $reportpage;
        $this->detail->html->params = array('html', $reportname);
        $this->detail->word->pagename = $reportpage;
        $this->detail->word->params = array('doc', $reportname);
        $this->detail->excel->pagename = $reportpage;
        $this->detail->excel->params = array('xls', $reportname);

        $this->detail->setVisible(true);
    }

    private function generateReport()
    {
        $acc = Account::load($this->filter->acc->getValue());

        $from = $this->filter->from->getDate();
        $to = $this->filter->to->getDate();


        $header = array(
            'from' => date('d.m.Y', $from),
            'to' => date('d.m.Y', $to),
            'acc' => $acc->acc_name
        );

        $detail = array();


        $conn = \ZCL\DB\DB::getConnect();

        $data = $acc->getSaldoAndOb($from, $to);  //получаем остатки  и  обороты  на  период

        $sql = "select sum(case when acc_d = {$acc->acc_code} then amount else 0 end )  as ad,
            sum(case when acc_c = {$acc->acc_code} then amount else 0 end )  as ac ,document_number,document_date
            from erp_account_entry_view  where ( acc_d ={$acc->acc_code} or acc_c ={$acc->acc_code} ) and document_date >= " . $conn->DBDate($from) . " and document_date <= " . $conn->DBDate($to) . "  group by  document_number,document_date  order  by  document_date            ";

        $rs = $conn->Execute($sql);


        $startdt = $data['startdt'];
        $startct = $data['startct'];

        foreach ($rs as $row) {
            $amountdt = 0;
            $amountct = 0;
            $enddt = 0;
            $endct = 0;
            if ($row['ad'] > 0) {
                $amountdt = $row['ad'];
                $enddt = $startdt + $row['ad'];
                $endct = $startct;
            }
            if ($row['ac'] > 0) {
                $amountct = $row['ac'];
                $endct = $startct + $row['ac'];
                $enddt = $startdt;
            }

            if ($enddt - $endct > 0) {
                $enddt = $enddt - $endct;
                $endct = 0;
            } else {
                $endct = $endct - $enddt;
                $enddt = 0;
            }

            $detail[] = array(
                "date" => date("d.m.Y", strtotime($row['document_date'])),
                "doc" => $row['document_number'],
                "amountdt" => H::fm($amountdt),
                "amountct" => H::fm($amountct),
                "startdt" => H::fm($startdt),
                "startct" => H::fm($startct),
                "enddt" => H::fm($enddt),
                "endct" => H::fm($endct)
            );

            $startdt = $enddt;
            $startct = $endct;
        }


        $report = new \ZippyERP\ERP\Report('accountactivity.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

}
