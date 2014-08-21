<?php

namespace ZippyERP\ERP\Pages\Reference;

use \Zippy\Html\Form\Form;
use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\TextArea;
use \Zippy\Html\Form\SubmitButton;
use \Zippy\Html\Form\Button;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Form\CheckBox;
use \Zippy\Html\Panel;
use \ZippyERP\ERP\Entity\Contact;
use \Zippy\Html\DataList\Paginator;

class ContactList extends \ZippyERP\ERP\Pages\Base
{

    private $_contact;

    public function __construct()
    {
        parent::__construct();

        $this->add(new Panel('contacttable'))->setVisible(true);

        $this->contacttable->add(new DataView('contactlist', new \ZCL\DB\EntityDataSource('\ZippyERP\ERP\Entity\Contact'), $this, 'contactlistOnRow'));
        $this->contacttable->contactlist->setPageSize(10);
        $this->contacttable->add(new Paginator('pag', $this->contacttable->contactlist));
        $this->contacttable->contactlist->Reload();
        $this->contacttable->add(new ClickLink('add'))->setClickHandler($this, 'addOnClick');

        $this->add(new \ZippyERP\ERP\Blocks\Contact('contactdetail', $this, 'OnDetail'))->setVisible(false);

        $this->add(new Panel('content'))->setVisible(false);
        $this->content->add(new Label('contentname'));
        $this->content->add(new Label('contentnotes'));
        $this->content->add(new ClickLink('backlist'))->setClickHandler($this, 'backlistOnClick');
    }

    public function contactlistOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('firstname', $item->firstname));
        $row->add(new Label('lastname', $item->lastname));
        //$row->add(new Label('middlename', $item->middlename));
        $row->add(new Label('email', $item->email));
        $row->add(new Label('position', $item->position));
        $row->add(new Label('notes', $item->notes));
        $row->add(new ClickLink('edit'))->setClickHandler($this, 'editOnClick');
        $row->add(new ClickLink('show'))->setClickHandler($this, 'showOnClick');
        $row->add(new ClickLink('delete'))->setClickHandler($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender)
    {
        Contact::delete($sender->owner->getDataItem()->contact_id);
        $this->contacttable->contactlist->Reload();
    }

    public function editOnClick($sender)
    {
        $item = $sender->owner->getDataItem();
        $this->contacttable->setVisible(false);
        $this->contactdetail->open($item);
    }

    public function showOnClick($sender)
    {
        $this->_contact = $sender->owner->getDataItem();
        $this->contacttable->setVisible(false);
        $this->content->setVisible(true);
        $this->content->contentname->setText($this->_contact->getShortName());
        $this->content->contentnotes->setText($this->_contact->notes);
    }

    public function addOnClick($sender)
    {
        $this->contacttable->setVisible(false);
        $this->contactdetail->open();
    }

    public function backlistOnClick($sender)
    {
        $this->contacttable->setVisible(true);
        $this->content->setVisible(false);
    }

    /**
     * вызывается  блоком  редактирования
     * 
     * @param mixed true если cancel
     */
    public function OnDetail($cancel = false)
    {
        $this->contacttable->setVisible(true);
        $this->contacttable->contactlist->Reload();
    }

}
