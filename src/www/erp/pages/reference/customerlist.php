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
use \Zippy\Html\Panel;
use \ZippyERP\ERP\Entity\Customer;

class CustomerList extends \ZippyERP\ERP\Pages\Base
{

        private $_customer;

        public function __construct()
        {
                parent::__construct();

                $this->add(new Panel('customertable'))->setVisible(true);
                $this->customertable->add(new DataView('customerlist', new \ZCL\DB\EntityDataSource('\ZippyERP\ERP\Entity\Customer'), $this, 'customerlistOnRow'))->Reload();
                $this->customertable->add(new ClickLink('add'))->setClickHandler($this, 'addOnClick');
                $this->add(new Form('customerdetail'))->setVisible(false);
                $this->customerdetail->add(new TextInput('editcustomername'));
                $this->customerdetail->add(new TextArea('editaddress'));
                $this->customerdetail->add(new SubmitButton('save'))->setClickHandler($this, 'saveOnClick');
                $this->customerdetail->add(new Button('cancel'))->setClickHandler($this, 'cancelOnClick');
        }

        public function customerlistOnRow($row)
        {
                $item = $row->getDataItem();

                $row->add(new Label('customername', $item->customer_name));
                $row->add(new ClickLink('edit'))->setClickHandler($this, 'editOnClick');
                $row->add(new ClickLink('delete'))->setClickHandler($this, 'deleteOnClick');
        }

        public function editOnClick($sender)
        {
                $this->_customer = $sender->owner->getDataItem();
                $this->customertable->setVisible(false);
                $this->customerdetail->setVisible(true);
                $this->customerdetail->editcustomername->setText($this->_customer->customer_name);
                $this->customerdetail->editaddress->setText($this->_customer->address);
        }

        public function deleteOnClick($sender)
        {
                Customer::delete($sender->owner->getDataItem()->customer_id);
                $this->customertable->customerlist->Reload();
        }

        public function addOnClick($sender)
        {
                $this->customertable->setVisible(false);
                $this->customerdetail->setVisible(true);
                $this->customerdetail->editcustomername->setText('');
                $this->customerdetail->editaddress->setText('');
                $this->_customer = new Customer();
        }

        public function saveOnClick($sender)
        {

                $this->_customer->customer_name = $this->customerdetail->editcustomername->getText();
                $this->_customer->address = $this->customerdetail->editaddress->getText();
                if ($this->_customer->customer_name == '') {
                        $this->setError("Введите имя");
                        return;
                }

                $this->_customer->Save();
                $this->customerdetail->setVisible(false);
                $this->customertable->setVisible(true);
                $this->customertable->customerlist->Reload();
        }

        public function cancelOnClick($sender)
        {
                $this->customertable->setVisible(true);
                $this->customerdetail->setVisible(false);
        }

}