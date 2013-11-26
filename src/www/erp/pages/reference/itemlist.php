<?php

namespace ZippyERP\ERP\Pages\Reference;

use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\TextArea;
use \Zippy\Html\Form\SubmitButton;
use \Zippy\Html\Form\Button;
use \Zippy\Html\Panel;
use \ZippyERP\ERP\Entity\Item;

class ItemList extends \ZippyERP\ERP\Pages\Base
{

        private $_item;

        public function __construct()
        {
                parent::__construct();

                $this->add(new Panel('itemtable'))->setVisible(true);
                $this->itemtable->add(new DataView('itemlist', new \ZCL\DB\EntityDataSource('\ZippyERP\ERP\Entity\Item'), $this, 'itemlistOnRow'))->Reload();
                $this->itemtable->add(new ClickLink('add'))->setClickHandler($this, 'addOnClick');
                $this->add(new Form('itemdetail'))->setVisible(false);
                $this->itemdetail->add(new TextInput('editname'));
                $this->itemdetail->add(new DropDownChoice('editmeasure', \ZippyERP\ERP\Helper::getMeasureList()));
                $this->itemdetail->add(new DropDownChoice('edittype', \ZippyERP\ERP\Helper::getTypeList()));
                $this->itemdetail->add(new TextArea('editdescription'));
                $this->itemdetail->add(new SubmitButton('save'))->setClickHandler($this, 'saveOnClick');
                $this->itemdetail->add(new Button('cancel'))->setClickHandler($this, 'cancelOnClick');
        }

        public function itemlistOnRow($row)
        {
                $item = $row->getDataItem();
                $row->add(new Label('itemname', $item->itemname));
                $row->add(new Label('measure', $item->measure_name));
                $row->add(new Label('typename', $item->typename));
                $row->add(new ClickLink('edit'))->setClickHandler($this, 'editOnClick');
                $row->add(new ClickLink('delete'))->setClickHandler($this, 'deleteOnClick');
        }

        public function editOnClick($sender)
        {
                $this->_item = $sender->owner->getDataItem();
                $this->itemtable->setVisible(false);
                $this->itemdetail->setVisible(true);
                $this->itemdetail->editname->setText($this->_item->itemname);
                $this->itemdetail->editdescription->setText($this->_item->description);
                $this->itemdetail->editmeasure->setValue($this->_item->measure_id);
        }

        public function deleteOnClick($sender)
        {
                Item::delete($sender->owner->getDataItem()->item_id);
                $this->itemtable->itemlist->Reload();
        }

        public function addOnClick($sender)
        {
                $this->itemtable->setVisible(false);
                $this->itemdetail->setVisible(true);
                $this->itemdetail->editname->setText('');
                $this->itemdetail->editdescription->setText('');
                //$this->itemdetail->editmeasure->setValue(1);
                $this->_item = new Item();
        }

        public function saveOnClick($sender)
        {

                $this->_item->itemname = $this->itemdetail->editname->getText();
                $this->_item->description = $this->itemdetail->editdescription->getText();
                $this->_item->measure_id = $this->itemdetail->editmeasure->getValue();
                $this->_item->item_type = $this->itemdetail->edittype->getValue();
                if ($this->_item->itemname == '') {
                        $this->setError("Введите имя");
                        return;
                }

                $this->_item->Save();
                $this->itemdetail->setVisible(false);
                $this->itemtable->setVisible(true);
                $this->itemtable->itemlist->Reload();
        }

        public function cancelOnClick($sender)
        {
                $this->itemtable->setVisible(true);
                $this->itemdetail->setVisible(false);
        }

}