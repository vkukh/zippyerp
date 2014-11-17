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
        $this->_custds = new \ZCL\DB\EntityDataSource('ZippyERP\ERP\Entity\Customer', 'amount <> 0', 'customer_name');
        $this->clistpanel->add(new DataView('clist', $this->_custds, $this, 'clistOnRow'))->Reload();
        $this->add(new Panel('doclist'))->setVisible(false);
        $this->doclist->add(new ClickLink('backtolist'))->setClickHandler($this, 'backtolistOnClick');

        $this->doclist->add(new DataView('dlist', new \Zippy\Html\DataList\ArrayDataSource($this, "_dlist"), $this, 'dlistOnRow'));
        $this->doclist->dlist->setSelectedClass('success');
        $this->add(new Panel('invoicelist'))->setVisible(false);
        $this->invoicelist->add(new ClickLink('backtolist2'))->setClickHandler($this, 'backtolistOnClick');
        $this->invoicelist->add(new DataView('ilist', new \Zippy\Html\DataList\ArrayDataSource($this, "_ilist"), $this, 'ilistOnRow'));


        $this->add(new \ZippyERP\ERP\Blocks\DocView('docview'))->setVisible(false);
    }

    public function clistfilterOnSubmit($sender)
    {
        if ($this->clistpanel->clistfilter->clistshowall->isChecked()) {
            $this->_custds->setWhere('');
        } else {
            $this->_custds->setWhere('amount <> 0');
        }
        $sort = $this->clistpanel->clistfilter->clistsort->getValue();
        if ($sort == 0)
            $this->_custds->setOrder('customer_name');
        if ($sort == 1)
            $this->_custds->setOrder('amount asc');
        if ($sort == 2)
            $this->_custds->setOrder('amount desc');


        $this->clistpanel->clist->Reload();
    }

    public function clistOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('customername', $item->customer_name));
        $row->add(new Label('credit', $item->amount > 0 ? H::fm($item->amount) : ''));
        $row->add(new Label('debet', $item->amount < 0 ? H::fm(0 - $item->amount) : '' ));
        $row->add(new ClickLink('edit'))->setClickHandler($this, 'editOnClick');
        $row->add(new ClickLink('invoice'))->setClickHandler($this, 'invoiceOnClick');
    }

    public function editOnClick($sender)
    {
        $item = $sender->getOwner()->getDataItem();
        $this->_dlist = \ZippyERP\ERP\Entity\Customer::getActivityList($item->customer_id);
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

        $row->add(new Label('amountfrom', $item->amount < 0 ? H::fm($item->amount) : ''));
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
        $item = $sender->getOwner()->getDataItem();
        $this->_ilist = Document::find('intattr1=' . $item->customer_id . " and  (meta_name='Invoice' or meta_name='PurchaseInvoice') and state <> " . Document::STATE_CLOSED);
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
