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
    private $_ds;

    public function __construct()
    {
        parent::__construct();

        $this->add(new Form('filter'))->setSubmitHandler($this, "onFilter");
        $this->filter->add(new TextInput('search'));


        $this->add(new Panel('contacttable'))->setVisible(true);


        $this->_ds = new \ZCL\DB\EntityDataSource('\ZippyERP\ERP\Entity\Contact');
        $this->contacttable->add(new DataView('contactlist', $this->_ds, $this, 'contactlistOnRow'));
        $this->contacttable->contactlist->setPageSize(10);
        $this->contacttable->add(new Paginator('pag', $this->contacttable->contactlist));
        $this->contacttable->contactlist->setSelectedClass('success');
        $this->contacttable->contactlist->Reload();
        $this->contacttable->add(new ClickLink('addnew'))->setClickHandler($this, 'addOnClick');

        $this->add(new \ZippyERP\ERP\Blocks\Contact('contactdetail', $this, 'OnDetail'))->setVisible(false);
        $this->add(new \ZippyERP\ERP\Blocks\ContactView('contactview'))->setVisible(false);
    }

    public function contactlistOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('firstname', $item->firstname));
        $row->add(new Label('lastname', $item->lastname));
        //$row->add(new Label('middlename', $item->middlename));
        $row->add(new Label('email', $item->email));
        $row->add(new Label('type', $item->getType()));
        $row->add(new ClickLink('edit'))->setClickHandler($this, 'editOnClick');
        $row->add(new ClickLink('show'))->setClickHandler($this, 'showOnClick');
        $row->add(new ClickLink('delete'))->setClickHandler($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender)
    {
        Contact::delete($sender->owner->getDataItem()->contact_id);
        $this->contacttable->contactlist->Reload();
        $this->contactview->setVisible(false);
    }

    public function editOnClick($sender)
    {
        $item = $sender->owner->getDataItem();
        $this->contacttable->setVisible(false);
        $this->contactdetail->open($item);
    }

    public function onFilter($sender)
    {
        $search = $sender->search->getText();
        if (strlen($search) > 0) {
            $this->_ds->setWhere('lastname like ' . Contact::qstr("%{$search}%"));
        } else {
            $this->_ds->setWhere();
        }
        $this->contacttable->contactlist->Reload();
    }

    public function showOnClick($sender)
    {
        $item = $sender->owner->getDataItem();
        $this->contactview->open($item);
        $this->contactdetail->setVisible(false);
        $this->contacttable->contactlist->setSelectedRow($item->contact_id);
        $this->contacttable->contactlist->Reload();
    }

    public function addOnClick($sender)
    {
        $this->contacttable->setVisible(false);
        $this->contactview->setVisible(false);
        $this->contactdetail->open();
    }

    /**
     * вызывается  блоком  редактирования
     * 
     * @param mixed true если cancel
     */
    public function OnDetail($saved = false, $id = 0)
    {
        $this->contacttable->setVisible(true);
        $this->contacttable->contactlist->Reload();
    }

}
