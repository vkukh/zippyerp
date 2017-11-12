<?php

namespace ZippyERP\ERP\Pages\Report;

use Zippy\Html\Form\Date;
use Zippy\Html\Form\Form;
use Zippy\Html\Label;
use Zippy\Html\Link\RedirectLink;
use Zippy\Html\Panel;
use ZippyERP\ERP\Consts as C;
use ZippyERP\ERP\Helper as H;
use ZippyERP\System\System;

/**
 * Отчет книга доходов  и расходов
 */
class IEBook extends \ZippyERP\ERP\Pages\Base
{

    public function __construct()
    {
        parent::__construct();
        $dt = new \Carbon\Carbon;
        $dt->subMonth();
        $from = $dt->startOfMonth()->timestamp;
        $to = $dt->endOfMonth()->timestamp;

        $this->add(new Form('filter'))->onSubmit($this, 'OnSubmit');
        $this->filter->add(new Date('from', $from));
        $this->filter->add(new Date('to', $to));
        $this->add(new Panel('detail'))->setVisible(false);
        $this->detail->add(new RedirectLink('print', ""));
        $this->detail->add(new RedirectLink('html', ""));
        $this->detail->add(new RedirectLink('excel', ""));
        $this->detail->add(new Label('preview'));
    }

    public function OnSubmit($sender)
    {
        $html = $this->generateReport();
        $reportpage = "ZippyERP/ERP/Pages/ShowReport";
        $reportname = "iebook";

        $this->detail->preview->setAttribute('src', "/?p={$reportpage}&arg=preview/{$reportname}");


        \ZippyERP\System\Session::getSession()->printform = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head><body>" . $html . "</body></html>";


        $this->detail->print->pagename = $reportpage;
        $this->detail->print->params = array('print', $reportname);
        $this->detail->html->pagename = $reportpage;
        $this->detail->html->params = array('html', $reportname);
        $this->detail->excel->pagename = $reportpage;
        $this->detail->excel->params = array('xls', $reportname);

        $this->detail->setVisible(true);
    }

    private function generateReport()
    {
        $report = new \ZippyERP\ERP\Report('iebook.tpl');

        $header = array();
        //$detail = array();
        $firm = System::getOptions("firmdetail");
        $common = System::getOptions("common");

        $header['firm'] = $firm['name'];
        ;
        $header['code'] = $firm['edrpou'];
        $header['nds'] = $common['hasnds'] == true;

        $from = $this->filter->from->getDate();
        $to = $this->filter->to->getDate();


        $conn = \ZDB\DB::getConnect();

        $sql = "SELECT v.document_date,
              sum( case when extcode in(" . C::TYPEOP_CUSTOMER_IN . "," . C::TYPEOP_RET_IN . ")   then  amount else 0 end   ) as income ,
              sum( case when extcode in( " . C::TYPEOP_CUSTOMER_OUT_BACK . "," . C::TYPEOP_CUSTOMER_OUT_PREV . " )  then 0-amount else 0 end   ) as back
              from  erp_account_subconto_view v WHERE  v.account_id IN (30,31)
              AND  v.document_date >= " . $conn->DBDate($from) . "  AND  v.document_date <= " . $conn->DBDate($to) . "
              GROUP  by  v.document_date
              HAVING  (income + back) >0
              ORDER  by  v.document_date ";

        $rs = $conn->Execute($sql);


        $sql = "SELECT v.document_date,
              sum( case when extcode in(" . C::TYPEOP_CUSTOMER_OUT . ")   then 0- amount else 0 end   ) as expence ,
              sum( case when extcode = " . C::TYPEOP_COMMON_EXPENCES . "  then 0-amount else 0 end   ) as commonexpence ,
              sum( case when extcode = " . C::TYPEOP_CASH_SALARY . "  then 0-amount else 0 end   ) as salary ,
              sum( case when extcode  = " . C::TAX_ECB . "   then 0-amount else 0 end   ) as ecb
              from  erp_account_subconto_view v WHERE  v.account_id IN (30,31)
              AND  v.document_date >= " . $conn->DBDate($from) . "  AND  v.document_date <= " . $conn->DBDate($to) . "
              GROUP  by  v.document_date
              HAVING  (expence + commonexpence + salary + ecb) >0
              ORDER  by  v.document_date ";


        $rs2 = $conn->Execute($sql);

        $lines = array();
        $lines2 = array();

        if ($header['nds'] == true) {

            $nds = 1 - H::nds(true);

            foreach ($rs as $row) {
                $row['date'] = date("d-m-Y", strtotime($row['document_date']));
                $row['col2'] = number_format($row['income'] * $nds, 2, '.', '');
                $row['col3'] = number_format($row['back'] * $nds, 2, '.', '');
                $row['col4'] = number_format($row['income'] * $nds - $row['back'] * $nds, 2, '.', '');
                $row['col5'] = " ";
                $row['col6'] = number_format($row['income'] * $nds - $row['back'] * $nds, 2, '.', '');
                $row['col7'] = " ";
                $row['col8'] = " ";
                $lines[] = $row;
            }

            foreach ($rs2 as $row) {
                $row['date'] = date("d-m-Y", strtotime($row['document_date']));
                $row['col2'] = '';
                $row['col3'] = number_format($row['expence'] * $nds, 2, '.', '');
                $row['col4'] = number_format($row['salary'], 2, '.', '');
                $row['col5'] = number_format($row['ecb'], 2, '.', '');
                $row['col6'] = number_format($row['commonexpence'], 2, '.', '');
                $row['col7'] = number_format($row['expence'] * $nds + $row['salary'] + $row['ecb'] + $row['commonexpence'], 2, '.', '');

                $lines2[] = $row;
            }
        } else {

            foreach ($rs as $row) {
                $row['date'] = date("d-m-Y", strtotime($row['document_date']));
                $row['col2'] = $row['income'];
                $row['col3'] = $row['back'];
                $row['col4'] = number_format($row['income'] - $row['back'], 2, '.', '');
                $row['col5'] = " ";
                $row['col6'] = number_format($row['income'] - $row['back'], 2, '.', '');
                $row['col7'] = " ";
                $row['col8'] = " ";
                $lines[] = $row;
            }
        }

        $html = $report->generate($header, $lines, $lines2);

        return $html;
    }

}
