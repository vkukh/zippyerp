<?php

namespace ZippyERP\ERP\Pages\CustomPage;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Panel;
use ZippyERP\ERP\Entity\Employee;
use ZippyERP\ERP\Helper as H;

class AccountablePayments extends \ZippyERP\System\Pages\Base
{

    public $_dlist = array();
    public $_empds;

    public function __construct()
    {
        parent::__construct();

        $this->add(new Panel('alistpanel'));
        $this->_empds = new APDataSource();
        $this->alistpanel->add(new DataView('alist', $this->_empds, $this, 'alistOnRow'))->Reload();
        $this->add(new Panel('doclist'))->setVisible(false);
        $this->doclist->add(new ClickLink('backtolist'))->setClickHandler($this, 'backtolistOnClick');

        $this->doclist->add(new DataView('dlist', new \Zippy\Html\DataList\ArrayDataSource($this, "_dlist"), $this, 'dlistOnRow'));
        $this->doclist->dlist->setSelectedClass('success');
        $this->doclist->add(new Label("empname1"));

        $this->add(new \ZippyERP\ERP\Blocks\DocView('docview'))->setVisible(false);
    }

    public function alistOnRow($row)
    {
        $item = $row->getDataItem();
        $row->add(new Label('empname', $item->shortname));
        $row->add(new Label('saldo', H::fm($item->saldo)));
        $row->add(new ClickLink('edit'))->setClickHandler($this, 'editOnClick');
    }

    public function editOnClick($sender)
    {
        $employee = $sender->getOwner()->getDataItem();
        $this->doclist->empname1->setText($employee->shortname);
        $conn = \ZDB\DB::getConnect();
        $sql = "select  sc.amount , sc.document_id,sc.document_date,d.document_number
                from  erp_account_subconto sc join erp_document d  on sc.document_id = d.document_id
                where  account_id = 372 order by sc.document_date ";
        $rs = $conn->Execute($sql);
        $this->_dlist = array();
        foreach ($rs as $row) {
            $item = new \ZippyERP\ERP\DataItem();
            $item->document_id = $row['document_id'];
            $item->amount = $row['amount'];
            $item->description = $row['meta_desc'];
            $item->document_number = $row['document_number'];
            $item->document_date = strtotime($row['document_date']);

            $this->_dlist[] = $item;
        }

        $this->doclist->dlist->Reload();
        $this->alistpanel->setVisible(false);
        $this->doclist->setVisible(true);
    }

    public function backtolistOnClick($sender)
    {
        $this->alistpanel->setVisible(true);
        $this->doclist->setVisible(false);

        $this->docview->setVisible(false);
    }

    public function dlistOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('amount', H::fm($item->amount)));
        $row->add(new Label('ddate', date('Y-m-d', $item->document_date)));
        $row->add(new ClickLink('ddoc', $this, 'ddocOnClick'))->setValue($item->description . ' ' . $item->document_number);
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

class APDataSource implements \Zippy\Interfaces\DataSource
{

    public $showall = false;
    public $sort = 0;

    public function __construct()
    {

    }

    public function getItemCount()
    {
        //no pagination
    }

    public function getItems($start, $count, $sortfield = null, $asc = null)
    {
        $conn = \ZDB\DB::getConnect();
        $sql = "select coalesce(sum(sc.amount),0) as  saldo, sc.employee_id,shortname
                from  erp_account_subconto sc join erp_staff_employee_view  e on sc.employee_id = e.employee_id
                where  account_id = 372
                group  by  sc.employee_id,e.shortname
                having saldo <> 0";

        $list = array();
        $rs = $conn->Execute($sql);
        foreach ($rs as $row) {
            $employee = new Employee($row);
            $list[] = $employee;
        }

        return $list;
    }

    public function getItem($id)
    {
        return Employee::load($id);
    }

}
