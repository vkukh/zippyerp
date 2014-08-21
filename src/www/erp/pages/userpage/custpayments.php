<?php

namespace ZippyERP\ERP\Pages\UserPage;

use \Zippy\Html\Panel;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Form\CheckBox;
use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;

class CustPayments extends \ZippyERP\ERP\Pages\Base
{

    public $_dlist = array();
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
        $this->add(new \ZippyERP\ERP\Blocks\DocView('docview'))->setVisible(false);
    }

    public function clistfilterOnSubmit($sender)
    {
        
    }

    public function clistOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('customername', $item->customer_name));
        $row->add(new Label('credit', $item->amount > 0 ? number_format($item->amount / 100, 2) : ''));
        $row->add(new Label('debet', $item->amount < 0 ? number_format(0 - $item->amount / 100, 2) : '' ));
        $row->add(new ClickLink('edit'))->setClickHandler($this, 'editOnClick');
    }

    public function editOnClick($sender)
    {
        $item = $sender->getOwner()->getDataItem();
        $this->_dlist = \ZippyERP\ERP\Entity\Customer::getActivityList($item->customer_id);
        $this->doclist->dlist->Reload();
        $this->clistpanel->setVisible(false);
        $this->doclist->setVisible(docview);
    }

    public function backtolistOnClick($sender)
    {
        $this->clistpanel->setVisible(true);
        $this->doclist->setVisible(false);
        $this->docview->setVisible(false);
    }

    public function dlistOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('amountfrom', $item->amount < 0 ? number_format($item->amount / 100, 2) : ''));
        $row->add(new Label('amountto', $item->amount > 0 ? number_format($item->amount / 100, 2) : ''));
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

}
