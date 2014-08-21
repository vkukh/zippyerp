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


        $this->add(new \ZippyERP\ERP\Blocks\Item('itemdetail', $this, 'OnDetail'))->setVisible(false);
    }

    public function itemlistOnRow($row)
    {
        $item = $row->getDataItem();
        $row->add(new Label('itemname', $item->itemname));
        $row->add(new Label('measure', $item->measure_name));
        $row->add(new Label('typename', $item->typename));
        $row->add(new Label('group_name', $item->group_name));
        $row->add(new ClickLink('edit'))->setClickHandler($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->setClickHandler($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender)
    {
        Item::delete($sender->owner->getDataItem()->item_id);
        $this->itemtable->itemlist->Reload();
    }

    public function editOnClick($sender)
    {
        $item = $sender->owner->getDataItem();
        $this->itemtable->setVisible(false);
        $this->itemdetail->open($item);
    }

    public function addOnClick($sender)
    {
        $this->itemtable->setVisible(false);
        $this->itemdetail->open();
    }

    /**
     * вызывается  блоком  редактирования
     * 
     * @param mixed true если cancel
     */
    public function OnDetail($cancel = false)
    {
        $this->itemtable->setVisible(true);
        $this->itemtable->itemlist->Reload();
    }

}
