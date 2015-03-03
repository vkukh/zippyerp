<?php

namespace ZippyERP\ERP\Pages\CustomPage;

use \Zippy\Html\Panel;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Form\CheckBox;
use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \ZippyERP\ERP\Helper as H;
use \ZippyERP\ERP\Entity\Doc\Document;
use \ZippyERP\ERP\Entity\Customer;

class CustPayments extends \ZippyERP\ERP\Pages\Base
{

    public $_dlist = array();
    public $_ilist = array();
    public $_custds;

    public function __construct()
    {
        parent::__construct();

        $this->add(new Panel('clistpanel'));
        $this->clistpanel->add(new Form('clistfilter'))->setSubmitHandler($this, 'clistfilterOnSubmit');
        $this->clistpanel->clistfilter->add(new DropDownChoice('clistsort'));
        $this->clistpanel->clistfilter->add(new CheckBox('clistshowall'));
        $this->_custds = new CPDataSource();
        $this->clistpanel->add(new DataView('clist', $this->_custds, $this, 'clistOnRow'))->Reload();
        $this->add(new Panel('doclist'))->setVisible(false);
        $this->doclist->add(new ClickLink('backtolist'))->setClickHandler($this, 'backtolistOnClick');

        $this->doclist->add(new DataView('dlist', new \Zippy\Html\DataList\ArrayDataSource($this, "_dlist"), $this, 'dlistOnRow'));
        $this->doclist->dlist->setSelectedClass('success');
        $this->doclist->add(new Label("custname1"));
        $this->add(new Panel('invoicelist'))->setVisible(false);
        $this->invoicelist->add(new ClickLink('backtolist2'))->setClickHandler($this, 'backtolistOnClick');
        $this->invoicelist->add(new DataView('ilist', new \Zippy\Html\DataList\ArrayDataSource($this, "_ilist"), $this, 'ilistOnRow'));
        $this->invoicelist->add(new Label("custname2"));

        $this->add(new \ZippyERP\ERP\Blocks\DocView('docview'))->setVisible(false);
    }

    public function clistfilterOnSubmit($sender)
    {

        $this->_custds->showall = $this->clistpanel->clistfilter->clistshowall->isChecked();
        $this->_custds->sort = $this->clistpanel->clistfilter->clistsort->getValue();
        $this->clistpanel->clist->Reload();
    }

    public function clistOnRow($row)
    {
        $item = $row->getDataItem();
        $amount = $item->amount;
        $row->add(new Label('customername', $item->customer_name));
        $row->add(new Label('credit', $amount > 0 ? H::fm($amount) : ''));
        $row->add(new Label('debet', $amount < 0 ? H::fm(0 - $amount) : '' ));
        $row->add(new ClickLink('edit'))->setClickHandler($this, 'editOnClick');
        $row->add(new ClickLink('invoice'))->setClickHandler($this, 'invoiceOnClick');
    }

    public function editOnClick($sender)
    {
        $customer = $sender->getOwner()->getDataItem();
        $this->doclist->custname1->setText($customer->customer_name);
        $conn = \ZCL\DB\DB::getConnect();
        $sql = "select ctag,document_id,amount,meta_desc,document_number,created from erp_account_entry_view where (dtag = {$customer->customer_id} or  ctag = {$customer->customer_id} ) and (acc_d = 36 or acc_c = 63 or acc_c = 36 or acc_d = 63)  order  by  created  desc";
        $rs = $conn->Execute($sql);
        $this->_dlist = array();
        foreach ($rs as $row) {
            $item = new \ZippyERP\ERP\DataItem();
            $item->document_id = $row['document_id'];
            $item->amount = $row['amount'];
            $item->amount = $row['ctag'] == $customer->customer_id ? 0 - $item->amount : $item->amount ;
            $item->description = $row['meta_desc'];
            $item->document_number = $row['document_number'];
            $item->document_date = strtotime($row['created']);

            $this->_dlist[] = $item;
        }        

        $this->doclist->dlist->Reload();
        $this->clistpanel->setVisible(false);
        $this->doclist->setVisible(true);
    }

