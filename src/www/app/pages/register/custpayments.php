<?php

namespace App\Pages\Register;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\CheckBox;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Panel;
use App\Entity\Customer;
use App\Entity\Doc\Document;
use \Zippy\Html\DataList\ArrayDataSource;
use \Zippy\Html\DataList\Paginator;
use App\Helper as H;

class CustPayments extends \App\Pages\Base
{

    public $_dlist = array();
    public $_ilist = array();
    public $_custds;

    public function __construct() {
        parent::__construct();

        $this->add(new Panel('clistpanel'));
        $this->clistpanel->add(new Form('clistfilter'))->onSubmit($this, 'clistfilterOnSubmit');
        $this->clistpanel->clistfilter->add(new DropDownChoice('clistsort'));
        $this->clistpanel->clistfilter->add(new CheckBox('clistshowall'));
        $this->_custds = new CPDataSource();
        $this->clistpanel->add(new DataView('clist', $this->_custds, $this, 'clistOnRow'))->Reload();
        $this->clistpanel->add(new Paginator("pag", $this->clistpanel->clist));
        $this->add(new Panel('doclist'))->setVisible(false);
        $this->doclist->add(new ClickLink('backtolist'))->onClick($this, 'backtolistOnClick');

        $this->doclist->add(new DataView('dlist', new ArrayDataSource($this, "_dlist"), $this, 'dlistOnRow'));
        $this->doclist->add(new Paginator("pagd", $this->doclist->dlist));

        // $this->doclist->dlist->setSelectedClass('table-success');
        $this->doclist->add(new Label("custname1"));
        $this->add(new Panel('invoicelist'))->setVisible(false);
        $this->invoicelist->add(new ClickLink('backtolist2'))->onClick($this, 'backtolistOnClick');
        $this->invoicelist->add(new DataView('ilist', new ArrayDataSource($this, "_ilist"), $this, 'ilistOnRow'));
        $this->invoicelist->add(new Paginator("pagi", $this->invoicelist->ilist));
        $this->invoicelist->add(new Label("custname2"));

        $this->add(new \App\Widgets\DocView('docview'))->setVisible(false);
    }

    public function clistfilterOnSubmit($sender) {

        $this->_custds->showall = $this->clistpanel->clistfilter->clistshowall->isChecked();
        $this->_custds->sort = $this->clistpanel->clistfilter->clistsort->getValue();
        $this->clistpanel->clist->Reload();
    }

