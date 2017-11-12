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
use ZippyERP\ERP\Entity\Position;

class PositionList extends \ZippyERP\ERP\Pages\Base
{

    private $_position;

    public function __construct()
    {
        parent::__construct();

        $this->add(new Panel('positiontable'))->setVisible(true);
        $this->positiontable->add(new DataView('positionlist', new \ZCL\DB\EntityDataSource('\ZippyERP\ERP\Entity\position'), $this, 'positionlistOnRow'))->Reload();
        $this->positiontable->add(new ClickLink('addnew'))->onClick($this, 'addOnClick');
        $this->add(new Form('positiondetail'))->setVisible(false);
        $this->positiondetail->add(new TextInput('editpositionname'));
        $this->positiondetail->add(new SubmitButton('save'))->onClick($this, 'saveOnClick');
        $this->positiondetail->add(new Button('cancel'))->onClick($this, 'cancelOnClick');
    }

    public function positionlistOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('position_name', $item->position_name));
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender)
    {
        Position::delete($sender->owner->getDataItem()->position_id);
        $this->positiontable->positionlist->Reload();
    }

    public function editOnClick($sender)
    {
        $this->_position = $sender->owner->getDataItem();
        $this->positiontable->setVisible(false);
        $this->positiondetail->setVisible(true);
        $this->positiondetail->editpositionname->setText($this->_position->position_name);
    }

    public function addOnClick($sender)
    {
        $this->positiontable->setVisible(false);
        $this->positiondetail->setVisible(true);
        // Очищаем  форму
        $this->positiondetail->clean();

        $this->_position = new Position();
    }

    public function saveOnClick($sender)
    {
        $this->_position->position_name = $this->positiondetail->editpositionname->getText();
        if ($this->_position->position_name == '') {
            $this->setError("Введите имя");
            return;
        }

        $this->_position->Save();
        $this->positiondetail->setVisible(false);
        $this->positiontable->setVisible(true);
        $this->positiontable->positionlist->Reload();
    }

    public function cancelOnClick($sender)
    {
        $this->positiontable->setVisible(true);
        $this->positiondetail->setVisible(false);
    }

}
