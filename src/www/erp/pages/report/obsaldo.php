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
        $this->detail->add(new RedirectLink('pdf', ""));
        $this->detail->add(new RedirectLink('word', ""));
        $this->detail->add(new RedirectLink('excel', ""));
        $this->detail->add(new Label('preview'));
    }

    public function OnSubmit($sender)
    {

        $html = $this->generateReport();
        $this->detail->preview->setText($html, true);

        \ZippyERP\System\Session::getSession()->printform = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head><body>" . $html . "</body></html>";
        $reportpage = "ZippyERP/ERP/Pages/ShowDoc";
        $this->detail->print->pagename = $reportpage;
        $this->detail->print->params = array('print', "obsaldoreport");
        $this->detail->pdf->pagename = $reportpage;
        $this->detail->pdf->params = array('pdf', "obsaldoreport");
        $this->detail->word->pagename = $reportpage;
        $this->detail->word->params = array('doc', "obsaldoreport");
        $this->detail->excel->pagename = $reportpage;
        $this->detail->excel->params = array('xls', "obsaldoreport");

        $this->detail->setVisible(true);
    }

    private function generateReport()
    {

        $acclist = Account::find("", "acc_code");

        $detail = array();
        $totstartdt = 0;
        $totstartct = 0;
        $totobdt = 0;
        $totobct = 0;
        $totenddt = 0;
        $totendct = 0;

        $from = strtotime($this->filter->from->getValue());
        $to = strtotime($this->filter->to->getValue());

        foreach ($acclist as $acc) {

            $data = $acc->getSaldoAndOb($from, $to);  //получаем остатки  и  обороты  на  период

            $detail[] = array(
                "acc_code" => $acc->acc_code,
                'startdt' => number_format($data['startdt'] / 100, 2),
                'startct' => number_format($data['startct'] / 100, 2),
                'obdt' => number_format($data['obdt'] / 100, 2),
                'obct' => number_format($data['obct'] / 100, 2),
                'enddt' => number_format($data['enddt'] / 100, 2),
                'endct' => number_format($data['endct'] / 100, 2)
            );
            if ($data['parent'] != true) {  //только  для счетов а не  для   субсчетов
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
            'totstartdt' => number_format($totstartdt / 100, 2),
            'totstartct' => number_format($totstartct / 100, 2),
            'totobdt' => number_format($totobdt / 100, 2),
            'totobct' => number_format($totobct / 100, 2),
            'totenddt' => number_format($totenddt / 100, 2),
            'totendct' => number_format($totendct / 100, 2)
        );

        $reportgen = new \ZCL\RepGen\RepGen(_ROOT . 'templates/erp/templates/obsaldo.html', $header);

        $html = $reportgen->generateSimple($detail);
        if (strlen($html) == 0) {
            $this->setError("Не найден шаблон печатной формы");
            return "";
        }
        return $html;
    }

}
