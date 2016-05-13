<?php

namespace ZippyERP\ERP\Pages\Report;

use Zippy\Html\Form\Date;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Label;
use Zippy\Html\Link\RedirectLink;
use Zippy\Html\Panel;

class ABC extends \ZippyERP\ERP\Pages\Base
{

    private $typelist = array();

    public function __construct()
    {
        parent::__construct();

        $this->typelist[1] = "Товары,  прибыль";
        $this->typelist[2] = "Поставщики, объем поставок";
        $this->typelist[3] = "Покупатели, объем продаж";
        $this->typelist[4] = "Услуги, выручка";

        $dt = new \Carbon\Carbon;
        $dt->subMonth();
        $from = $dt->startOfMonth()->timestamp;
        $to = $dt->endOfMonth()->timestamp;

        $this->add(new Form('filter'))->setSubmitHandler($this, 'OnSubmit');
        $this->filter->add(new Date('from', $from));
        $this->filter->add(new Date('to', $to));
        $this->filter->add(new DropDownChoice('type', $this->typelist, 1));


        $this->add(new Panel('detail'))->setVisible(false);
        $this->detail->add(new RedirectLink('print', "abc"));
        $this->detail->add(new RedirectLink('html', "abc"));
        $this->detail->add(new RedirectLink('excel', "abc"));
        $this->detail->add(new Label('preview'));
    }

    public function OnSubmit($sender)
    {

        $html = $this->generateReport();
        $this->detail->preview->setText($html, true);
        \ZippyERP\System\Session::getSession()->printform = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head><body>" . $html . "</body></html>";

        $reportpage = "ZippyERP/ERP/Pages/ShowReport";
        $reportname = "abc";

        $this->detail->preview->setAttribute('src', "/?p={$reportpage}&arg=preview/{$reportname}");

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

        $type = $this->filter->type->getValue();


        $from = $this->filter->from->getDate();
        $to = $this->filter->to->getDate();

        $header = array('from' => date('d.m.Y', $from),
            'to' => date('d.m.Y', $to),
            "type" => $this->typelist[$type]
        );

        $detail = array();

        if ($type == 1) {
            $detail = $this->find1();
        }
        if ($type == 2) {
            $detail = $this->find2();
        }
        if ($type == 3) {
            $detail = $this->find3();
        }
        if ($type == 4) {
            $detail = $this->find4();
        }

        $detail = $this->calc($detail);


        $report = new \ZippyERP\ERP\Report('abc.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    private function find1()
    {
        $list = array();
        $conn = \ZDB\DB::getConnect();
        $sql = "SELECT * FROM (
                    SELECT itemname as name, SUM( ABS( extcode ) ) AS value
                    FROM  `erp_account_subconto_view`
                    WHERE account_id in(26,281,282)
                    AND extcode >0
                    AND document_date >= " . $conn->DBDate($this->filter->from->getDate()) . "
                    AND document_date <= " . $conn->DBDate($this->filter->to->getDate()) . "
                    GROUP BY name
                    )t
                    ORDER BY value DESC";

        $rs = $conn->Execute($sql);
        foreach ($rs as $row) {
            $list[] = $row;
        }

        return $list;
    }

    private function find2()
    {
        $list = array();
        $conn = \ZDB\DB::getConnect();
        $sql = "SELECT * FROM (
                    SELECT customer_name as name, SUM( ABS( amount ) ) AS value
                    FROM  `erp_account_subconto_view`
                    WHERE account_id =63
                    AND amount >0
                    AND document_date >= " . $conn->DBDate($this->filter->from->getDate()) . "
                    AND document_date <= " . $conn->DBDate($this->filter->to->getDate()) . "
                    GROUP BY name
                    )t
                    ORDER BY value DESC";

        $rs = $conn->Execute($sql);
        foreach ($rs as $row) {
            $list[] = $row;
        }

        return $list;
    }

    private function find3()
    {
        $list = array();
        $conn = \ZDB\DB::getConnect();
        $sql = "SELECT * FROM (
                    SELECT customer_name as name, SUM( ABS( amount ) ) AS value
                    FROM  `erp_account_subconto_view`
                    WHERE account_id =36
                    AND amount <0
                    AND document_date >= " . $conn->DBDate($this->filter->from->getDate()) . "
                    AND document_date <= " . $conn->DBDate($this->filter->to->getDate()) . "
                    GROUP BY name
                    )t
                    ORDER BY value DESC";

        $rs = $conn->Execute($sql);
        foreach ($rs as $row) {
            $list[] = $row;
        }

        return $list;
    }

    private function find4()
    {
        $conn = \ZDB\DB::getConnect();
        //выбираем  по  Акту выполненых работ
        $where = "  meta_name ='ServiceAct'
                   AND document_date >= " . $conn->DBDate($this->filter->from->getDate()) . "
                   AND document_date <= " . $conn->DBDate($this->filter->to->getDate());

        $list = \ZippyERP\ERP\Entity\Doc\Document::find($where);
        $detail = array();

        foreach ($list as $item) {
            foreach ($item->detaildata as $row) {

                $detail[] = array('name' => $row['itemname'], 'value' => $row['amount']);
            }
        }

        return $detail;
    }

    //выполняет расчет  АВС
    private function calc($detail)
    {

        //   $detail =  \Pinq\Traversable::from($detail)
        //       ->orderByAscending(function($row){return $row['value'];})
        //       ->select(function($row){ return array('name'=>$row['name'],'value'=>$row['value'])   ;})->asArray();


        $sum = 0;
        $_detail = array();
        foreach ($detail as $row) {

            $row['value'] = round($row['value'] / 1000);
            $sum += $row['value'];
            $row['perc'] = 0;
            $row['percsum'] = 0;
            $row['group'] = '';
            $row['color'] = '';
            $_detail[] = $row;
        }
        $val = 0;
        for ($i = 0; $i < count($_detail); $i++) {
            $_detail[$i]['perc'] = $_detail[$i]['value'] / $sum * 100;
            $_detail[$i]['value'] = number_format($_detail[$i]['value'] / 100, 2, '.', '');
            $_detail[$i]['percsum'] = $_detail[$i]['perc'] + $val;
            if ($_detail[$i]['percsum'] <= 80) {
                $_detail[$i]['group'] = 'A';
                $_detail[$i]['color'] = '#AAFFAA';
            } else if ($_detail[$i]['percsum'] <= 95) {
                $_detail[$i]['group'] = 'B';
                $_detail[$i]['color'] = 'CCCCFF';
            } else {
                $_detail[$i]['group'] = 'C';
                $_detail[$i]['color'] = 'yellow';
            }
            $val = $_detail[$i]['percsum'];
            $_detail[$i]['perc'] = number_format($_detail[$i]['perc'], 2, '.', '');
            $_detail[$i]['percsum'] = number_format($_detail[$i]['percsum'], 2, '.', '');
        }
        return $_detail;
    }

}
