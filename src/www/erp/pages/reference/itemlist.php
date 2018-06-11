<?php

namespace ZippyERP\ERP\Pages\Reference;

use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\TextArea;
use \Zippy\Html\Form\CheckBox;
use \Zippy\Html\Form\Button;
use \Zippy\Html\Form\SubmitButton;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Panel;
use ZippyERP\ERP\Entity\Item;

class ItemList extends \ZippyERP\ERP\Pages\Base
{

    private $_item;

    public function __construct($add = false) {
        parent::__construct();

        $this->add(new Form('filter'))->onSubmit($this, 'OnFilter');
        $this->filter->add(new TextInput('searchkey'));
        $this->filter->add(new DropDownChoice('stype', \ZippyERP\ERP\Entity\Item::getTMZList()))->setValue(Item::ITEM_TYPE_STUFF);

        $this->add(new Panel('itemtable'))->setVisible(true);
        $this->itemtable->add(new DataView('itemlist', new ItemDataSource($this), $this, 'itemlistOnRow'));
        $this->itemtable->add(new ClickLink('addnew'))->onClick($this, 'addOnClick');
        $this->itemtable->itemlist->setPageSize(25);
        $this->itemtable->add(new \Zippy\Html\DataList\Paginator('pag', $this->itemtable->itemlist));


        $this->add(new Form('itemdetail'))->setVisible(false);
        $this->itemdetail->add(new TextInput('editname'));
        $this->itemdetail->add(new TextInput('editpriceopt'));
        $this->itemdetail->add(new TextInput('editpriceret'));
        $this->itemdetail->add(new DropDownChoice('editmeasure', \ZippyERP\ERP\Helper::getMeasureList()));
        $this->itemdetail->add(new DropDownChoice('edittype', \ZippyERP\ERP\Entity\Item::getTMZList()));
        $this->itemdetail->add(new TextInput('editbarcode'));
        $this->itemdetail->add(new TextInput('editcode'));
        $this->itemdetail->add(new TextInput('edituktzed'));
        $this->itemdetail->add(new TextArea('editdescription'));
        $this->itemdetail->add(new CheckBox('editdeleted'));

        $this->itemdetail->add(new SubmitButton('save'))->onClick($this, 'OnSubmit');
        $this->itemdetail->add(new Button('cancel'))->onClick($this, 'cancelOnClick');

        if ($add == false) {
            $this->itemtable->itemlist->Reload();
        } else {
            $this->addOnClick(null);
        }
    }

    public function itemlistOnRow($row) {
        $item = $row->getDataItem();
        $row->add(new Label('itemname', $item->itemname));
        $row->add(new Label('measure', $item->measure_name));

        $row->add(new Label('code', $item->item_code));
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender) {
        $item = $sender->owner->getDataItem();
        if ($item->item_type == Item::ITEM_TYPE_STUFF) {
            //проверка на партии
            if ($item->checkDelete()) {
                Item::delete($item->item_id);
            } else {
                $this->setError("Не можна видаляти цей  товар");
                return;
            }
        }
        if ($item->item_type == Item::ITEM_TYPE_SERVICE) {
            //проверка на услуги
            $cnt = \ZippyERP\ERP\Entity\Doc\Document::findCnt("(meta_name ='ServiceAct' or meta_name='ServiceIncome') and content like'%<item_id>{$item->item_id}</item_id>%'");
            if ($cnt > 0) {
                $this->setError("Не можна видаляти цю послугу");
                return;
            }
            Item::delete($item->item_id);
        }

        $this->itemtable->itemlist->Reload();
    }

    public function editOnClick($sender) {
        $this->_item = $sender->owner->getDataItem();
        $this->itemtable->setVisible(false);
        $this->itemdetail->setVisible(true);

        $this->itemdetail->editname->setText($this->_item->itemname);
        $this->itemdetail->editpriceret->setText($this->_item->priceret);
        $this->itemdetail->editpriceopt->setText($this->_item->priceopt);
        $this->itemdetail->editdescription->setText($this->_item->description);
        $this->itemdetail->editcode->setText($this->_item->item_code);
        $this->itemdetail->edituktzed->setText($this->_item->uktzed);
        $this->itemdetail->editbarcode->setText($this->_item->barcode);
        $this->itemdetail->editmeasure->setValue($this->_item->measure_id);
        $this->itemdetail->edittype->setValue($this->_item->item_type);
        $this->itemdetail->editdeleted->setChecked($this->_item->deleted);
    }

    public function addOnClick($sender) {
        $this->itemtable->setVisible(false);
        $this->itemdetail->setVisible(true);
        // Очищаем  форму
        $this->itemdetail->clean();
        $this->_item = new Item();
    }

    public function cancelOnClick($sender) {
        $this->itemtable->setVisible(true);
        $this->itemdetail->setVisible(false);
    }

    public function OnFilter($sender) {
        $this->itemtable->itemlist->Reload();
    }

    public function OnSubmit($sender) {
        $this->itemtable->setVisible(true);
        $this->itemdetail->setVisible(false);

        $this->_item->itemname = $this->itemdetail->editname->getText();
        $this->_item->priceret = $this->itemdetail->editpriceret->getText();
        $this->_item->priceopt = $this->itemdetail->editpriceopt->getText();
        $this->_item->item_code = $this->itemdetail->editcode->getText();
        $this->_item->uktzed = $this->itemdetail->edituktzed->getText();
        $this->_item->barcode = $this->itemdetail->editbarcode->getText();
        $this->_item->description = $this->itemdetail->editdescription->getText();
        $this->_item->measure_id = $this->itemdetail->editmeasure->getValue();
        $this->_item->item_type = $this->itemdetail->edittype->getValue();
        $this->_item->deleted = $this->itemdetail->editdeleted->isChecked();
        $this->_item->Save();

        $this->itemtable->itemlist->Reload();
    }

}

class ItemDataSource implements \Zippy\Interfaces\DataSource
{

    private $page;

    public function __construct($page) {
        $this->page = $page;
    }

    private function getWhere() {

        $form = $this->page->filter;
        $where = "item_type   = " . $form->stype->getValue();
        $text = trim($form->searchkey->getText());
        if (strlen($text) > 0) {
            $where = $where . " and (itemname like " . Item::qstr('%' . $text . '%') . " or item_code like " . Item::qstr('%' . $text . '%') . " )  ";
        }
        return $where;
    }

    public function getItemCount() {
        return Item::findCnt($this->getWhere());
    }

    public function getItems($start, $count, $sortfield = null, $asc = null) {
        return Item::find($this->getWhere(), "itemname asc", $count, $start);
    }

    public function getItem($id) {
        return Item::load($id);
    }

}
