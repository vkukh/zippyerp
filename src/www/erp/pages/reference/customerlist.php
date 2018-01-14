<?php

namespace ZippyERP\ERP\Pages\Reference;

use Zippy\Html\DataList\DataView;
 
use Zippy\Html\Form\Button;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Panel;
use ZippyERP\ERP\Entity\Contact;
use ZippyERP\ERP\Entity\Customer;

class CustomerList extends \ZippyERP\ERP\Pages\Base
{

    private $_customer = null;
    private $_cds; // контакты

    public function __construct()
    {
        parent::__construct();

        $this->add(new Form('filter'))->onSubmit($this, 'OnSesrch');
        $this->filter->add(new TextInput('searchkey'));
        
        
        $this->add(new Panel('customertable'))->setVisible(true);
        $this->customertable->add(new DataView('customerlist', new \ZCL\DB\EntityDataSource('\ZippyERP\ERP\Entity\Customer'), $this, 'customerlistOnRow'));
        $this->customertable->customerlist->setPageSize(25);
        $this->customertable->add(new \Zippy\Html\DataList\Paginator('pag', $this->customertable->customerlist));
        $this->customertable->customerlist->Reload();
        
        $this->customertable->add(new ClickLink('addnew'))->onClick($this, 'addOnClick');
        $this->customertable->add(new ClickLink('addf'))->onClick($this, 'addOnClick');
        $this->add(new Form('customerdetail'))->setVisible(false);
        $this->customerdetail->add(new TextInput('editcustomername'));
        $this->customerdetail->add(new TextInput('editcode'));
        $this->customerdetail->add(new TextInput('editinn'));
        $this->customerdetail->add(new TextInput('editlic'));
        $this->customerdetail->add(new TextInput('editfaddress'));
        $this->customerdetail->add(new TextInput('editladdress'));
        $this->customerdetail->add(new TextInput('editphone'));
        $this->customerdetail->add(new TextInput('editemail'));
        $this->customerdetail->add(new DropDownChoice('editbank', \ZippyERP\ERP\Entity\Bank::findArray('bank_name', '', 'bank_name')));
        $this->customerdetail->add(new DropDownChoice('editbank2', \ZippyERP\ERP\Entity\Bank::findArray('bank_name', '', 'bank_name')));
        $this->customerdetail->add(new TextInput('editbankaccount'));
        $this->customerdetail->add(new TextInput('editbankaccount2'));
        $this->customerdetail->add(new DropDownChoice('cust_type'));
        $this->customerdetail->add(new SubmitButton('save'))->onClick($this, 'saveOnClick');
        $this->customerdetail->add(new Button('cancel'))->onClick($this, 'cancelOnClick');

        $this->add(new Panel('editcontacts'))->setVisible(false);

        $this->editcontacts->add(new Label('cname'));
        $this->editcontacts->add(new ClickLink('toclist', $this, 'OnToCList'));
        $this->add(new \ZippyERP\ERP\Blocks\Contact('contactdetail', $this, 'OnContactDetail'))->setVisible(false);
        $this->add(new \ZippyERP\ERP\Blocks\ContactView('contactview'))->setVisible(false);

        $this->_cds = new \ZCL\DB\EntityDataSource('\ZippyERP\ERP\Entity\Contact', '');
        $this->editcontacts->add(new DataView('contactlist', $this->_cds, $this, 'contactlistOnRow'));
        $this->editcontacts->add(new Form('newcontactform'))->onSubmit($this, 'OnNewContactform');
        $this->editcontacts->newcontactform->add(new DropDownChoice('choicecontact',Contact::findArray("fullname", " employee = 0 and customer = 0  ", "fullname" )));
        $this->editcontacts->newcontactform->add(new ClickLink('addnewcontact'))->onClick($this, 'OnAddNewcontact');
    }
    
    public function OnSesrch($sender)
    {
        if (strlen($this->filter->searchkey->getText()) == 0) return;
        
        $this->customertable->customerlist->getDataSource()->setWhere("customer_name like  ". Customer::qstr('%' . $this->filter->searchkey->getText().'%') ) ; 
        $this->customertable->customerlist->setPageSize(6);
        $this->add(new \Zippy\Html\DataList\Paginator('pag', $this->customertable->customerlist));
         
        $this->customertable->customerlist->Reload();
    }
    