    public function clistOnRow($row) {
        $item = $row->getDataItem();
        $amount = $item->saldo;
        $row->add(new Label('customer_name', $item->customer_name));
        $row->add(new Label('debet', $amount > 0 ? H::famt($amount) : ''));
        $row->add(new Label('credit', $amount < 0 ? H::famt(0 - $amount) : ''));
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('invoice'))->onClick($this, 'invoiceOnClick');
    }

    public function editOnClick($sender) {
        $customer = $sender->getOwner()->getDataItem();
        $this->doclist->custname1->setText($customer->customer_name);
        $conn = \ZDB\DB::getConnect();
        $sql = "select  sc.document_id,sum(sc.amount) as amount,dc.meta_desc,dc.document_number,sc.document_date
                from  entrylist_view sc  join  documents_view dc on sc.document_id = dc.document_id
                where dc.customer_id = {$customer->customer_id} and (acc_code = '371' or acc_code = '681')
                group by sc.document_id,dc.meta_desc,dc.document_number,sc.document_date order  by  sc.`document_date`  desc";
        $rs = $conn->Execute($sql);
        $this->_dlist = array();
        foreach ($rs as $row) {
            $item = new \App\DataItem();
            $item->document_id = $row['document_id'];
            $item->amount = $row['amount'];
            $item->description = $row['meta_desc'];
            $item->document_number = $row['document_number'];
            $item->document_date = strtotime($row['document_date']);

            $this->_dlist[] = $item;
        }

        $this->doclist->dlist->Reload();
        $this->clistpanel->setVisible(false);
        $this->doclist->setVisible(true);
    }

    public function backtolistOnClick($sender) {
        $this->clistpanel->setVisible(true);
        $this->doclist->setVisible(false);
        $this->invoicelist->setVisible(false);
        $this->docview->setVisible(false);
    }

    public function dlistOnRow($row) {
        $item = $row->getDataItem();

        $row->add(new Label('amountfrom', $item->amount < 0 ? H::famt(0 - $item->amount) : ''));
        $row->add(new Label('amountto', $item->amount > 0 ? H::famt($item->amount) : ''));
        $row->add(new Label('ddate', date('Y-m-d', $item->document_date)));

        $row->add(new ClickLink('ddoc', $this, 'ddocOnClick'))->setValue($item->description . ' ' . $item->document_number);
    }

    public function ddocOnClick($sender) {
        $item = $sender->getOwner()->getDataItem();
        $this->doclist->dlist->setSelectedRow($sender->getOwner());
        $this->doclist->dlist->Reload();
        $this->docview->setVisible(true);
        $this->docview->setDoc(\App\Entity\Doc\Document::load($item->document_id));
    }

    public function payOnClick($sender) {
        $item = $sender->getOwner()->getDataItem();
        $item->updateStatus(Document::STATE_PAYED);
        $this->backtolistOnClick(null);
    }

    public function invoiceOnClick($sender) {
        $customer = $sender->getOwner()->getDataItem();
        $this->invoicelist->custname2->setText($customer->customer_name);

        $this->_ilist = Document::find('customer_id=' . $customer->customer_id . " and  (meta_name='Invoice' or meta_name='PurchaseInvoice') and state = " . Document::STATE_WP, 'document_date asc');
        $this->invoicelist->ilist->Reload();
        $this->clistpanel->setVisible(false);
        $this->invoicelist->setVisible(true);
    }

    public function ilistOnRow($row) {
        $item = $row->getDataItem();

        // $row->add(new Label('idoc', $item->document_number));
        $row->add(new Label('iamount', $item->amount > 0 ? H::famt($item->amount) : ''));
        $row->add(new Label('idate', date('Y-m-d', $item->headerdata['paydate'])));
        if ($item->headerdata['paydate'] < time())
            $row->idate->setAttribute('style', 'color: red;');
        $row->add(new ClickLink('idoc', $this, 'ddocOnClick'))->setValue($item->meta_desc . ' ' . $item->document_number);
        $row->add(new ClickLink('pay', $this, 'payOnClick'));
    }

}

class CPDataSource implements \Zippy\Interfaces\DataSource
{

    public $showall = false;
    public $sort = 0;

    public function __construct() {
        
    }

    public function getItemCount() {
        $conn = \ZDB\DB::getConnect();
        $sql = "select count(*) as cnt from (select  coalesce(sum(sc.amount ),0) as  saldo,sc.customer_id ,sc.customer_name
                from  entrylist_view sc   where  acc_code = '371' or acc_code = '681'
                group  by  sc.customer_id ,sc.customer_name ) t ";
        if ($this->showall == false)
            $sql = $sql . " where saldo <> 0 ";

        return $conn->getOne($sql);
    }

    public function getItems($start, $count, $sortfield = null, $asc = null) {
        $conn = \ZDB\DB::getConnect();
        $sql = "select * from (select  coalesce(sum(sc.amount ),0) as  saldo,sc.customer_id ,sc.customer_name
                from  entrylist_view sc   where  acc_code = '371' or acc_code = '681'
                group  by  sc.customer_id ,sc.customer_name ) t ";
        if ($this->showall == false)
            $sql = $sql . " where saldo <> 0 ";
        if ($this->sort == 0)
            $sql = $sql . " order by  customer_name ";
        if ($this->sort == 1)
            $sql = $sql . " order by  saldo  ";
        if ($this->sort == 2)
            $sql = $sql . " order by  saldo desc ";
        $list = array();
        $rs = $conn->Execute($sql);
        foreach ($rs as $row) {
            $customer = new Customer($row);
            $list[] = $customer;
        }

        return $list;
    }

    public function getItem($id) {
        return Customer::load($id);
    }

}
