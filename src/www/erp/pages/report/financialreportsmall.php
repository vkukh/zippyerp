<?php

namespace ZippyERP\ERP\Pages\Report;

use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\Date;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Panel;
use \ZippyERP\ERP\Entity\Account;
use \ZippyERP\ERP\Entity\SubConto;
use \Zippy\Html\Link\RedirectLink;
use \ZippyERP\ERP\Helper as H;
use \ZippyERP\System\System;
use \Carbon\Carbon;

/**
 * финансовый отчет  малого  предприятия
 */
class FinancialReportSmall extends \ZippyERP\ERP\Pages\Base
{

    public function __construct()
    {
        parent::__construct();

        $this->add(new Form('filter'))->setSubmitHandler($this, 'OnSubmit');
        $this->filter->add(new DropDownChoice('yr'));
        $this->filter->add(new DropDownChoice('qw'));

        $this->add(new Panel('detail'))->setVisible(false);
        $this->detail->add(new RedirectLink('print', ""));
        $this->detail->add(new RedirectLink('html', ""));
        $this->detail->add(new RedirectLink('xml', ""));
        $this->detail->add(new RedirectLink('excel', ""));
        $this->detail->add(new Label('preview'));
    }

    public function OnSubmit($sender)
    {
        $header = $this->getHeaderData();

        $html = $this->generateReport($header);
        $reportpage = "ZippyERP/ERP/Pages/ShowReport";
        $reportname = "finreport25";

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

    private function getHeaderData()
    {


        $detail = array();
        $totstartdt = 0;
        $totstartct = 0;
        $totobdt = 0;
        $totobct = 0;
        $totenddt = 0;
        $totendct = 0;

        $year = $this->filter->yr->getValue();
        $qw = $this->filter->qw->getValue();

        $date = new Carbon();
        $date->year($year)->startOfYear();
        $from = $date->timestamp;
        $date->addMonths($qw * 3);
        $to = $date->timestamp - 1;

        $firm = System::getOptions("firmdetail");

        $a10 = Account::load(10);
        $a11 = Account::load(11);
        $a12 = Account::load(12);
        $a13 = Account::load(13);
        $a15 = Account::load(15);
        $a20 = Account::load(20);
        $a22 = Account::load(22);
        $a23 = Account::load(23);
        $a26 = Account::load(26);
        $a28 = Account::load(28);
        $a30 = Account::load(30);
        $a31 = Account::load(31);
        $a36 = Account::load(36);
        $a37 = Account::load(37);
        $a40 = Account::load(40);
        $a641 = Account::load(641);
        $a642 = Account::load(642);
        $a643 = Account::load(643);
        $a644 = Account::load(644);
        $a63 = Account::load(63);
        $a66 = Account::load(66);
        $a68 = Account::load(68);
        $a70 = Account::load(70);
        $a701 = Account::load(701);
        $a702 = Account::load(702);
        $a703 = Account::load(703);
        //  $a704 = Account::load(704);
        $a71 = Account::load(71);

        $a79 = Account::load(79);
        $a90 = Account::load(90);
        $a91 = Account::load(91);
        $a92 = Account::load(92);
        $a93 = Account::load(93);
        $a94 = Account::load(94);
        $a97 = Account::load(97);


        //актив
        $b1011 = $a10->getSaldoD($from) + $a11->getSaldoD($from) + $a12->getSaldoD($from);
        $e1011 = $a10->getSaldoD($to) + $a11->getSaldoD($to) + $a12->getSaldoD($to);
        ;
        $b1012 = $a13->getSaldoD($from);
        $e1012 = $a13->getSaldoD($to);

        $b1010 = $b1011 - $b1012;
        $e1010 = $e1011 - $e1012;

        $b1005 = $a15->getSaldoD($from);
        $e1005 = $a15->getSaldoD($to);

        $b1095 = $b1005 + $b1010;
        $e1095 = $e1005 + $e1010;

        $b1100 = $a20->getSaldoD($from) + $a22->getSaldoD($from) + $a23->getSaldoD($from);
        $e1100 = $a20->getSaldoD($to) + $a22->getSaldoD($to) + $a23->getSaldoD($to);
        $b1103 = $a26->getSaldoD($from) + $a28->getSaldoD($from);
        $e1103 = $a26->getSaldoD($to) + $a28->getSaldoD($to);


        $b1100 = $b1100 + $b1103;
        $e1100 = $e1100 + $e1103;

        $b1125 = $a36->getSaldoD($from);
        $e1125 = $a36->getSaldoD($to);
        $b1135 = $a641->getSaldoD($from) + $a642->getSaldoD($from);
        $e1135 = $a641->getSaldoD($to) + $a642->getSaldoD($to);
        // $b1136 = SubConto::getAmount($from,641,0,0,0,0,0,666);
        // $e1136 = SubConto::getAmount($to,641,0,0,0,0,0,666);
        $b1155 = $a63->getSaldoD($from) + $a37->getSaldoD($from) + $a68->getSaldoD($from);
        $e1155 = $a63->getSaldoD($to) + $a37->getSaldoD($to) + $a68->getSaldoD($to);
        $b1165 = $a30->getSaldoD($from) + $a31->getSaldoD($from);
        $e1165 = $a30->getSaldoD($to) + $a31->getSaldoD($to);
        $b1190 = $a643->getSaldoD($from) + $a644->getSaldoD($from);
        $e1190 = $a643->getSaldoD($to) + $a644->getSaldoD($to);

        $b1195 = $b1100 + $b1125 + $b1135 + $b1155 + $b1165 + $b1190;
        $e1195 = $e1100 + $e1125 + $e1135 + $e1155 + $e1165 + $e1190;

        $b1300 = $b1095 + $b1195;
        $e1300 = $e1095 + $e1195;

        //пассив

        $b1400 = $a40->getSaldoC($from);
        $e1400 = $a40->getSaldoC($to);

        $b1420 = $a79->getSaldoC($from);
        $e1420 = $a79->getSaldoC($to);

        $b1495 = $b1420;
        $e1495 = $e1420;

        $b1420 = $b1420 > 0 ? $b1420 : "({$b1420})";
        $e1420 = $e1420 > 0 ? $e1420 : "({$e1420})";

        $b1615 = $a63->getSaldoC($from);
        $e1615 = $a63->getSaldoC($to);
        $b1620 = $a641->getSaldoC($from);
        $e1620 = $a641->getSaldoC($to);
        // $b1621 = SubConto::getAmount($from,641,0,0,0,0,0,666);
        // $e1621 = SubConto::getAmount($to,641,0,0,0,0,0,666);

        $b1630 = $a66->getSaldoC($from);
        $e1630 = $a66->getSaldoC($to);
        $b1690 = $a36->getSaldoC($from) + $a37->getSaldoC($from) + $a643->getSaldoC($from) + $a644->getSaldoC($from) + $a68->getSaldoC($from);
        $e1690 = $a36->getSaldoC($to) + $a37->getSaldoC($to) + $a643->getSaldoC($to) + $a644->getSaldoC($to) + $a68->getSaldoC($to);

        $b1695 = $b1615 + $b1620 + $b1630 + $b1690;
        $e1695 = $e1615 + $e1620 + $e1630 + $e1690;

        $b1900 = $b1400 + $b1495 + $b1695;
        $e1900 = $e1400 + $e1495 + $e1695;



        //форма 2
        $_from = date('- 1 year', $from);
        $_to = date('- 1 year', $to);


        $ob701 = $a701->getSaldoAndOb($from, $to);
        $ob702 = $a702->getSaldoAndOb($from, $to);
        $ob703 = $a703->getSaldoAndOb($from, $to);
        $b2000 = $ob701['obct'] + $ob702['obct'] + $ob703['obct'];
        $b2000 -= Account::getObBetweenAccount(70, 30, $from, $to);
        $b2000 -= Account::getObBetweenAccount(70, 31, $from, $to);
        $b2000 -= Account::getObBetweenAccount(70, 36, $from, $to);
        $b2000 -= Account::getObBetweenAccount(70, 641, $from, $to);
        $b2000 -= Account::getObBetweenAccount(70, 642, $from, $to);
        $b2000 -= Account::getObBetweenAccount(70, 643, $from, $to);

        $ob71 = $a71->getSaldoAndOb($from, $to);
        $b2120 = $ob71['obct'];
        $b2120 -= Account::getObBetweenAccount(71, 641, $from, $to);
        $b2120 -= Account::getObBetweenAccount(71, 643, $from, $to);

        //$ob72 = $a72->getSaldoAndOb($from,$to);
        //$b2240   = $ob72['obct'];  73 74
        $b2240 = 0;
        $b2280 = $b2000 + $b2120 + $b2240;

        $ob90 = $a90->getSaldoAndOb($from, $to);
        $ob91 = $a91->getSaldoAndOb($from, $to);
        $ob92 = $a92->getSaldoAndOb($from, $to);
        $ob93 = $a93->getSaldoAndOb($from, $to);
        $ob94 = $a94->getSaldoAndOb($from, $to);
        $ob97 = $a97->getSaldoAndOb($from, $to);
        // $ob98 = $a98->getSaldoAndOb($from,$to);
        $b2050 = $ob90['obdt'];
        $b2180 = $ob92['obdt'] + $ob93['obdt'] + $ob94['obdt'];

        $b2270 = $ob97['obdt'];
        $b2285 = $b2050 + $b2180 + $b2270;
        $b2290 = $b2280 - $b2285;
        //$b2300 =  $ob98['obdt']   ;
        $b2350 = $b2290 - $b2300;

        $ob701 = $a701->getSaldoAndOb($_from, $_to);
        $ob702 = $a702->getSaldoAndOb($_from, $_to);
        $ob703 = $a703->getSaldoAndOb($_from, $_to);
        $e2000 = $ob701['obct'] + $ob702['obct'] + $ob703['obct'];
        $e2000 -= Account::getObBetweenAccount(70, 30, $_from, $_to);
        $e2000 -= Account::getObBetweenAccount(70, 31, $_from, $_to);
        $e2000 -= Account::getObBetweenAccount(70, 36, $_from, $_to);
        $e2000 -= Account::getObBetweenAccount(70, 641, $_from, $_to);
        $e2000 -= Account::getObBetweenAccount(70, 642, $_from, $_to);
        $e2000 -= Account::getObBetweenAccount(70, 643, $_from, $_to);

        $ob71 = $a71->getSaldoAndOb($_from, $_to);
        $e2120 = $ob71['obct'];
        $e2120 -= Account::getObBetweenAccount(71, 641, $_from, $_to);
        $e2120 -= Account::getObBetweenAccount(71, 643, $_from, $_to);

        //$ob72 = $a72->getSaldoAndOb($_from,$_to);
        //$e2240   = $ob72['obct'];
        $e2240 = 0;
        $e2280 = $e2000 + $e2120 + $e2240;

        $ob90 = $a90->getSaldoAndOb($_from, $_to);
        $ob91 = $a91->getSaldoAndOb($_from, $_to);
        $ob92 = $a92->getSaldoAndOb($_from, $_to);
        $ob93 = $a93->getSaldoAndOb($_from, $_to);
        $ob94 = $a94->getSaldoAndOb($_from, $_to);
        $ob97 = $a97->getSaldoAndOb($_from, $_to);
        // $ob98 = $a98->getSaldoAndOb($_from,$_to);
        $e2050 = $ob90['obdt'];
        $e2180 = $ob92['obdt'] + $ob93['obdt'] + $ob94['obdt'];

        $e2270 = $ob97['obdt'];
        $e2285 = $e2050 + $e2180 + $b2270;
        $e2290 = $e2280 - $e2285;
        //$e2300 =  $ob98['obdt']   ;
        $e2350 = $e2290 - $e2300;


        $header = array(
            'date1y' => date('Y', time()),
            'date1m' => date('m', time()),
            'date1d' => date('d', time()),
            'date2' => date('d.m.Y', $to + 1),
            'edrpou' => (string) sprintf("%10d", $firm['edrpou']),
            'koatuu' => (string) sprintf("%10d", $firm['koatuu']),
            'kopfg' => (string) sprintf("%10d", $firm['kopfg']),
            'kodu' => (string) sprintf("%10d", $firm['kodu']),
            'kved' => (string) sprintf("%10s", $firm['kved']),
            'address' => $firm->dtreet . ' ' . $firm->city . ', ' . $firm->phone,
            'firmname' => $firm['name'],
            'b1005' => H::fm_t1($b1005),
            'e1005' => H::fm_t1($e1005),
            'b1010' => H::fm_t1($b1010),
            'e1010' => H::fm_t1($e1010),
            'b1011' => H::fm_t1($b1011),
            'e1011' => H::fm_t1($e1011),
            'b1012' => H::fm_t1($b1012),
            'e1012' => H::fm_t1($e1012),
            'b1095' => H::fm_t1($b1095),
            'e1095' => H::fm_t1($e1095),
            'b1100' => H::fm_t1($b1100),
            'e1100' => H::fm_t1($e1100),
            'b1103' => H::fm_t1($b1103),
            'e1103' => H::fm_t1($e1103),
            'b1125' => H::fm_t1($b1125),
            'e1125' => H::fm_t1($e1125),
            'b1135' => H::fm_t1($b1135),
            'e1135' => H::fm_t1($e1135),
            'b1136' => H::fm_t1($b1136),
            'e1136' => H::fm_t1($e1136),
            'b1155' => H::fm_t1($b1155),
            'e1155' => H::fm_t1($e1155),
            'b1165' => H::fm_t1($b1165),
            'e1165' => H::fm_t1($e1165),
            'b1190' => H::fm_t1($b1190),
            'e1190' => H::fm_t1($e1190),
            'b1195' => H::fm_t1($b1195),
            'e1195' => H::fm_t1($e1195),
            'b1300' => H::fm_t1($b1300),
            'e1300' => H::fm_t1($e1300),
            'b1400' => H::fm_t1($b1400),
            'e1400' => H::fm_t1($e1400),
            'b1420' => H::fm_t1($b1420),
            'e1420' => H::fm_t1($e1420),
            'b1495' => H::fm_t1($b1495),
            'e1495' => H::fm_t1($e1495),
            'b1615' => H::fm_t1($b1615),
            'e1615' => H::fm_t1($e1615),
            'b1620' => H::fm_t1($b1620),
            'e1620' => H::fm_t1($e1620),
            'b1621' => H::fm_t1($b1621),
            'e1621' => H::fm_t1($e1621),
            'b1630' => H::fm_t1($b1630),
            'e1630' => H::fm_t1($e1630),
            'b1690' => H::fm_t1($b1690),
            'e1690' => H::fm_t1($e1690),
            'b1695' => H::fm_t1($b1695),
            'e1695' => H::fm_t1($e1695),
            'b1900' => H::fm_t1($b1900),
            'e1900' => H::fm_t1($e1900),
            'b2000' => H::fm_t1($b2000),
            'e2000' => H::fm_t1($e2000),
            'b2120' => H::fm_t1($b2120),
            'e2120' => H::fm_t1($e2120),
            'b2240' => H::fm_t1($b2240),
            'e2240' => H::fm_t1($e2240),
            'b2280' => H::fm_t1($b2280),
            'e2280' => H::fm_t1($e2280),
            'b2050' => H::fm_t1($b2050),
            'e2050' => H::fm_t1($e2050),
            'b2180' => H::fm_t1($b2180),
            'e2180' => H::fm_t1($e2180),
            'b2270' => H::fm_t1($b2270),
            'e2270' => H::fm_t1($e2270),
            'b2285' => H::fm_t1($b2285),
            'e2285' => H::fm_t1($e2285),
            'b2290' => H::fm_t1($b2290),
            'e2290' => H::fm_t1($e2290),
            'b2300' => H::fm_t1($b2300),
            'e2300' => H::fm_t1($e2300),
            'b2350' => H::fm_t1($b2350),
            'e2350' => H::fm_t1($e2350)
        );


        return $header;
    }

    public function generateReport($header)
    {



        $report = new \ZippyERP\ERP\Report('financialreportsmall.tpl');



        $html = $report->generate($header, array());

        return $html;
    }

    public function exportGNAU($header)
    {
        $common = System::getOptions("common");
        $firm = System::getOptions("firmdetail");
        $jf = ($common['juridical'] == true ? "J" : "F" ) . "1201004";

        $edrpou = (string) sprintf("%10d", $firm['edrpou']);
        //2301 0011111111 F0901004 1 00 0000045 1 03 2015 2301.xml
        $number = (string) sprintf('%07d', 1);
        $filename = $firm['gni'] . $edrpou . $jf . "100{$number}1" . date('mY', time()) . $firm['gni'] . ".xml";
        $filename = str_replace(' ', '0', $filename);

        $xml = "<?xml version=\"1.0\" encoding=\"windows-1251\" ?>
  <DECLAR xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"J0901106.xsd\">
  <DECLARHEAD>
  <TIN>{$firm['edrpou']}</TIN>
  <C_DOC>J09</C_DOC>
  <C_DOC_SUB>011</C_DOC_SUB>
  <C_DOC_VER>6</C_DOC_VER>
  <C_DOC_TYPE>0</C_DOC_TYPE>
  <C_DOC_CNT>1</C_DOC_CNT>
  <C_REG>" . substr($firm['gni'], 0, 2) . "</C_REG>
  <C_RAJ>" . substr($firm['gni'], 2, 2) . "</C_RAJ>
  <PERIOD_MONTH>12</PERIOD_MONTH>
  <PERIOD_TYPE>5</PERIOD_TYPE>
  <PERIOD_YEAR>" . $this->filter->yr->getValue() . "</PERIOD_YEAR>
  <C_STI_ORIG>{$firm['gni']}</C_STI_ORIG>
  <C_DOC_STAN>1</C_DOC_STAN>
  <LINKED_DOCS xsi:nil=\"true\" />
  <D_FILL>" . (string) date('dmY') . "</D_FILL>
  <SOFTWARE>Zippy ERP</SOFTWARE>
  </DECLARHEAD>
  <DECLARBODY>
  <HFILL>" . (string) date('dmY') . "</HFILL>
  <HNAME>{$firm['name']}<</HNAME>
  <HTIN>{$firm['inn']}</HTIN>
  <HKOATUU_S xsi:nil=\"true\" />
  <HKOATUU />
  <HKOPFG_S xsi:nil=\"true\" />
  <HKOPFG xsi:nil=\"true\" />
  <HKVED_S xsi:nil=\"true\" />
  <HKVED xsi:nil=\"true\" />
  <HKIL xsi:nil=\"true\" />
  <HLOC xsi:nil=\"true\" />
  <HTEL xsi:nil=\"true\" />
  <HPERIOD />
  <HZY>" . $this->filter->yr->getValue() . "</HZY>
  <R1005G3 xsi:nil=\"true\" />
  <R1005G4 xsi:nil=\"true\" />
  <R1010G3 xsi:nil=\"true\" />
  <R1010G4 xsi:nil=\"true\" />
  <R1011G3 xsi:nil=\"true\" />
  <R1011G4 xsi:nil=\"true\" />
  <R1012G3 xsi:nil=\"true\" />
  <R1012G4 xsi:nil=\"true\" />
  <R1020G3 xsi:nil=\"true\" />
  <R1020G4 xsi:nil=\"true\" />
  <R1030G3 xsi:nil=\"true\" />
  <R1030G4 xsi:nil=\"true\" />
  <R1090G3 xsi:nil=\"true\" />
  <R1090G4 xsi:nil=\"true\" />
  <R1095G3 xsi:nil=\"true\" />
  <R1095G4 xsi:nil=\"true\" />
  <R1100G3>1000.0</R1100G3>
  <R1100G4>2000.0</R1100G4>
  <R1103G3 xsi:nil=\"true\" />
  <R1103G4 xsi:nil=\"true\" />
  <R1110G3 xsi:nil=\"true\" />
  <R1110G4 xsi:nil=\"true\" />
  <R1125G3 xsi:nil=\"true\" />
  <R1125G4 xsi:nil=\"true\" />
  <R1135G3 xsi:nil=\"true\" />
  <R1135G4 xsi:nil=\"true\" />
  <R1136G3 xsi:nil=\"true\" />
  <R1136G4 xsi:nil=\"true\" />
  <R1155G3 xsi:nil=\"true\" />
  <R1155G4 xsi:nil=\"true\" />
  <R1160G3 xsi:nil=\"true\" />
  <R1160G4 xsi:nil=\"true\" />
  <R1165G3 xsi:nil=\"true\" />
  <R1165G4 xsi:nil=\"true\" />
  <R1170G3 xsi:nil=\"true\" />
  <R1170G4 xsi:nil=\"true\" />
  <R1190G3 xsi:nil=\"true\" />
  <R1190G4 xsi:nil=\"true\" />
  <R1195G3>1000.0</R1195G3>
  <R1195G4>2000.0</R1195G4>
  <R1200G3 xsi:nil=\"true\" />
  <R1200G4 xsi:nil=\"true\" />
  <R1300G3>1000.0</R1300G3>
  <R1300G4>2000.0</R1300G4>
  <R1400G3 xsi:nil=\"true\" />
  <R1400G4 xsi:nil=\"true\" />
  <R1410G3 xsi:nil=\"true\" />
  <R1410G4 xsi:nil=\"true\" />
  <R1415G3 xsi:nil=\"true\" />
  <R1415G4 xsi:nil=\"true\" />
  <R1420G3 xsi:nil=\"true\" />
  <R1420G4 xsi:nil=\"true\" />
  <R1425G3 xsi:nil=\"true\" />
  <R1425G4 xsi:nil=\"true\" />
  <R1495G3 xsi:nil=\"true\" />
  <R1495G4 xsi:nil=\"true\" />
  <R1595G3 xsi:nil=\"true\" />
  <R1595G4 xsi:nil=\"true\" />
  <R1600G3 xsi:nil=\"true\" />
  <R1600G4 xsi:nil=\"true\" />
  <R1610G3 xsi:nil=\"true\" />
  <R1610G4 xsi:nil=\"true\" />
  <R1615G3 xsi:nil=\"true\" />
  <R1615G4 xsi:nil=\"true\" />
  <R1620G3 xsi:nil=\"true\" />
  <R1620G4 xsi:nil=\"true\" />
  <R1621G3 xsi:nil=\"true\" />
  <R1621G4 xsi:nil=\"true\" />
  <R1625G3 xsi:nil=\"true\" />
  <R1625G4 xsi:nil=\"true\" />
  <R1630G3 xsi:nil=\"true\" />
  <R1630G4 xsi:nil=\"true\" />
  <R1665G3 xsi:nil=\"true\" />
  <R1665G4 xsi:nil=\"true\" />
  <R1690G3 xsi:nil=\"true\" />
  <R1690G4 xsi:nil=\"true\" />
  <R1695G3 xsi:nil=\"true\" />
  <R1695G4 xsi:nil=\"true\" />
  <R1700G3 xsi:nil=\"true\" />
  <R1700G4 xsi:nil=\"true\" />
  <R1900G3>1000.0</R1900G3>
  <R1900G4>2000.0</R1900G4>
  <HPERIOD1 />
  <HZY1 />
  <R2000G3 xsi:nil=\"true\" />
  <R2000G4 xsi:nil=\"true\" />
  <R2120G3 xsi:nil=\"true\" />
  <R2120G4 xsi:nil=\"true\" />
  <R2240G3 xsi:nil=\"true\" />
  <R2240G4 xsi:nil=\"true\" />
  <R2280G3 xsi:nil=\"true\" />
  <R2280G4 xsi:nil=\"true\" />
  <R2050G3 xsi:nil=\"true\" />
  <R2050G4 xsi:nil=\"true\" />
  <R2180G3 xsi:nil=\"true\" />
  <R2180G4 xsi:nil=\"true\" />
  <R2270G3 xsi:nil=\"true\" />
  <R2270G4 xsi:nil=\"true\" />
  <R2285G3 xsi:nil=\"true\" />
  <R2285G4 xsi:nil=\"true\" />
  <R2290G3 xsi:nil=\"true\" />
  <R2290G4 xsi:nil=\"true\" />
  <R2300G3 xsi:nil=\"true\" />
  <R2300G4 xsi:nil=\"true\" />
  <R2350G3 xsi:nil=\"true\" />
  <R2350G4 xsi:nil=\"true\" />
  <HBOS />
  <HBUH xsi:nil=\"true\" />
  </DECLARBODY>
  </DECLAR>";

        return $xml;
    }

}
