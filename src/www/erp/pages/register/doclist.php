<?php

namespace ZippyERP\ERP\Pages\Register;

use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\Date;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Panel;
use \Zippy\Html\Link\RedirectLink;
use \ZippyERP\ERP\Helper;
use \Zippy\Interfaces\Binding\PropertyBinding as Bund;
use \ZippyERP\ERP\Entity\Doc\Document;
use \ZippyERP\System\Application as App;
use \ZippyERP\System\System;
use \ZippyERP\System\Session;

class DocList extends \ZippyERP\ERP\Pages\Base
{

        public function __construct()
        {
                parent::__construct();

                $this->add(new Form('filter'))->setSubmitHandler($this, 'filterOnSubmit');
                $this->filter->add(new Date('from', time() - (7 * 24 * 3600)));
                $this->filter->add(new Date('to', time()));
                $this->filter->add(new DropDownChoice('docgroup', Helper::getDocGroups()));
                $this->add(new DataView('doclist', new DocDataSource($this), $this, 'doclistOnRow'))->Reload();
                $this->add(new \ZippyERP\ERP\Blocks\DocView('docview'))->setVisible(false);

        }

        public function filterOnSubmit($sender)
        {
                $this->docview->setVisible(false);
                $this->doclist->Reload();
        }

        public function doclistOnRow($row)
        {
                $item = $row->getDataItem();
                $row->add(new Label('name', $item->meta_desc));
                $row->add(new Label('number', $item->document_number));
                $row->add(new Label('date', date('d-m-Y', $item->document_date)));
                $row->add(new Label('amount', ($item->amount > 0) ? number_format($item->amount / 100.0, 2) : ""));

                $row->add(new Label('user', $item->userlogin));
                $row->add(new Label('created', date('d-m-Y', $item->created)));
                $row->add(new ClickLink('show'))->setClickHandler($this, 'showOnClick');
                $row->add(new ClickLink('edit'))->setClickHandler($this, 'editOnClick');
                $row->add(new ClickLink('save'))->setClickHandler($this, 'saveOnClick');
                $row->add(new ClickLink('delete'))->setClickHandler($this, 'deleteOnClick');
                $row->add(new ClickLink('cancel'))->setClickHandler($this, 'cancelOnClick');
                $row->edit->setVisible($item->state != Document::STATE_EXECUTED);
                $row->save->setVisible($item->state != Document::STATE_EXECUTED);
                $row->delete->setVisible($item->state != Document::STATE_EXECUTED);
                $row->cancel->setVisible($item->state == Document::STATE_EXECUTED);
        }

        public function showOnClick($sender)
        {
                $item = $sender->owner->getDataItem();
                $this->docview->setVisible(true);
                $this->docview->setDoc($item);
         }

        public function editOnClick($sender)
        {
                $item = $sender->owner->getDataItem();
                $type = Helper::getMetaType($item->type_id);
                $class = "\\ZippyERP\\ERP\\Pages\\Doc\\" . $type['meta_name'];
                //   $item = $class::load($item->document_id);
                App::Redirect($class, $item->document_id);
        }

        public function saveOnClick($sender)
        {
                $this->docview->setVisible(false);
                $doc = $sender->owner->getDataItem();
                $type = Helper::getMetaType($doc->type_id);
                $class = "\\ZippyERP\\ERP\\Entity\\Doc\\" . $type['meta_name'];
                $doc = $class::load($doc->document_id);
                $doc->Execute();   //Обновляем  склад
                $this->doclist->Reload();
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
                $item->Cancel();
                $this->doclist->Reload();
        }

        /*
          События  жизненного  цикла  страницы, раскоментировать нужное
          public function beforeRequest(){
          parent::beforeRequest();

          }
          public function afterRequest(){
          parent::afterRequest();

          }
          public function beforeRender(){
          parent::beforeRender();

          }
          public function afterRender(){
          parent::afterRender();

          }
         */
}

class DocDataSource implements \Zippy\Interfaces\DataSource
{

        private $page;

        public function __construct($page)
        {
                $this->page = $page;
        }

        private function getWhere()
        {

                $conn = \ZCL\DB\DB::getConnect();

                $from = $this->page->filter->from->getDate();
                $to = $this->page->filter->to->getDate();
                $where = " document_date >= " . $conn->DBDate($from) . " and  document_date <= " . $conn->DBDate($to);

                $group = $this->page->filter->docgroup->getValue();
                if (strlen($group) > 1) {
                        $where .= "and type_id in (select meta_id from  erp_metadata where  menugroup ='{$group}' )";
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