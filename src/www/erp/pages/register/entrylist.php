<?php

namespace ZippyERP\ERP\Pages\Register;

use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Panel;
use \Zippy\Html\Link\RedirectLink;
use \ZippyERP\ERP\Helper;
use \Zippy\Interfaces\Binding\PropertyBinding as Bund;
use \ZippyERP\ERP\Entity\Entry;
use \ZippyERP\System\Application as App;
use \ZippyERP\System\System;
use \ZippyERP\System\Session;

/**
 * Класс  страницы  журнала  проводок
 */
class EntryList extends \ZippyERP\ERP\Pages\Base
{

        public function __construct()
        {
                parent::__construct();

                $this->add(new Form('filter'))->setSubmitHandler($this, 'filterOnSubmit');
                $this->filter->add(new TextInput('from', date("Y-m-d", time() - (7 * 24 * 3600))));
                $this->filter->add(new TextInput('to', date("Y-m-d")));
                $this->filter->add(new DropDownChoice('dt', \ZippyERP\ERP\Entity\Account::findArray("acc_name", "acc_id not in (select acc_pid  from erp_account_plan)")));
                $this->filter->add(new DropDownChoice('ct', \ZippyERP\ERP\Entity\Account::findArray("acc_name", "acc_id not in (select acc_pid  from erp_account_plan)")));
                $this->add(new DataView('entrylist', new EntryDataSource($this), $this, 'entrylistOnRow'));
                $this->add(new \ZippyERP\ERP\Blocks\DocView('docview'))->setVisible(false);
                $this->add(new \Zippy\Html\DataList\Paginator("paginator", $this->entrylist));
                $this->entrylist->setPageSize(10);
                $this->entrylist->Reload();
        }

        public function filterOnSubmit($sender)
        {
                $this->docview->setVisible(false);
                $this->entrylist->Reload();
        }

        public function entrylistOnRow($row)
        {
                $item = $row->getDataItem();
                $row->add(new Label('acc_d_code', $item->acc_d_code));
                $row->add(new Label('acc_c_code', $item->acc_c_code));
                $row->add(new Label('amount', ($item->amount > 0) ? number_format($item->amount / 100.0, 2) : ""));

                $row->add(new Label('comment', $item->comment));
                $row->add(new Label('created', date('d-m-Y', $item->created)));
                $row->add(new ClickLink('show', $this, 'showOnClick'))->setValue($item->meta_desc . ' №' . $item->document_number);
        }

        public function showOnClick($sender)
        {
                $item = $sender->owner->getDataItem();
                $this->docview->setVisible(true);
                $this->docview->setDoc($item);                
                
        }

}

class EntryDataSource implements \Zippy\Interfaces\DataSource
{

        private $page;

        public function __construct($page)
        {
                $this->page = $page;
        }

        private function getWhere()
        {

                $conn = \ZCL\DB\DB::getConnect();

                $from = $this->page->filter->from->getText();
                $to = $this->page->filter->to->getText();
                $from = strtotime($from);
                $to = strtotime($to);
                $where = " date(created) >= " . $conn->DBDate($from) . " and  date(created) <= " . $conn->DBDate($to);
                $dt = $this->page->filter->dt->getValue();
                if ($dt > 0) {
                        $where .= " and acc_d =" . $dt;
                }
                $ct = $this->page->filter->ct->getValue();
                if ($ct > 0) {
                        $where .= " and acc_c =" . $ct;
                }
                return $where;
        }

        public function getItemCount()
        {
                return Entry::findCnt($this->getWhere());
        }

        public function getItems($start, $count, $sortfield = null, $asc = null)
        {
                return Entry::find($this->getWhere(), "created", "desc");
        }

        public function getItem($id)
        {
                
        }

}