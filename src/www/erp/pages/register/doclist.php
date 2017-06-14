<?php

namespace ZippyERP\ERP\Pages\Register;

use Zippy\Html\DataList\DataView;
use Zippy\Html\DataList\Paginator;
use Zippy\Html\Form\CheckBox;
use Zippy\Html\Form\Date;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Panel;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use ZippyERP\ERP\Entity\Doc\Document;
use ZippyERP\ERP\Filter;
use ZippyERP\ERP\Helper as H;
use Zippy\WebApplication as App;
use ZippyERP\System\System;

/**
 * журнал  докуметов
 */
class DocList extends \ZippyERP\System\Pages\Base
{

    /**
     *
     * @param mixed $docid Документ  должен  быть  показан  в  просмотре
     * @return DocList
     */
    public function __construct($docid = 0)
    {
        parent::__construct();
        $filter = Filter::getFilter("doclist");
        if ($filter->to == null) {
            $filter->to = time();
            $filter->from = time() - (7 * 24 * 3600);
            $filter->page = 1;
        }
        $this->add(new Form('filter'))->onSubmit($this, 'filterOnSubmit');
        $this->filter->add(new Date('from', $filter->from));
        $this->filter->add(new Date('to', $filter->to));
        $this->filter->add(new DropDownChoice('docgroup', H::getDocGroups()));
        $this->filter->add(new CheckBox('onlymy'))->setChecked($filter->onlymy == true);
        $this->filter->add(new TextInput('searchnumber'));

        if (strlen($filter->docgroup) > 0)
            $this->filter->docgroup->setValue($filter->docgroup);

        $doclist = $this->add(new DataView('doclist', new DocDataSource(), $this, 'doclistOnRow'));
        $doclist->setSelectedClass('success');

        $this->add(new Paginator('pag', $doclist));
        $doclist->setPageSize(10);
        $filter->page = $this->doclist->setCurrentPage($filter->page);
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
        $filter->searchnumber = $this->filter->searchnumber->getText();

        $this->doclist->setCurrentPage(1);
        $this->doclist->Reload();
    }

    public function doclistOnRow($row)
    {
        $item = $row->getDataItem();
        $item = $item->cast();
        $row->add(new Label('name', $item->meta_desc));
        $row->add(new Label('number', $item->document_number));
        $row->add(new Label('date', date('d-m-Y', $item->document_date)));
        $row->add(new Label('amount', ($item->amount > 0) ? H::fm($item->amount) : ""));

        $row->add(new Label('state', Document::getStateName($item->state)));
        // $row->add(new Label('created', date('d-m-Y', $item->created)));
        $row->add(new ClickLink('show'))->onClick($this, 'showOnClick');
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('cancel'))->onClick($this, 'cancelOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
        $user = System::getUser();
        $row->delete->setVisible($user->userlogin == 'admin' || $user->user_id = $item->user_id);

        if ($item->state == Document::STATE_EXECUTED || $item->state == Document::STATE_CLOSED) {
            $row->delete->setVisible(false);
            $row->edit->setVisible(false);
            $row->cancel->setVisible(true);
        } else {
            $row->edit->setVisible(true);
            $row->cancel->setVisible(false);
        }

        //спписок документов   которые   могут  быть созданы  на  основании  текущего
        $basedon = $row->add(new Label('basedon'));
        $basedonlist = $item->getRelationBased();
        if (count($basedonlist) == 0) {
            $basedon->setVisible(false);
        } else {
            $list = "";
            foreach ($basedonlist as $doctype => $docname) {
                $list .= "<li><a href=\"/?p=ZippyERP/ERP/Pages/Doc/" . $doctype . "&arg=/0/{$item->document_id}\">{$docname}</a></li>";
            };
            $basedon = $row->add(new Label('basedlist'))->setText($list, true);
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
        $type = H::getMetaType($item->type_id);
        $class = "\\ZippyERP\\ERP\\Pages\\Doc\\" . $type['meta_name'];
        //   $item = $class::load($item->document_id);
        //запоминаем страницу пагинатора
        $filter = Filter::getFilter("doclist");
        $filter->page = $this->doclist->getCurrentPage();

        App::Redirect($class, $item->document_id);
    }

    public function deleteOnClick($sender)
    {
        $this->docview->setVisible(false);

        $doc = $sender->owner->getDataItem();
        if ($doc->checkDeleted() == false) {
            $this->setError("Документ не  может  быть  удален");
            return;
        }
        Document::delete($doc->document_id);
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

        $conn = \ZDB\DB::getConnect();
        $filter = Filter::getFilter("doclist");
        $where = " date(document_date) >= " . $conn->DBDate($filter->from) . " and  date(document_date) <= " . $conn->DBDate($filter->to);

        if (strlen($filter->docgroup) > 1) {
            $where .= " and type_id in (select meta_id from  erp_metadata where  menugroup ='{$filter->docgroup}' )";
        }
        if (strlen($filter->searchnumber) > 1) {
            $where .= " and document_number like '%{$filter->searchnumber}%' ";
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
        $docs = Document::find($this->getWhere(), "document_date desc,document_id desc", $count, $start);

        //$l = Traversable::from($docs);
        //$l = $l->where(function ($doc) {return $doc->document_id == 169; }) ;
        //$l = $l->select(function ($doc) { return $doc; })->asArray() ;
        return $docs;
    }

    public function getItem($id)
    {
        
    }

}
