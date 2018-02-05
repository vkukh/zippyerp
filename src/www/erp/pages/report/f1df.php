<?php

namespace ZippyERP\ERP\Pages\Report;

use Zippy\Html\Form\Button;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Link\RedirectLink;
use Zippy\Html\Link\SubmitLink;
use Zippy\Html\Panel;
use ZippyERP\ERP\Entity\Employee;
use ZippyERP\ERP\Helper as H;
use ZippyERP\System\System;

/**
 * Отчет форма 1ДФ
 */
class F1df extends \ZippyERP\ERP\Pages\Base
{

    public $_emplist = array();
    private $_rowid = 0;
    private $_mil = 0;

    public function __construct() {
        parent::__construct();
        $this->add(new Form('filter'));
        $this->filter->add(new DropDownChoice('yr'))->setValue(2016);
        $this->filter->add(new DropDownChoice('qw'))->setValue(1);
        $this->filter->add(new SubmitLink('load'))->onClick($this, 'OnLoad');
        $this->filter->add(new SubmitLink('show'))->onClick($this, 'OnShow');

        $this->add(new Panel('listp'));
        $this->filter->add(new ClickLink('addrow'))->onClick($this, 'addrowOnClick');

        $this->listp->add(new \Zippy\Html\DataList\DataView('list', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, '_emplist')), $this, 'detailOnRow'));

        $this->add(new Form('editemp'))->setVisible(false);
        ;
        $this->editemp->add(new DropDownChoice('editemployee', Employee::findArray('fullname', "", "fullname")));
        $this->editemp->add(new TextInput('editincome'));
        $this->editemp->add(new TextInput('editoutcome'));
        $this->editemp->add(new TextInput('edittax'));
        $this->editemp->add(new TextInput('editcode'));

        $this->editemp->add(new Button('cancelrow'))->onClick($this, 'cancelrowOnClick');
        $this->editemp->add(new SubmitButton('submitrow'))->onClick($this, 'saverowOnClick');


        $this->add(new Panel('detail'))->setVisible(false);
        $this->detail->add(new RedirectLink('print', ""));
        $this->detail->add(new RedirectLink('html', ""));
        $this->detail->add(new RedirectLink('xml', ""));
        $this->detail->add(new RedirectLink('excel', ""));
        $this->detail->add(new Label('preview'));


        $this->OnLoad(null);
    }

    public function detailOnRow($row) {
        $emp = $row->getDataItem();

        $row->add(new Label('emp', $emp->getInitName()));

        $row->add(new Label('income', H::fm($emp->income)));
        $row->add(new Label('outcome', H::fm($emp->outcome)));
        $row->add(new Label('tax', H::fm($emp->tax)));
        $row->add(new Label('code', $emp->code));
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function addrowOnClick($sender) {
        $this->listp->setVisible(false);
        $this->editemp->setVisible(true);
        $this->_rowid = 0;
    }

    public function editOnClick($sender) {
        $emp = $sender->getOwner()->getDataItem();
        $this->editemp->editemployee->setValue($emp->employee_id);
        $this->editemp->editincome->setText(H::fm($emp->income));
        $this->editemp->editoutcome->setText(H::fm($emp->outcome));
        $this->editemp->edittax->setText(H::fm($emp->tax));
        $this->editemp->editcode->setText($emp->code);

        $this->_rowid = $emp->employee_id;
        $this->listp->setVisible(false);
        $this->editemp->setVisible(true);
    }

    public function deleteOnClick($sender) {
        $emp = $sender->owner->getDataItem();

        $this->_emplist = array_diff_key($this->_emplist, array($emp->employee_id => $this->_emplist[$emp->employee_id]));

        $this->listp->list->Reload();
    }

    public function saverowOnClick($sender) {

        $id = $this->editemp->editemployee->getValue();
        if ($id == 0) {
            $this->setError("Не вибраний співробітник");
            return;
        }
        $emp = Employee::load($id);

        $emp->income = 100 * $this->editemp->editincome->getText();
        $emp->outcome = 100 * $this->editemp->editoutcome->getText();
        $emp->tax = 100 * $this->editemp->edittax->getText();
        $emp->code = $this->editemp->editcode->getText();

        unset($this->_emplist[$this->_rowid]);
        $this->_emplist[$emp->employee_id] = $emp;
        $this->listp->setVisible(true);
        $this->editemp->setVisible(false);
        $this->editemp->Clean();
        $this->listp->list->Reload();
    }

    public function cancelrowOnClick($sender) {
        $this->listp->setVisible(true);
        $this->editemp->setVisible(false);
    }

    public function OnLoad($sender) {
        $this->detail->setVisible(false);
        $this->_emplist = array();
        $conn = \ZDB\DB::getConnect();
        $year = $this->filter->yr->getValue();
        $qw = $this->filter->qw->getValue();

        $date = new \Carbon\Carbon();
        $date->year($year)->startOfYear();
        $from = $date->timestamp;
        $date->addMonths($qw * 3);
        $to = $date->timestamp - 1;

        $where = "select employee_id from erp_account_subconto where account_id =66 and  document_date >= " . $conn->DBDate($from) . "  and document_date <= " . $conn->DBDate($to);
        $list = Employee::find("employee_id in({$where})", "fullname");


        foreach ($list as $emp) {
            //выплачено
            $sql = "select coalesce(sum(amount),0) from erp_account_subconto_view where account_id =66 and meta_name='OutSalary' and amount >0  and employee_id = {$emp->employee_id} and  document_date >= " . $conn->DBDate($from) . "  and document_date <= " . $conn->DBDate($to);
            $out = $conn->GetOne($sql);
            $emp->outcome = $out;
            //начислено
            $sql = "select coalesce(sum(amount),0) from erp_account_subconto_view where account_id =66 and meta_name='InSalary' and amount <0  and employee_id = {$emp->employee_id} and  document_date >= " . $conn->DBDate($from) . "  and document_date <= " . $conn->DBDate($to);
            $in = $conn->GetOne($sql);
            $emp->income = 0 - $in;
            //налог
            $sql = "select abs(coalesce(sum(amount),0)) from erp_account_subconto_view where account_id =66 and meta_name='InSalary' and extcode = " . \ZippyERP\ERP\Consts::TAX_NDFL . "  and employee_id = {$emp->employee_id} and  document_date >= " . $conn->DBDate($from) . "  and document_date <= " . $conn->DBDate($to);
            $tax = $conn->GetOne($sql);
            $emp->tax = $tax;

            $emp->code = 101;
            if ($emp->combined > 0) {
                $emp->code = 102; //совместитель
            }
            $this->_emplist[$emp->employee_id] = $emp;
        }

        //военый сбор
        $sql = "select coalesce(sum(amount),0) from erp_account_subconto_view where account_id =66 and meta_name='InSalary'  and extcode = " . \ZippyERP\ERP\Consts::TAX_MIL . " and  document_date >= " . $conn->DBDate($from) . "  and document_date <= " . $conn->DBDate($to);
        $mil = $conn->GetOne($sql);
        $this->_mil = $mil;

        $this->listp->list->Reload();
    }

    public function OnShow($sender) {
        $header = $this->getHeaderData();

        $html = $this->generateReport($header);
        $reportpage = "ZippyERP/ERP/Pages/ShowReport";
        $reportname = "1df";

        \ZippyERP\System\Session::getSession()->printform = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head><body>" . $html . "</body></html>";
        \ZippyERP\System\Session::getSession()->printxml = $this->exportGNAU($header);

        $this->detail->preview->setAttribute('src', "/?p={$reportpage}&arg=preview/{$reportname}");

        $this->detail->print->pagename = $reportpage;
        $this->detail->print->params = array('print', $reportname);
        $this->detail->html->pagename = $reportpage;
        $this->detail->html->params = array('html', $reportname);
        $this->detail->excel->pagename = $reportpage;
        $this->detail->excel->params = array('xls', $reportname);
        $this->detail->xml->pagename = $reportpage;
        $this->detail->xml->params = array('xml', $reportname);

        $this->detail->setVisible(true);
    }

    public function generateReport($header) {


        $report = new \ZippyERP\ERP\Report('f1df.tpl');


        $html = $report->generate($header, $header['details']);

        return $html;
    }

    private function getHeaderData() {
        $header = array();
        $header['details'] = array();

        $year = $this->filter->yr->getValue();
        $pm = (string) sprintf('%02d', 3 * $this->filter->qw->getValue());

        $common = System::getOptions("common");

        $firm = System::getOptions("firmdetail");
        $header['HNAME'] = $firm['name'];
        $header['HTIN'] = H::addSpaces($common['juridical'] == true ? $firm['edrpou'] : $firm['inn']);
        $header['HZY'] = $year;
        $header['HZP'] = $pm;
        $header['HJ'] = $common['juridical'] == true ? 'X' : '';
        $header['HF'] = $common['juridical'] == false ? 'X' : '';
        if ($common['juridical']) {
            if ($common['manager'] > 0) {
                $boss = Employee::load($common['manager']);
                $header['HBOS'] = $boss->getInitName();
                $header['HKBOS'] = $boss->inn;
                $header['HKBOS_'] = H::addSpaces($boss->inn);
            }
        } else {
            $owner = \ZippyERP\ERP\Entity\Contact::load($common['owner']);
            $header['HFO'] = $owner->getInitName();
            $header['HKFO'] = $firm['inn'];
            $header['HKFO_'] = H::addSpaces($firm['inn']);
        }
        if ($common['accounter'] > 0) {
            $accounter = Employee::load($common['accounter']);
            $header['HBUH'] = $accounter->getInitName();
            $header['HKBUH'] = $accounter->inn;
            $header['HKBUH_'] = H::addSpaces($accounter->inn);
        }
        $header['R00G011'] = 0;
        $header['R00G021'] = 0;
        $header['R01G03A'] = 0;
        $header['R01G03'] = 0;
        $header['R01G04A'] = 0;


        $header['R02G011'] = count($this->_emplist);
        $_mps = array();
        $num = 1;
        foreach ($this->_emplist as $emp) {
            $_mps[$emp->employee_id] = 1;
            if ($emp->combined > 0)
                $header['R00G021'] ++;
            else
                $header['R00G011'] ++;
            $header['details'][] = array('number' => $num++,
                'RXXXXG02' => $emp->inn,
                'RXXXXG02_' => H::addSpaces($emp->inn),
                'RXXXXG03A' => H::fm($emp->income),
                'RXXXXG03' => H::fm($emp->outcome),
                'RXXXXG04A' => H::fm($emp->tax),
                'RXXXXG05' => $emp->code
            );
            $header['R01G03A'] += $emp->income;
            $header['R01G03'] += $emp->outcome;
            $header['R01G04A'] += $emp->tax;
        }
        $header['R02G021'] = count(array_keys($_mps));
        $header['R00G011_'] = H::addSpaces($header['R00G011']);
        $header['R00G021_'] = H::addSpaces($header['R00G021']);
        $header['R02G011_'] = H::addSpaces($header['R02G011']);
        $header['R02G021_'] = H::addSpaces($header['R02G021']);

        $header['R01G03A'] = H::fm($header['R01G03A']);
        $header['R01G03'] = H::fm($header['R01G03']);
        $header['R01G04A'] = H::fm($header['R01G04A']);
        $header['MIL'] = H::fm($this->_mil);


        return $header;
    }

    public function exportGNAU($header) {
        $year = $this->filter->yr->getValue();
        $pm = (string) sprintf('%02d', 3 * $this->filter->qw->getValue());

        $common = System::getOptions("common");
        $firm = System::getOptions("firmdetail");
        $jf = ($common['juridical'] == true ? "J" : "F") . "0500102";
        $hjhf = ($common['juridical'] == true) ? "<HJ>1</HJ>" : "<HF>1</HF>";

        $edrpou = (string) sprintf("%10d", $firm['edrpou']);
        $tin = $common['juridical'] == true ? $firm['edrpou'] : $firm['inn'];

        $number = (string) sprintf('%07d', 1);
        $filename = $firm['gni'] . $edrpou . $jf . "100{$number}2" . $pm . $year . $firm['gni'] . ".xml";
        $filename = str_replace(' ', '0', $filename);

        if (isset($header['HKBOS'])) {
            $boss = "<HKBOS>" . $header['HKBOS'] . "</HKBOS>";
            $boss .= "<HBOS>" . $header['HBOS'] . "</HBOS>";
        }
        if (isset($header['HKBUH'])) {
            $buh = "<HKBUH>" . $header['HKBUH'] . "</HKBUH>";
            $buh .= "<HBUH>" . $header['HBUH'] . "</HBUH>";
        }
        if (isset($header['HKFO'])) {
            $hfo = "<HKFO>" . $header['HKFO'] . "</HKFO>";
            $hfo .= "<HFO>" . $header['HFO'] . "</HFO>";
        }

        $xml = "<?xml version=\"1.0\" encoding=\"windows-1251\" ?>
  <DECLAR xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"J0901106.xsd\">
  <DECLARHEAD>
  <TIN>{$tin}</TIN>
  <C_DOC>J09</C_DOC>
  <C_DOC_SUB>011</C_DOC_SUB>
  <C_DOC_VER>6</C_DOC_VER>
  <C_DOC_TYPE>0</C_DOC_TYPE>
  <C_DOC_CNT>1</C_DOC_CNT>
  <C_REG>" . substr($firm['gni'], 0, 2) . "</C_REG>
  <C_RAJ>" . substr($firm['gni'], 2, 2) . "</C_RAJ>
  <PERIOD_MONTH>{$pm}</PERIOD_MONTH>
  <PERIOD_TYPE>2</PERIOD_TYPE>
  <PERIOD_YEAR>{$year}</PERIOD_YEAR>
  <C_STI_ORIG>{$firm['gni']}</C_STI_ORIG>
  <C_DOC_STAN>1</C_DOC_STAN>
  <LINKED_DOCS xsi:nil=\"true\" />
  <D_FILL>" . (string) date('dmY') . "</D_FILL>
  <SOFTWARE>Zippy ERP</SOFTWARE>
  </DECLARHEAD>
  <DECLARBODY>
  <HFILL>" . (string) date('dmY') . "</HFILL>
  <HNAME>{$header['HNAME']}</HNAME>
  <HTIN>{$tin}</HTIN>

  <HZY>{$year}</HZY>
  <HZP>{$pm}</HZP>
  {$boss}{$buh}{$hfo}

  <R00G011>{$header['R00G011']}</R00G011>
  <R00G021>{$header['R00G021']}</R00G021>
  <R01G03A>{$header['R01G03A']}</R01G03A>
  <R01G03>{$header['R01G03']}</R01G03>
  <R01G04A>{$header['R01G04A']}</R01G04A>
  <R02G011>{$header['R02G011']}</R02G011>
  <R02G021>{$header['R02G021']}</R02G021>";
        $num = 1;
        foreach ($header['details'] as $row) {
            $xml .= "<RXXXXG02 ROWNUM=\"{$num}\">{$row['RXXXXG02']}</RXXXXG02>";
            $xml .= "<RXXXXG03A ROWNUM=\"{$num}\">{$row['RXXXXG03A']}</RXXXXG03A>";
            $xml .= "<RXXXXG04A ROWNUM=\"{$num}\">{$row['RXXXXG04A']}</RXXXXG04A>";
            $xml .= "<RXXXXG05 ROWNUM=\"{$num}\">{$row['RXXXXG05']}</RXXXXG05>";
            $num++;
        }

        $xml .= "</DECLARBODY>
  </DECLAR>";

        return $xml;
    }

}
