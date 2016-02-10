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

class ObSaldo extends \ZippyERP\ERP\Pages\Base
{

    public function __construct()
    {
        parent::__construct();

        $this->add(new Form('filter'))->setSubmitHandler($this, 'OnSubmit');
        $this->filter->add(new Date('from', time() - (7 * 24 * 3600)));
        $this->filter->add(new Date('to', time()));

        $this->add(new Panel('detail'))->setVisible(false);
        $this->detail->add(new RedirectLink('print', ""));
        $this->detail->add(new RedirectLink('html', ""));
        $this->detail->add(new RedirectLink('word', ""));
        $this->detail->add(new RedirectLink('excel', ""));
        $this->detail->add(new Label('preview'));
    }

    public function OnSubmit($sender)
    {

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

        $acclist = Account::find("", "cast(acc_code as char)");

        $detail = array();
        $totstartdt = 0;
        $totstartct = 0;
        $totobdt = 0;
        $totobct = 0;
        $totenddt = 0;
        $totendct = 0;

        $from = $this->filter->from->getDate();
        $to = $this->filter->to->getDate();

        foreach ($acclist as $acc) {

            $data = $acc->getSaldoAndOb($from, $to);  //получаем остатки  и  обороты  на  период


            $detail[] = array(
                "acc_code" => $acc->acc_code,
                'startdt' => H::fm($data['startdt']),
                'startct' => H::fm($data['startct']),
                'obdt' => H::fm($data['obdt']),
                'obct' => H::fm($data['obct']),
                'enddt' => H::fm($data['enddt']),
                'endct' => H::fm($data['endct'])
            );
            if ($acc->acc_pid == 0) {
                $totstartdt += $data['startdt'];
                $totstartct += $data['startct'];
                $totobdt += $data['obdt'];
                $totobct += $data['obct'];
                $totenddt += $data['enddt'];
                $totendct += $data['endct'];
            }
        }

        $header = array(
            'from' => date('d.m.Y', $from),
            'to' => date('d.m.Y', $to),
            'totstartdt' => H::fm($totstartdt),
            'totstartct' => H::fm($totstartct),
            'totobdt' => H::fm($totobdt),
            'totobct' => H::fm($totobct),
            'totenddt' => H::fm($totenddt),
            'totendct' => H::fm($totendct)
        );

        $report = new \ZippyERP\ERP\Report('obsaldo.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

}
