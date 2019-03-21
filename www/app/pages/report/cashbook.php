<?php

namespace App\Pages\Report;

use Carbon\Carbon;
use Zippy\Html\Form\CheckBox;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Label;
use Zippy\Html\Link\RedirectLink;
use Zippy\Html\Panel;
use App\Entity\Account;
use App\Entity\Doc\Document;
use App\Helper as H;
use App\System;

/**
 * Отчет кассовая книга
 */
class CashBook extends \App\Pages\Base
{

    public function __construct() {
        parent::__construct();

        $this->add(new Form('filter'))->onSubmit($this, 'OnSubmit');
        $this->filter->add(new DropDownChoice('yr', array(date('Y') - 1 => date('Y') - 1, date('Y') => date('Y'), date('Y') + 1 => date('Y') + 1), date('Y')));
        $this->filter->add(new DropDownChoice('mn', H::getMonth(), date('m')));
        $this->filter->add(new CheckBox('phead'));
        $this->add(new Panel('detail'))->setVisible(false);
        $this->detail->add(new RedirectLink('print', ""));
        $this->detail->add(new RedirectLink('html', ""));
        $this->detail->add(new RedirectLink('excel', ""));
        $this->detail->add(new Label('preview'));
    }

    public function OnSubmit($sender) {
        $html = $this->generateReport();
        $reportpage = "App/Pages/ShowReport";
        $reportname = "cashbook";

        $this->detail->preview->setText($html, true);


        \App\Session::getSession()->printform = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head><body>" . $html . "</body></html>";


        $this->detail->print->pagename = $reportpage;
        $this->detail->print->params = array('print', $reportname);
        $this->detail->html->pagename = $reportpage;
        $this->detail->html->params = array('html', $reportname);
        $this->detail->excel->pagename = $reportpage;
        $this->detail->excel->params = array('xls', $reportname);

        $this->detail->setVisible(true);
    }

    private function generateReport() {
        $report = new \App\Report('cashbook.tpl');

        $header = array();
        $firm = System::getOptions("firmdetail");


        $header['header'] = $this->filter->phead->isChecked();
        $header['firm'] = $firm['name'];
        ;
        $header['code'] = $firm['edrpou'];
        $mn = H::getMonth();
        $codes = H::getCodesList();
        $header['mn'] = $mn[$this->filter->mn->getValue()];
        $header['yr'] = $this->filter->yr->getValue();

        $date = new Carbon();
        $date->year($this->filter->yr->getValue());
        $date->month($this->filter->mn->getValue());
        $from = $date->startOfMonth()->timestamp;
        $to = $date->endOfMonth()->timestamp;


        $a30 = Account::load(30);

        $curamount = $a30->getSaldo($from);

        $conn = \ZDB\DB::getConnect();

        $sql = "SELECT  v.acc_d,v.acc_c,v.document_id FROM   account_entry_view v  
              WHERE  v.meta_name IN ('CashReceiptIn','CashReceiptOut')
              AND  v.document_date >= " . $conn->DBDate($from) . "  AND  v.document_date <= " . $conn->DBDate($to) . "   
              ORDER  by  v.document_date ";

        $rs = $conn->Execute($sql);


        $curdate = "";
        $detail = array();
        $page = array();
        $lines = array();
        foreach ($rs as $row) {
            $doc = Document::load($row['document_id']);
            $date = date("Y-m-d", $doc->document_date);

            if ($date != $curdate) { //дата  изменена
                $curdate = $date;
                if (count($page) > 0) {   //закрываем страницу
                    $page['lines'] = $lines;
                    $page['end'] = H::famt($curamount);
                    $detail[] = $page;
                    $page = array();
                    $lines = array();
                }
                $page['start'] = H::famt($curamount);
                $page['date'] = $curdate;
            }

            $line = array();
            $line['doc'] = $doc->document_number;
            $line['desc'] = $codes[$doc->headerdata['optype']];
            $line['desc'] = $line['desc'] . ' ' . $doc->headerdata['opdetailname'];
            if ($doc->meta_name == 'CashReceiptIn') { //Приходный
                $line['in'] = H::famt($doc->amount);
                $line['out'] = '-';
                $line['acc'] = $row['acc_c'];
                $curamount += $doc->amount;
            }
            if ($doc->meta_name == 'CashReceiptOut') { //расходный[jlysq]
                $line['out'] = H::famt($doc->amount);
                $line['in'] = '-';
                $line['acc'] = $row['acc_d'];
                $curamount -= $doc->amount;
            }


            $lines[] = $line;
        }

        if (count($page) > 0) {
            $page['lines'] = $lines;
            $page['end'] = H::famt($curamount);
            $detail[] = $page;
        }


        $html = $report->generate($header, $detail);

        return $html;
    }

}
