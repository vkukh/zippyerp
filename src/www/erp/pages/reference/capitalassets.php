<?php

namespace ZippyERP\ERP\Pages\Reference;

use \Zippy\Html\Form\Form;
use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\Date;
use \Zippy\Html\Form\TextArea;
use \Zippy\Html\Form\SubmitButton;
use \Zippy\Html\Form\AutocompleteTextInput;
use \Zippy\Html\Form\Button;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Form\CheckBox;
use \Zippy\Html\Panel;
use \ZippyERP\ERP\Entity\CapitalAsset;
use \ZippyERP\ERP\Entity\Item;
use \ZippyERP\ERP\Helper as H;

class CapitalAssets extends \ZippyERP\ERP\Pages\Base
{

    private $_item;
    private $_expenses = array(23 => "Производство", 91 => "Общепроизводственные затраты", 92 => "Административные затраты");

    public function __construct()
    {
        parent::__construct();

        $this->add(new Panel('itemtable'))->setVisible(true);
        $this->itemtable->add(new DataView('itemlist', new \ZCL\DB\EntityDataSource('\ZippyERP\ERP\Entity\CapitalAsset', "item_type=" . Item::ITEM_TYPE_OS), $this, 'itemlistOnRow'))->Reload();
        $this->itemtable->add(new ClickLink('addnew'))->setClickHandler($this, 'addOnClick');
        $this->add(new Form('itemdetail'))->setVisible(false);
        $this->itemdetail->add(new TextInput('edititemname'));
        $this->itemdetail->add(new TextInput('editterm'));
        $this->itemdetail->add(new TextInput('editvalue'));
        $this->itemdetail->add(new TextInput('editcancelvalue'));
        $this->itemdetail->add(new TextInput('editinventory'));
        $this->itemdetail->add(new TextInput('editnorma'));

        $this->itemdetail->add(new Date('editdatemaint'));
        $this->itemdetail->add(new TextArea('editdescription'));
        $this->itemdetail->add(new SubmitButton('save'))->setClickHandler($this, 'saveOnClick');
        $this->itemdetail->add(new Button('cancel'))->setClickHandler($this, 'cancelOnClick');

        $this->itemdetail->add(new DropDownChoice('editgroup'));
        $this->itemdetail->add(new DropDownChoice('editdepreciation'));
        $this->itemdetail->add(new DropDownChoice('edittypeos', CapitalAsset::getNAList()));
        $this->itemdetail->add(new DropDownChoice('editexpenses', $this->_expenses));
    }

    public function itemlistOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('itemname', $item->itemname));
        $row->add(new Label('inventory', $item->inventory));
        $row->add(new ClickLink('copy'))->setClickHandler($this, 'editOnClick');
        $row->add(new ClickLink('edit'))->setClickHandler($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->setClickHandler($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender)
    {
        CapitalAsset::delete($sender->owner->getDataItem()->item_id);
        $this->itemtable->itemlist->Reload();
    }

    public function editOnClick($sender)
    {
        $this->_item = $sender->owner->getDataItem();
        if (strpos($sender->id, "copy") === 0) {
            $this->_item->inventory = "";
            $this->_item->item_id = 0;
        }

        $this->itemtable->setVisible(false);
        $this->itemdetail->setVisible(true);
        $this->itemdetail->edititemname->setText($this->_item->itemname);
        $this->itemdetail->editinventory->setText($this->_item->inventory);
        $this->itemdetail->editdatemaint->setDate($this->_item->datemaint);
        $this->itemdetail->editterm->setText($this->_item->term);
        $this->itemdetail->editvalue->setText(H::fm($this->_item->value));
        $this->itemdetail->editcancelvalue->setText(H::fm($this->_item->cancelvalue));
        $this->itemdetail->edittypeos->setValue($this->_item->typeos);
        $this->itemdetail->editexpenses->setValue($this->_item->expenses);
        $this->itemdetail->editdepreciation->setValue($this->_item->depreciation);
        $this->itemdetail->editnorma->setValue($this->_item->norma);
        $this->itemdetail->editgroup->setValue($this->_item->group);
    }

    public function addOnClick($sender)
    {
        $this->itemtable->setVisible(false);
        $this->itemdetail->setVisible(true);
        // Очищаем  форму
        $this->itemdetail->clean();

        $this->_item = new CapitalAsset();
    }

    public function saveOnClick($sender)
    {


        $this->_item->itemname = $this->itemdetail->edititemname->getText();
        $this->_item->measure_id = 1; //шт
        $this->_item->item_type = Item::ITEM_TYPE_OS;
        $this->_item->depreciation = $this->itemdetail->editdepreciation->getValue();
        $this->_item->cancelvalue = 100 * $this->itemdetail->editcancelvalue->getText();
        $this->_item->value = 100 * $this->itemdetail->editvalue->getText();
        $this->_item->term = $this->itemdetail->editterm->getText();
        $this->_item->inventory = $this->itemdetail->editinventory->getText();
        $this->_item->datemaint = $this->itemdetail->editdatemaint->getDate();
        $this->_item->typeos = $this->itemdetail->edittypeos->getValue();
        $this->_item->expenses = $this->itemdetail->editexpenses->getValue();
        $this->_item->norma = $this->itemdetail->editnorma->getText();
        $this->_item->group = $this->itemdetail->editgroup->getValue();


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

    public function OnAutoItem($sender)
    {
        $text = $sender->getValue();
        return Item::findArray('itemname', "itemname like '%{$text}%' and   item_type=" . ITEM::ITEM_TYPE_STUFF);
    }

}
