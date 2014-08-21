<?php

namespace ZippyERP\ERP\Pages\Register;

use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\Date;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Form\CheckBox;
use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Panel;
use \Zippy\Html\Link\RedirectLink;
use \ZippyERP\ERP\Helper;
use \ZippyERP\ERP\Filter;
use \Zippy\Interfaces\Binding\PropertyBinding as Bund;
use \ZippyERP\ERP\Entity\Doc\Document;
use \ZippyERP\System\Application as App;
use \ZippyERP\System\System;
use \ZippyERP\System\Session;

/**
 * журнал  докуметов
 */
class DocList extends \ZippyERP\ERP\Pages\Base
{

    /**
     * 
     * @param mixed $docid  Документ  должен  быть  показан  в  просмотре
     * @return DocList
     */
    public function __construct($docid = 0)
    {
        parent::__construct();
        $filter = Filter::getFilter("doclist");
        $this->add(new Form('filter'))->setSubmitHandler($this, 'filterOnSubmit');
        $this->filter->add(new Date('from', strlen($filter->from) > 0 ? $filter->from : time() - (7 * 24 * 3600)));
        $this->filter->add(new Date('to', strlen($filter->to) > 0 ? $filter->to : time()));
        $this->filter->add(new DropDownChoice('docgroup', Helper::getDocGroups()));
        $this->filter->add(new CheckBox('onlymy'))->setChecked($filter->onlymy == true);
        if (strlen($filter->docgroup) > 0)
            $this->filter->docgroup->setValue($filter->docgroup);
        $doclist = $this->add(new DataView('doclist', new DocDataSource(), $this, 'doclistOnRow'));
        $doclist->setSelectedClass('success');
        $doclist->Reload();
        $this->add(new \ZippyERP\ERP\Blocks\DocView('docview'))->setVisible(false);
        if ($docid > 0) {
            $this->docview->setVisible(true);
            $this->docview->setDoc(Document::load($docid));
            $this->doclist->setSelectedRow($docid);
            $doclist->Reload();
        }
    }

    public function filterOnSubmit($sender)
    {
        $this->docview->setVisible(false);
        //запоминаем  форму   фильтра
        $filter = Filter::getFilter("doclist");
        $filter->from = $this->filter->from->getDate();
        $filter->to = $this->filter->to->getDate(true);
        $filter->docgroup = $this->filter->docgroup->getValue();
        $filter->onlymy = $this->filter->onlymy->isChecked();

        $this->doclist->Reload();
    }

    public function doclistOnRow($row)
    {
        $item = $row->getDataItem();
        $item = $item->cast();
        $row->add(new Label('name', $item->meta_desc));
        $row->add(new Label('number', $item->document_number));
        $row->add(new Label('date', date('d-m-Y', $item->document_date)));
        $row->add(new Label('amount', ($item->amount > 0) ? number_format($item->amount / 100.0, 2) : ""));

        $row->add(new Label('state', Document::getStateName($item->state)));
        $row->add(new Label('created', date('d-m-Y', $item->created)));
        $row->add(new ClickLink('show'))->setClickHandler($this, 'showOnClick');
        $row->add(new ClickLink('edit'))->setClickHandler($this, 'editOnClick');
        $row->add(new ClickLink('cancel'))->setClickHandler($this, 'cancelOnClick');

        if ( $item->state == Document::STATE_EXECUTED)  {
             $row->edit->setVisible(false);
             $row->cancel->setVisible(true);
        } else {
             $row->edit->setVisible(true);
             $row->cancel->setVisible(false);
        }
           

        



        //закрытый период
        if ($item->updated < strtotime("2013-01-01")) {
            $row->edit->setVisible(false);
            $row->cancel->setVisible(false);

        }
    }

    //просмотр
    public function showOnClick($sender)
    {
        $item = $sender->owner->getDataItem();
        $this->docview->setVisible(true);
        $this->docview->setDoc($item);
        $this->doclist->setSelectedRow($item->document_id);
        $this->doclist->Reload();
    }

    //редактирование
    public function editOnClick($sender)
    {
        $item = $sender->owner->getDataItem();
        $type = Helper::getMetaType($item->type_id);
        $class = "\\ZippyERP\\ERP\\Pages\\Doc\\" . $type['meta_name'];
        //   $item = $class::load($item->document_id);
        App::Redirect($class, $item->document_id);
    }



    public function deleteOnClick($sender)
    {
        $this->docview->setVisible(false);

        $item = $sender->owner->getDataItem();
        Document::delete($item->document_id);
        $this->doclist->Reload();
    }



    public function cancelOnClick($sender)
    {
        $this->docview->setVisible(false);

        $item = $sender->owner->getDataItem();
        $item->updateStatus(Document::STATE_CANCELED);
        $this->doclist->Reload();
    }

}

/**
 *  Источник  данных  для   списка  документов
 */
class DocDataSource implements \Zippy\Interfaces\DataSource
{

    private function getWhere()
    {

        $conn = \ZCL\DB\DB::getConnect();
        $filter = Filter::getFilter("doclist");
        $where = " document_date >= " . $conn->DBDate($filter->from) . " and  document_date <= " . $conn->DBDate($filter->to);

        if (strlen($filter->docgroup) > 1) {
            $where .= " and type_id in (select meta_id from  erp_metadata where  menugroup ='{$filter->docgroup}' )";
        }
        if ($filter->onlymy == true) {
            $where .= " and user_id  = " . System::getUser()->user_id;
        }
        return $where;
    }

    public function getItemCount()
    {
        return Document::findCnt($this->getWhere());
    }

    public function getItems($start, $count, $sortfield = null, $asc = null)
    {
        return Document::find($this->getWhere(), "created ", "desc");
    }

    public function getItem($id)
    {
        
    }

}