    public function backtolistOnClick($sender)
    {
        $this->clistpanel->setVisible(true);
        $this->doclist->setVisible(false);
        $this->invoicelist->setVisible(false);
        $this->docview->setVisible(false);
    }

    public function dlistOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('amountfrom', $item->amount < 0 ? H::fm(0-$item->amount) : ''));
        $row->add(new Label('amountto', $item->amount > 0 ? H::fm($item->amount) : ''));
        $row->add(new Label('ddate', date('Y-m-d', $item->document_date)));
        $row->add(new ClickLink('ddoc', $this, 'ddocOnClick'))->setValue($item->document_number);
    }

    public function ddocOnClick($sender)
    {
        $item = $sender->getOwner()->getDataItem();
        $this->doclist->dlist->setSelectedRow($item->getID());
        $this->doclist->dlist->Reload();
        $this->docview->setVisible(true);
        $this->docview->setDoc(\ZippyERP\ERP\Entity\Doc\Document::load($item->document_id));
    }

    public function invoiceOnClick($sender)
    {
        $customer = $sender->getOwner()->getDataItem();
        $this->invoicelist->custname2->setText($customer->customer_name);

        $this->_ilist = Document::find('intattr1=' . $customer->customer_id . " and  (meta_name='Invoice' or meta_name='PurchaseInvoice') and state <> " . Document::STATE_CLOSED . " and state <> " . Document::STATE_EXECUTED,'document_id asc' );
        $this->invoicelist->ilist->Reload();
        $this->clistpanel->setVisible(false);
        $this->invoicelist->setVisible(true);
    }

    public function ilistOnRow($row)
    {
        $item = $row->getDataItem();

        // $row->add(new Label('idoc', $item->document_number));
        $row->add(new Label('iamount', $item->amount > 0 ? H::fm($item->amount) : ''));
        $row->add(new Label('idate', date('Y-m-d', $item->headerdata['payment_date'])));
        $row->add(new ClickLink('idoc', $this, 'ddocOnClick'))->setValue($item->meta_desc . ' ' . $item->document_number);
    }

}


class CPDataSource implements \Zippy\Interfaces\DataSource
{

    public $showall = false;
    public $sort = 0;

    public function __construct()
    {
        
    }

    public function getItemCount()
    {
        $conn = \ZCL\DB\DB::getConnect();
        $_sql = "select sum( case when ctag=c.customer_id then 0-amount else amount  end ) from erp_account_entry where (dtag = c.customer_id or  ctag = c.customer_id ) and (acc_d = 36 or acc_c = 63 or acc_c = 36 or acc_d = 63) ";
        $sql = "select count(*) from (select c.*,coalesce(($_sql),0) as  amount from erp_customer c) t  ";
        if($this->showall == false) $sql = $sql ." where amount <>0 ";
        
        $rs = $conn->GetOne($sql); 
    }

    public function getItems($start, $count, $sortfield = null, $asc = null)
    {
        $conn = \ZCL\DB\DB::getConnect();
        $_sql = "select sum( case when ctag=c.customer_id then 0-amount else amount  end ) from erp_account_entry where (dtag = c.customer_id or  ctag = c.customer_id ) and (acc_d = 36 or acc_c = 63 or acc_c = 36 or acc_d = 63) ";
        $sql = "select * from (select c.*,coalesce(($_sql),0) as  amount from erp_customer c) t  ";
        if($this->showall == false) $sql = $sql ." where amount <>0 ";
        if($this->sort == 0) $sql = $sql ." order by  customer_name ";
        if($this->sort == 1) $sql = $sql ." order by  amount  ";
        if($this->sort == 2) $sql = $sql ." order by  amount desc ";
        $list = array();
        $rs = $conn->Execute($sql); 
        foreach($rs as $row){
            $customer = new Customer($row);
            $list[] = $customer;
        }
        
        return $list;
    }

    public function getItem($id)
    {
        return Customer::load($id);
    }

}