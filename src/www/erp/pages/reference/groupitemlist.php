<?php

namespace ZippyERP\ERP\Pages\Reference;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Panel;
use ZippyERP\ERP\Entity\GroupItem;

class GroupItemList extends \ZippyERP\System\Pages\Base
{

    private $_groupitem;

    public function __construct()
    {
        parent::__construct();

        $this->add(new Panel('groupitemtable'))->setVisible(true);
        $this->groupitemtable->add(new DataView('groupitemlist', new \ZCL\DB\EntityDataSource('\ZippyERP\ERP\Entity\GroupItem'), $this, 'groupitemlistOnRow'))->Reload();
        $this->groupitemtable->add(new ClickLink('addnew'))->setClickHandler($this, 'addOnClick');
        $this->add(new Form('groupitemdetail'))->setVisible(false);
        $this->groupitemdetail->add(new TextInput('editgroupitemname'));
        $this->groupitemdetail->add(new SubmitButton('save'))->setClickHandler($this, 'saveOnClick');
        $this->groupitemdetail->add(new Button('cancel'))->setClickHandler($this, 'cancelOnClick');
    }

    public function groupitemlistOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('groupitem_name', $item->group_name));
        $row->add(new ClickLink('edit'))->setClickHandler($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->setClickHandler($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender)
    {
        GroupItem::delete($sender->owner->getDataItem()->group_id);
        $this->groupitemtable->groupitemlist->Reload();
    }

    public function editOnClick($sender)
    {
        $this->_groupitem = $sender->owner->getDataItem();
        $this->groupitemtable->setVisible(false);
        $this->groupitemdetail->setVisible(true);
        $this->groupitemdetail->editgroupitemname->setText($this->_groupitem->group_name);
    }

    public function addOnClick($sender)
    {
        $this->groupitemtable->setVisible(false);
        $this->groupitemdetail->setVisible(true);
        // Очищаем  форму
        $this->groupitemdetail->clean();

        $this->_groupitem = new GroupItem();
    }

    public function saveOnClick($sender)
    {
        $this->_groupitem->group_name = $this->groupitemdetail->editgroupitemname->getText();
        if ($this->_groupitem->group_name == '') {
            $this->setError("Введите наименование");
            return;
        }

        $this->_groupitem->Save();
        $this->groupitemdetail->setVisible(false);
        $this->groupitemtable->setVisible(true);
        $this->groupitemtable->groupitemlist->Reload();
    }

    public function cancelOnClick($sender)
    {
        $this->groupitemtable->setVisible(true);
        $this->groupitemdetail->setVisible(false);
    }

}
