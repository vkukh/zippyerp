<?php

namespace ZippyERP\ERP\Pages\Register;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\CheckBox;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use ZippyERP\ERP\Entity\Customer;
use ZippyERP\ERP\Entity\Doc\CustomerOrder;
use ZippyERP\ERP\Entity\Doc\Document;
use ZippyERP\System\Filter;
use ZippyERP\ERP\Helper as H;
use Zippy\WebApplication as App;

/**
 * журнал  докуметов - заказов  клиента
 */
class CustomerOrderList extends \ZippyERP\ERP\Pages\Base
{

    /**
     *
     * @param mixed $docid Документ  должен  быть  показан  в  просмотре
     * @return DocList
     */
    public function __construct($docid = 0)
    {
        parent::__construct();
        $filter = Filter::getFilter("CustomerOrderList");
        $this->add(new Form('filter'))->onSubmit($this, 'filterOnSubmit');
        $this->filter->add(new DropDownChoice('statelist', CustomerOrder::getStatesList()));
        $this->filter->add(new DropDownChoice('customerlist', Customer::findArray('customer_name')));
        $this->filter->add(new CheckBox('notpayed'));
        if (strlen($filter->state) > 0)
            $this->filter->statelist->setValue($filter->state);
        if (strlen($filter->customer) > 0)
            $this->filter->customerlist->setValue($filter->customer);
        $this->filter->notpayed->setChecked($filter->notpayed);

        $doclist = $this->add(new DataView('doclist', new DocCODataSource(), $this, 'doclistOnRow'));
        $doclist->setSelectedClass('success');
        $doclist->Reload();
        $this->add(new \ZippyERP\ERP\Blocks\DocView('docview'))->setVisible(false);
        if ($docid > 0) {
            $this->docview->setVisible(true);
            $this->docview->setDoc(Document::load($docid));
            //$this->doclist->setSelectedRow($docid);
            $doclist->Reload();
        }
        $this->add(new \Zippy\Html\DataList\Paginator('pag', $doclist));
    }

    public function doclistOnRow($row)
    {
        $item = $row->getDataItem();
        $customer = Customer::load($item->intattr1);
        $item = $item->cast();
        $row->add(new Label('number', $item->document_number));
        $row->add(new Label('date', date('d-m-Y', $item->document_date)));
        $row->add(new Label('customer', ($customer) ? $customer->customer_name : ""));
        $row->add(new Label('amount', ($item->amount > 0) ? H::fm($item->amount) : ""));
        $row->add(new Label('payment', ($item->intattr2 > 0) ? H::fm($item->intattr2) : ""));

        $row->add(new Label('state', Document::getStateName($item->state)));
        $row->add(new ClickLink('show'))->onClick($this, 'showOnClick');
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
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
        $filter = Filter::getFilter("CustomerOrderList");
        $filter->state = $this->filter->statelist->getValue();
        $filter->customer = $this->filter->customerlist->getValue();
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
        $this->doclist->setSelectedRow($sender->getOwner());
        $this->doclist->Reload();
    }

}

/**
 *  Источник  данных  для   списка  документов
 */
class DocCODataSource implements \Zippy\Interfaces\DataSource
{

    private function getWhere()
    {

        //$conn = \ZDB\DB::getConnect();
        $filter = Filter::getFilter("CustomerOrderList");
        $where = " meta_name ='CustomerOrder' ";

        if ($filter->state > 0) {
            $where .= " and state =  " . $filter->state;
        }
        if ($filter->customer > 0) {
            $where .= " and intattr1 =  " . $filter->customer;
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
        return Document::find($this->getWhere(), "document_date " . $asc, $count, $start);
    }

    public function getItem($id)
    {
        
    }

}
