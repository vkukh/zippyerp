<?php

namespace ZippyERP\ERP\Pages\Reference;

use Zippy\Html\DataList\DataView;
use Zippy\Html\DataList\Paginator;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Panel;
use ZippyERP\ERP\Entity\Contact;

class ContactList extends \ZippyERP\ERP\Pages\Base
{

    private $_contact;
    private $_ds;

    public function __construct($cid = 0) {
        parent::__construct();

        $this->add(new Form('filter'))->onSubmit($this, "onFilter");
        $this->filter->add(new TextInput('search'));
        $this->filter->add(new DropDownChoice('ctype', array(0 => 'Всі', 1 => 'Контрагенти', 4 => 'Клієнти', 2 => 'Співробтники', 5 => 'Держ. організаціі', 3 => 'Різні контакти'), 0));


        $this->add(new Panel('contacttable'))->setVisible(true);


        $this->_ds = new \ZCL\DB\EntityDataSource('\ZippyERP\ERP\Entity\Contact');
        $this->contacttable->add(new DataView('contactlist', $this->_ds, $this, 'contactlistOnRow'));
        $this->contacttable->contactlist->setPageSize(25);
        $this->contacttable->add(new Paginator('pag', $this->contacttable->contactlist));

        $this->contacttable->contactlist->setSelectedClass('success');
        $this->contacttable->contactlist->Reload();
        $this->contacttable->add(new ClickLink('addnew'))->onClick($this, 'addOnClick');

        $this->add(new \ZippyERP\ERP\Blocks\Contact('contactdetail', $this, 'OnDetail'))->setVisible(false);
        $this->add(new \ZippyERP\ERP\Blocks\ContactView('contactview'))->setVisible(false);
        if ($cid > 0) {
            $item = Contact::load($cid);
            $this->contactview->open($item);
            $this->contactdetail->setVisible(false);
        }
    }

    public function contactlistOnRow($row) {
        $item = $row->getDataItem();

        $row->add(new Label('firstname', $item->firstname));
        $row->add(new Label('lastname', $item->lastname));
        //$row->add(new Label('middlename', $item->middlename));
        $row->add(new Label('email', $item->email));
        $row->add(new Label('phone', $item->phone));
        $row->add(new Label('type', $item->getType()));
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('show'))->onClick($this, 'showOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender) {
        Contact::delete($sender->owner->getDataItem()->contact_id);
        $this->contacttable->contactlist->Reload();
        $this->contactview->setVisible(false);
    }

    public function editOnClick($sender) {
        $item = $sender->owner->getDataItem();
        $this->contacttable->setVisible(false);
        $this->contactdetail->open($item);
    }

    public function onFilter($sender) {
        $where = "1=1 ";

        $search = $sender->search->getText();
        $type = $sender->ctype->getValue();
        if (strlen($search) > 0) {
            $where .= ' and lastname like ' . Contact::qstr("%{$search}%");
        }
        if ($type == 1) {
            $where .= ' and customer>0';
        }
        if ($type == 2) {
            $where .= ' and employee>0';
        }
        if ($type == 3) {
            $where .= ' and employee=0 and customer=0';
        }
        if ($type == 4) {
            $where .= ' and cust_type=5 and customer>0';
        }
        if ($type == 5) {
            $where .= ' and cust_type=4 and customer>0';
        }

        $this->_ds->setWhere($where);
        $this->contacttable->contactlist->Reload();
    }

    public function showOnClick($sender) {
        $item = $sender->owner->getDataItem();
        $this->contactview->open($item);
        $this->contactdetail->setVisible(false);
        $this->contacttable->contactlist->setSelectedRow($sender->getOwner());
        $this->contacttable->contactlist->Reload();
    }

    public function addOnClick($sender) {
        $this->contacttable->setVisible(false);
        $this->contactview->setVisible(false);
        $this->contactdetail->open();
    }

    /**
     * вызывается  блоком  редактирования
     *
     * @param mixed true если cancel
     */
    public function OnDetail($saved = false, $id = 0) {
        $this->contacttable->setVisible(true);
        $this->contacttable->contactlist->Reload();
    }

}
