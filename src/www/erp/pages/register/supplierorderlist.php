<?php

namespace ZippyERP\ERP\Pages\Register;

use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Form\CheckBox;
use \Zippy\Html\DataList\DataView;
use \ZCL\DB\EntityDataSource;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Panel;
use \ZippyERP\ERP\Entity\Doc\Document;
use \ZippyERP\ERP\Entity\Doc\SupplierOrder;
use \ZippyERP\System\Application as App;
use \ZippyERP\System\System;
use \ZippyERP\System\Session;
use \ZippyERP\ERP\Helper as H;
use \ZippyERP\ERP\Filter;
use \ZippyERP\ERP\Entity\Customer;

/**
 * журнал  докуметов - заказов  поставщику
 */
class SupplierOrderList extends \ZippyERP\ERP\Pages\Base
{

    /**
     *
     * @param mixed $docid  Документ  должен  быть  показан  в  просмотре
     * @return DocList
     */
    public function __construct($docid = 0)
    {
        parent::__construct();
        $filter = Filter::getFilter("SupplierOrderList");
        $this->add(new Form('filter'))->setSubmitHandler($this, 'filterOnSubmit');
        $this->filter->add(new DropDownChoice('statelist', SupplierOrder::getStatesList()));
        $this->filter->add(new DropDownChoice('supplierlist', Customer::findArray('customer_name')));
        $this->filter->add(new CheckBox('notpayed'));

        if (strlen($filter->state) > 0)
            $this->filter->statelist->setValue($filter->state);
        if (strlen($filter->supplier) > 0)
            $this->filter->supplierlist->setValue($filter->supplier);
        $this->filter->notpayed->setChecked($filter->notpayed);

        $doclist = $this->add(new DataView('doclist', new DocSODataSource(), $this, 'doclistOnRow'));
        $doclist->setSelectedClass('success');
        $doclist->Reload();
        $this->add(new \ZippyERP\ERP\Blocks\DocView('docview'))->setVisible(false);
        if ($docid > 0) {
            $this->docview->setVisible(true);
            $this->docview->setDoc(Document::load($docid));
            $this->doclist->setSelectedRow($docid);
            $doclist->Reload();
        }
        $this->add(new \Zippy\Html\DataList\Paginator('pag', $doclist));
    }

    public function doclistOnRow($row)
    {
        $item = $row->getDataItem();
        $supplier = Customer::load($item->intattr1);
        $item = $item->cast();
        $row->add(new Label('number', $item->document_number));
        $row->add(new Label('date', date('d-m-Y', $item->document_date)));
        $row->add(new Label('supplier', ($supplier) ? $supplier->customer_name : ""));
        $row->add(new Label('amount', ($item->amount > 0) ? H::fm($item->amount) : ""));
        $row->add(new Label('payment', ($item->intattr2 > 0) ? H::fm($item->intattr2) : ""));

        $row->add(new Label('state', Document::getStateName($item->state)));
        $row->add(new ClickLink('show'))->setClickHandler($this, 'showOnClick');
        $row->add(new ClickLink('edit'))->setClickHandler($this, 'editOnClick');
        //закрытый период
        if ($item->updated < strtotime("2013-01-01")) {
            $row->edit->setVisible(false);
            $row->cancel->setVisible(false);
        }
    }

    public function filterOnSubmit($sender)
    {
        $this->docview->setVisible(false);
        //запоминаем  форму   фильтра
        $filter = Filter::getFilter("SupplierOrderList");
        $filter->state = $this->filter->statelist->getValue();
        $filter->supplier = $this->filter->supplierlist->getValue();
        $filter->notpayed = $this->filter->notpayed->isChecked();
        $this->doclist->Reload();
    }

    public function editOnClick($sender)
    {
        $item = $sender->owner->getDataItem();
        $type = H::getMetaType($item->type_id);
        $class = "\\ZippyERP\\ERP\\Pages\\Doc\\" . $type['meta_name'];
        //   $item = $class::load($item->document_id);
        App::Redirect($class, $item->document_id);
    }

    public function showOnClick($sender)
    {
        $item = $sender->owner->getDataItem();
        $this->docview->setVisible(true);
        $this->docview->setDoc($item);
        $this->doclist->setSelectedRow($item->document_id);
        $this->doclist->Reload();
    }

}

/**
 *  Источник  данных  для   списка  документов
 */
class DocSODataSource implements \Zippy\Interfaces\DataSource
{

    private function getWhere()
    {

        $conn = \ZCL\DB\DB::getConnect();
        $filter = Filter::getFilter("SupplierOrderList");
        $where = " meta_name ='SupplierOrder' ";

        if ($filter->state > 0) {
            $where .= " and state =  " . $filter->state;
        }
        if ($filter->supplier > 0) {
            $where .= " and intattr1 =  " . $filter->supplier;
        }
        if ($filter->notpayed == true) {
            $where .= " and intattr2 = 0 ";
        }
        return $where;
    }

    public function getItemCount()
    {
        return Document::findCnt($this->getWhere());
    }

    public function getItems($start, $count, $sortfield = null, $asc = null)
    {
        return Document::find($this->getWhere(), "document_date", "desc");
    }

    public function getItem($id)
    {
        
    }

}
