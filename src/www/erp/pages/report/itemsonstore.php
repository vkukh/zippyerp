<?php

namespace ZippyERP\ERP\Pages\Report;

use Zippy\Html\Form\Date;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Label;
use Zippy\Html\Link\RedirectLink;
use Zippy\Html\Panel;
use ZippyERP\ERP\Entity\Item;
use ZippyERP\ERP\Entity\Store;
use ZippyERP\ERP\Helper as H;

/**
 * Товары на складе
 */
class ItemsOnStore extends \ZippyERP\ERP\Pages\Base
{

    public function __construct() {
        parent::__construct();

        $this->add(new Form('filter'))->onSubmit($this, 'OnSubmit');
        $this->filter->add(new Date('from', time() - (7 * 24 * 3600)));

        $this->filter->add(new DropDownChoice('store', Store::findArray("storename", "")));
        $this->filter->store->selectFirst();


        $this->add(new Panel('detail'))->setVisible(false);
        $this->detail->add(new RedirectLink('print', "movereport"));
        $this->detail->add(new RedirectLink('html', "movereport"));
        $this->detail->add(new RedirectLink('word', "movereport"));
        $this->detail->add(new RedirectLink('excel', "movereport"));
        $this->detail->add(new Label('preview'));
    }

    public function OnSubmit($sender) {


        $html = $this->generateReport();
       
        \ZippyERP\System\Session::getSession()->printform = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head><body>" . $html . "</body></html>";

        // \ZippyERP\System\Session::getSession()->storereport = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head><body>" . $html . "</body></html>";
        $reportpage = "ZippyERP/ERP/Pages/ShowReport";
        $reportname = "itemonstore_" . date("Ymd");

        $this->detail->preview->setText($html, true);

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

        $storeid = $this->filter->store->getValue();

        $from = $this->filter->from->getDate();


        $header = array('datefrom' => date('d.m.Y', $from),
            "store" => Store::load($storeid)->storename
        );


        $i = 1;
        $detail = array();
        $conn = \ZDB\DB::getConnect();

        $sql = "
            select itemname,partion, sum(a.`quantity`/1000) as qty  from `erp_stock_view` s join `erp_account_subconto` a
            on s.`stock_id` = a.`stock_id`   and store_id = {$storeid}
            group by itemname,partion
            having qty <> 0  order  by  itemname,partion
        ";

        $rs = $conn->Execute($sql);

        foreach ($rs as $row) { 
            $detail[] = array(
                "item" => $row['itemname'],
                "price" => H::fm($row['partion']),
                "qty" => (int) $row['qty']
            );
        }


        $report = new \ZippyERP\ERP\Report('itemsonstore.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

}
