<?php

namespace App\Pages\Report;

use Zippy\Html\Form\Date;
use Zippy\Html\Form\Form;
use Zippy\Html\Label;
use Zippy\Html\Link\RedirectLink;
use Zippy\Html\Panel;
use App\Entity\Account;
use App\Helper as H;

/**
 * Оборотно-сальдовая ведомость
 */
class ObSaldo extends \App\Pages\Base
{

    public function __construct() {
        parent::__construct();


        if (\App\ACL::checkShowReport('ObSaldo') == false) {
            $this->setWarn('Недостаточно  прав  для просмотра');
            return;
        }


        $this->add(new Form('filter'))->onSubmit($this, 'OnSubmit');
        $this->filter->add(new Date('from', time() - (7 * 24 * 3600)));
        $this->filter->add(new Date('to', time()));

        $this->add(new Panel('detail'))->setVisible(false);
        $this->detail->add(new RedirectLink('print', ""));
        $this->detail->add(new RedirectLink('html', ""));
        $this->detail->add(new RedirectLink('word', ""));
        $this->detail->add(new RedirectLink('excel', ""));
        $this->detail->add(new Label('preview'));
    }

    public function OnSubmit($sender) {

        $html = $this->generateReport();
        $reportpage = "App/Pages/ShowReport";
        $reportname = "obsaldoreport";

        $this->detail->preview->setText($html, true);

        \App\Session::getSession()->printform = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head><body>" . $html . "</body></html>";
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

    private function generateReport() {

        $acclist = Account::find("", " acc_code ");

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
            if ($data['startdt'] + $data['startct'] + $data['obdt'] + $data['enddt'] + $data['enddt'] + $data['endct'] == 0)
                continue;

            $detail[] = array(
                "acc_code" => $acc->acc_code,
                'startdt' => H::famt($data['startdt']),
                'startct' => H::famt($data['startct']),
                'obdt' => H::famt($data['obdt']),
                'obct' => H::famt($data['obct']),
                'enddt' => H::famt($data['enddt']),
                'endct' => H::famt($data['endct'])
            );
            if (strlen($acc->pcode) > 0) {
                $totstartdt += $data['startdt'];
                $totstartct += $data['startct'];
                $totobdt += $data['obdt'];
                $totobct += $data['obct'];
                $totenddt += $data['enddt'];
                $totendct += $data['endct'];
            }
        }

        $header = array(
            "_detail" => $detail,
            'from' => date('d.m.Y', $from),
            'to' => date('d.m.Y', $to),
            'totstartdt' => H::famt($totstartdt),
            'totstartct' => H::famt($totstartct),
            'totobdt' => H::famt($totobdt),
            'totobct' => H::famt($totobct),
            'totenddt' => H::famt($totenddt),
            'totendct' => H::famt($totendct)
        );

        $report = new \App\Report('obsaldo.tpl');

        $html = $report->generate($header);

        return $html;
    }

}
