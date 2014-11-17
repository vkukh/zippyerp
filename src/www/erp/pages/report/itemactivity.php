<?php

namespace ZippyERP\ERP\Pages\Report;

use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\Date;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Panel;
use \ZippyERP\ERP\Entity\Store;
use \ZippyERP\ERP\Entity\Item;
use \Zippy\Html\Link\RedirectLink;
use \ZippyERP\ERP\Helper as H;

class ItemActivity extends \ZippyERP\ERP\Pages\Base
{

    public function __construct()
    {
        parent::__construct();

        $this->add(new Form('filter'))->setSubmitHandler($this, 'OnSubmit');
        $this->filter->add(new Date('from', time() - (7 * 24 * 3600)));
        $this->filter->add(new Date('to', time()));
        $this->filter->add(new DropDownChoice('store', Store::findArray("storename", "")));
        $this->filter->add(new DropDownChoice('item', Item::findArray("itemname", "item_type <> " . Item::ITEM_TYPE_SERVICE)));

        $this->add(new Panel('detail'))->setVisible(false);
        $this->detail->add(new RedirectLink('print', "movereport"));
        $this->detail->add(new RedirectLink('pdf', "movereport"));
        $this->detail->add(new RedirectLink('word', "movereport"));
        $this->detail->add(new RedirectLink('excel', "movereport"));
        $this->detail->add(new Label('preview'));
    }

    public function OnSubmit($sender)
    {

        $html = $this->generateReport();
        $this->detail->preview->setText($html, true);
        \ZippyERP\System\Session::getSession()->printform = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head><body>" . $html . "</body></html>";

        // \ZippyERP\System\Session::getSession()->storereport = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head><body>" . $html . "</body></html>";
        $reportpage = "ZippyERP/ERP/Pages/ShowDoc";
        $this->detail->print->pagename = $reportpage;
        $this->detail->print->params = array('print', "movereport");
        $this->detail->pdf->pagename = $reportpage;
        $this->detail->pdf->params = array('pdf', "movereport");
        $this->detail->word->pagename = $reportpage;
        $this->detail->word->params = array('doc', "movereport");
        $this->detail->excel->pagename = $reportpage;
        $this->detail->excel->params = array('xls', "movereport");

        $this->detail->setVisible(true);
    }

    private function generateReport()
    {

        $store = $this->filter->store->getValue();
        $item = $this->filter->item->getValue();
        $from = $this->filter->from->getDate();
        $to = $this->filter->to->getDate();

        $header = array('datefrom' => date('d.m.Y', $from),
            'dateto' => date('d.m.Y', $to),
            "store" => Store::load($store)->storename,
            "item" => Item::load($item)->itemname,
            "measure" => Item::load($item)->measure_name
        );


        $i = 1;
        $detail = array();
        $conn = \ZCL\DB\DB::getConnect();
        $sql = "select t.* ,(select coalesce(sum(u.`quantity`),0) from erp_stock_activity_view u where u.`document_date` < t.dt and u.`stock_id` = t.`stock_id`) as  begin_quantity
                             from (select stock_id,partion, date(updated) as dt,
                             sum(case  when  quantity > 0 then quantity else 0 end ) as  obin,
                             sum(case  when  quantity < 0 then 0-quantity else 0 end ) as  obout,
                             GROUP_CONCAT(document_number) as docs
                             from `erp_stock_activity_view`
                             where  item_id ={$item}   and   store_id ={$store} and date(document_date) >= " . $conn->DBDate($from) . " and date(document_date) <= " . $conn->DBDate($to) . " 
                             group  by stock_id, partion,date(updated) ) t order  by dt   ";

        $rs = $conn->Execute($sql);

        foreach ($rs as $row) {
            $detail[] = array("no" => $i++,
                "date" => date("d.m.Y", strtotime($row['dt'])),
                "documents" => str_replace(',', '<br>', $row['docs']),
                "price" => H::fm($row['partion']),
                "in" => $row['begin_quantity'],
                "obin" => $row['obin'],
                "obout" => $row['obout'],
                "out" => $row['begin_quantity'] + $row['obin'] - $row['obout']);
        }


        $report = new \ZippyERP\ERP\Report('itemactivity.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

}