    public function customerlistOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('customername', $item->customer_name));
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('editcontactlist'))->onClick($this, 'editContactOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function editOnClick($sender)
    {
        $this->_customer = $sender->owner->getDataItem();
        $this->customertable->setVisible(false);
        $this->customerdetail->setVisible(true);
        $this->editcontacts->setVisible(false);
        $this->customerdetail->editcustomername->setText($this->_customer->customer_name);
        $this->customerdetail->editcode->setText($this->_customer->code);
        $this->customerdetail->editinn->setText($this->_customer->inn);
        $this->customerdetail->editlic->setText($this->_customer->lic);
        $this->customerdetail->editfaddress->setText($this->_customer->faddress);
        $this->customerdetail->editladdress->setText($this->_customer->laddress);
        $this->customerdetail->editbank->setValue($this->_customer->bank);
        $this->customerdetail->editbank2->setValue($this->_customer->bank2);
        $this->customerdetail->cust_type->setValue($this->_customer->cust_type);
        $this->customerdetail->editbankaccount->setText($this->_customer->bankaccount);
        $this->customerdetail->editbankaccount2->setText($this->_customer->bankaccount2);
    }

    public function editContactOnClick($sender)
    {
        $this->_customer = $sender->owner->getDataItem();
        $this->customertable->setVisible(false);
        $this->customerdetail->setVisible(false);
        $this->editcontacts->setVisible(true);
        $this->_cds->setWhere('customer=' . $this->_customer->customer_id);
        $this->editcontacts->contactlist->Reload();
        $this->editcontacts->cname->setText($this->_customer->customer_name);
    }

    public function OnToCList($sender)
    {
        $this->customertable->setVisible(true);
        $this->editcontacts->setVisible(false);
        $this->contactdetail->setVisible(false);
        $this->contactview->setVisible(false);
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
        // Очищаем  форму
        $this->customerdetail->clean();

        $this->_customer = new Customer();
        if ($sender->id == 'addf') { //физическое  лицо на основании  контакта
            $this->_customer->contact_id = -1;   // признак  что будет связыватся   с  контактом
            $this->customerdetail->setVisible(false);
            $this->editcontacts->setVisible(true);
            // $this->editcontacts->toclist->setVisible(false);
            $this->editcontacts->cname->setText('Новый контрагент');
        }
    }

    public function saveOnClick($sender)
    {

        $this->_customer->customer_name = $this->customerdetail->editcustomername->getText();
        if ($this->_customer->customer_name == '') {
            $this->setError("Введіть найменування");
            return;
        }
        $this->_customer->code = $this->customerdetail->editcode->getText();
        $this->_customer->inn = $this->customerdetail->editinn->getText();
        $this->_customer->lic = $this->customerdetail->editlic->getText();
        $this->_customer->faddress = $this->customerdetail->editfaddress->getText();
        $this->_customer->laddress = $this->customerdetail->editladdress->getText();
        $this->_customer->bank = $this->customerdetail->editbank->getValue();
        $this->_customer->bank2 = $this->customerdetail->editbank2->getValue();
        $this->_customer->cust_type = $this->customerdetail->cust_type->getValue();
        $this->_customer->bankaccount = $this->customerdetail->editbankaccount->getText();
        $this->_customer->bankaccount2 = $this->customerdetail->editbankaccount2->getText();

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

    //строка  списка  контактов
    public function contactlistOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('cfirstname', $item->firstname));
        $row->add(new Label('clastname', $item->lastname));
        //$row->add(new Label('middlename', $item->middlename));
        $row->add(new Label('cemail', $item->email));
        $row->add(new ClickLink('cedit'))->onClick($this, 'contactlisteditOnClick');
        $row->add(new ClickLink('cshow'))->onClick($this, 'contactlistshowOnClick');
        $del = $row->add(new ClickLink('cdelete'));
        $del->onClick($this, 'contactlistdeleteOnClick');
        if ($this->_customer->contact_id == $item->contact_id)
            $del->setVisible(false);
    }

    //редактирование контакта
    public function contactlisteditOnClick($sender)
    {
        $contact = $sender->owner->getDataItem();
        $this->contactdetail->open($contact);
        $this->editcontacts->setVisible(false);
        $this->contactview->setVisible(false);
    }

    //просмотр контакта
    public function contactlistshowOnClick($sender)
    {
        $contact = $sender->owner->getDataItem();
        $this->contactview->open($contact);
    }

    //удаление контакта
    public function contactlistdeleteOnClick($sender)
    {
        $contact = $sender->owner->getDataItem();
        Contact::delete($contact->contact_id);
        $this->editcontacts->contactlist->Reload();
        $this->contactview->setVisible(false);
    }

    //возврат  с  формы  редактирования   контакта
    public function OnContactDetail($saved = false, $contact_id = 0)
    {

        $this->editcontacts->setVisible(false);
        $this->contactview->setVisible(false);
        $this->editcontacts->setVisible(true);
        if ($contact_id > 0) {  // создан новый контакт
            $contact = Contact::load($contact_id);
            $newc = $this->_customer->contact_id == -1;  //создается   контрагент
            if ($newc) {
                $this->_customer->contact_id = $contact_id;
                $this->_customer->customer_name = "ЧП " . $contact->firstname;
                $this->_customer->Save();
            }
            $contact->customer_id = $this->_customer->customer_id;
            $contact->Save();
            $this->editcontacts->contactlist->Reload();
            if ($newc) {
                $this->editcontacts->setVisible(false);
                $this->customerdetail->setVisible(true);
                $this->customerdetail->editcustomername->setText($this->_customer->customer_name);
                $this->customertable->customerlist->Reload();
            }
        }
        $this->editcontacts->contactlist->Reload();
    }

    // выбран  контакт  из  списка  для  добавления  к   контрагенту
    public function OnNewContactform($sender)
    {
        $contact_id = $sender->choicecontact->getValue();
        if ($contact_id > 0) {
            $contact = Contact::load($contact_id);
            $newc  =$this->_customer->contact_id == -1;
            if ($newc) {
                //создается   контрагент
                $this->_customer->contact_id = $contact_id;
                $this->_customer->customer_name = "ЧП " . $contact->firstname;
                $this->_customer->Save();
            }

            $contact->customer_id = $this->_customer->customer_id;
            $contact->Save();
            $this->editcontacts->contactlist->Reload();
            if ($newc) {
                $this->editcontacts->setVisible(false);
                $this->customerdetail->setVisible(true);
                $this->customerdetail->editcustomername->setText($this->_customer->customer_name);
                $this->customertable->customerlist->Reload();
            }
            $sender->choicecontact->setValue(0);
        }
    }


    public function OnAddNewcontact($sender)
    {
        $this->contactdetail->open();
        $this->editcontacts->setVisible(false);
        $this->contactview->setVisible(false);
    }

}
