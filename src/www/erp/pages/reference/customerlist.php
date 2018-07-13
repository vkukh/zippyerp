<?php

namespace ZippyERP\ERP\Pages\Reference;

use Zippy\Html\DataList\DataView;
use Zippy\Html\DataList\ArrayDataSource;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Form\TextArea;
use Zippy\Html\Form\AutocompleteTextInput;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Panel;
use ZippyERP\ERP\Helper;
use ZippyERP\ERP\Entity\Customer;
use Zippy\Binding\PropertyBinding as Bind;
use ZippyERP\System\System;
use Zippy\Html\Link\BookmarkableLink;

class CustomerList extends \ZippyERP\ERP\Pages\Base
{

    private $_customer = null;
    private $_cds; // контакты
    public $_fileslist = array();
    public $_msglist = array();
    public $_eventlist = array();

    public function __construct() {
        parent::__construct();

        $this->add(new Form('filter'))->onSubmit($this, 'OnSearch');
        $this->filter->add(new TextInput('searchkey'));


        $this->add(new Panel('customertable'))->setVisible(true);
        $this->customertable->add(new DataView('customerlist', new \ZCL\DB\EntityDataSource('\ZippyERP\ERP\Entity\Customer'), $this, 'customerlistOnRow'));
        $this->customertable->customerlist->setPageSize(25);
        $this->customertable->add(new \Zippy\Html\DataList\Paginator('pag', $this->customertable->customerlist));
        $this->customertable->customerlist->setSelectedClass('table-success');
        $this->customertable->customerlist->Reload();

        $this->customertable->add(new ClickLink('addnew'))->onClick($this, 'addOnClick');
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
        $this->customerdetail->add(new TextInput('editbankaccount'));
        $this->customerdetail->add(new TextInput('discount'));
        $this->customerdetail->add(new TextArea('editcomment'));
        $this->customerdetail->add(new DropDownChoice('cust_type', Customer::getTypeList()))->onChange($this, 'OnType');
        $this->customerdetail->add(new SubmitButton('save'))->onClick($this, 'saveOnClick');
        $this->customerdetail->add(new Button('cancel'))->onClick($this, 'cancelOnClick');

        $this->add(new Panel('contentview'))->setVisible(false);
        $this->contentview->add(new Form('addfileform'))->onSubmit($this, 'OnFileSubmit');
        $this->contentview->addfileform->add(new \Zippy\Html\Form\File('addfile'));
        $this->contentview->addfileform->add(new TextInput('adddescfile'));
        $this->contentview->add(new DataView('dw_files', new ArrayDataSource(new Bind($this, '_fileslist')), $this, 'fileListOnRow'));

        $this->contentview->add(new Form('addmsgform'))->onSubmit($this, 'OnMsgSubmit');
        $this->contentview->addmsgform->add(new TextArea('addmsg'));
        $this->contentview->add(new DataView('dw_msglist', new ArrayDataSource(new Bind($this, '_msglist')), $this, 'msgListOnRow'));

        $this->contentview->add(new Form('addeventform'))->onSubmit($this, 'OnEventSubmit');
        $this->contentview->addeventform->add(new \ZCL\BT\DateTimePicker('addeventdate', time()));
        $this->contentview->addeventform->add(new TextInput('addeventtitle'));
        $this->contentview->addeventform->add(new TextArea('addeventdesc'));
        $this->contentview->addeventform->add(new DropDownChoice('addeventnotify', array(1 => "1 годину", 2 => "2 години", 4 => "4 години", 8 => "8 годин", 16 => "16 годин", 24 => "24 години"), 0));
        $this->contentview->add(new DataView('dw_eventlist', new ArrayDataSource(new Bind($this, '_eventlist')), $this, 'eventListOnRow'));
        $this->contentview->dw_eventlist->setPageSize(10);
        $this->contentview->add(new \Zippy\Html\DataList\Paginator('eventpag', $this->contentview->dw_eventlist));
    }

    public function OnSearch($sender) {
        $search = trim($this->filter->searchkey->getText());
        $where = "";

        if (strlen($search) > 0) {
            $search = Customer::qstr('%' . $search . '%');
            $where = " (customer_name like  {$search} or phone like {$search}    )";
        }


        $this->customertable->customerlist->getDataSource()->setWhere($where);

        $this->customertable->customerlist->Reload();
        $this->contentview->setVisible(false);
    }

    public function customerlistOnRow($row) {
        $item = $row->getDataItem();

        $row->add(new Label('customername', $item->customer_name));
        $row->add(new Label('customerphone', $item->phone));
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('contentlist'))->onClick($this, 'editContentOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function editOnClick($sender) {
        $this->_customer = $sender->owner->getDataItem();
        $this->customertable->setVisible(false);
        $this->customerdetail->setVisible(true);
        $this->contentview->setVisible(false);
        $this->updateEditElements();

        $this->customerdetail->editcustomername->setText($this->_customer->customer_name);
        $this->customerdetail->editcode->setText($this->_customer->code);
        $this->customerdetail->editinn->setText($this->_customer->inn);
        $this->customerdetail->editlic->setText($this->_customer->lic);
        $this->customerdetail->editphone->setText($this->_customer->phone);
        $this->customerdetail->editemail->setText($this->_customer->email);
        $this->customerdetail->editfaddress->setText($this->_customer->faddress);
        $this->customerdetail->editladdress->setText($this->_customer->laddress);
        $this->customerdetail->editbank->setValue($this->_customer->bank);
        $this->customerdetail->cust_type->setValue($this->_customer->cust_type);
        $this->customerdetail->editbankaccount->setText($this->_customer->bankaccount);
        $this->customerdetail->discount->setText($this->_customer->discount);
        $this->customerdetail->editcomment->setText($this->_customer->comment);
    }

    public function OnType($sender) {
        $this->_customer->cust_type = $sender->getValue();
        $this->updateEditElements();
    }

    //виставляет видимость елементов в зависимости 

    public function updateEditElements() {



        if ($this->_customer->cust_type == Customer::TYPE_CLIENT || $this->_customer->cust_type == Customer::TYPE_OTHER) {
            $this->customerdetail->editcode->setVisible(false);
            $this->customerdetail->editinn->setVisible(false);
            $this->customerdetail->editlic->setVisible(false);


            $this->customerdetail->editladdress->setVisible(false);
            $this->customerdetail->editbank->setVisible(false);

            $this->customerdetail->editbankaccount->setVisible(false);
        } else {
            $this->customerdetail->editcode->setVisible(true);
            $this->customerdetail->editinn->setVisible(true);
            $this->customerdetail->editlic->setVisible(true);


            $this->customerdetail->editladdress->setVisible(true);
            $this->customerdetail->editbank->setVisible(true);

            $this->customerdetail->editbankaccount->setVisible(true);
        }
    }

    public function deleteOnClick($sender) {


        if (false == Customer::delete($sender->owner->getDataItem()->customer_id)) {
            $this->setError("Не можна видаляти контрагента");
            return;
        }
        ;

        $this->customertable->customerlist->Reload();
    }

    public function addOnClick($sender) {
        $this->customertable->setVisible(false);
        $this->customerdetail->setVisible(true);
        // Очищаем  форму
        $this->customerdetail->clean();
        $this->_customer = new Customer();
        $this->updateEditElements();
        $this->contentview->setVisible(false);
    }

    public function saveOnClick($sender) {

        $this->_customer->customer_name = $this->customerdetail->editcustomername->getText();
        if ($this->_customer->customer_name == '') {
            $this->setError("Введіть найменування");
            return;
        }
        $this->_customer->code = $this->customerdetail->editcode->getText();
        $this->_customer->inn = $this->customerdetail->editinn->getText();
        $this->_customer->lic = $this->customerdetail->editlic->getText();
        $this->_customer->phone = $this->customerdetail->editphone->getText();
        $this->_customer->email = $this->customerdetail->editemail->getText();
        $this->_customer->faddress = $this->customerdetail->editfaddress->getText();
        $this->_customer->laddress = $this->customerdetail->editladdress->getText();
        $this->_customer->bank = $this->customerdetail->editbank->getValue();
        $this->_customer->cust_type = $this->customerdetail->cust_type->getValue();
        $this->_customer->bankaccount = $this->customerdetail->editbankaccount->getText();
        $this->_customer->discount = $this->customerdetail->discount->getText();
        $this->_customer->comment = $this->customerdetail->editcomment->getText();

        $this->_customer->Save();
        $this->customerdetail->setVisible(false);
        $this->customertable->setVisible(true);
        $this->customertable->customerlist->Reload();
    }

    public function cancelOnClick($sender) {
        $this->customertable->setVisible(true);
        $this->customerdetail->setVisible(false);
    }

    //просмотр контакта
    public function editContentOnClick($sender) {
        $this->_customer = $sender->owner->getDataItem();
        $this->customerdetail->setVisible(false);
        $this->contentview->setVisible(true);
        $this->customertable->customerlist->setSelectedRow($sender->owner);
        $this->customertable->customerlist->Reload();
        $this->updateFiles();
        $this->updateMessages();
        $this->updateEvents();
    }

    //контент
    public function OnFileSubmit($sender) {

        $file = $this->contentview->addfileform->addfile->getFile();
        if ($file['size'] > 10000000) {
            $this->getOwnerPage()->setError("Файл більше 10М !");
            return;
        }

        Helper::addFile($file, $this->_customer->customer_id, $this->contentview->addfileform->adddescfile->getText(), \ZippyERP\ERP\Consts::FILE_ITEM_TYPE_CONTACT);
        $this->contentview->addfileform->adddescfile->setText('');
        $this->updateFiles();
        $this->goAnkor('contentviewlink');
    }

    // обновление  списка  прикрепленных файлов
    private function updateFiles() {
        $this->_fileslist = Helper::getFileList($this->_customer->customer_id, \ZippyERP\ERP\Consts::FILE_ITEM_TYPE_CONTACT);
        $this->contentview->dw_files->Reload();
    }

    //вывод строки  прикрепленного файла
    public function filelistOnRow($row) {
        $item = $row->getDataItem();

        $file = $row->add(new \Zippy\Html\Link\BookmarkableLink("filename", _BASEURL . '?p=ZippyERP/ERP/Pages/LoadFile&arg=' . $item->file_id));
        $file->setValue($item->filename);
        $file->setAttribute('title', $item->description);

        $row->add(new ClickLink('delfile'))->onClick($this, 'deleteFileOnClick');
    }

    //удаление прикрепленного файла
    public function deleteFileOnClick($sender) {
        $file = $sender->owner->getDataItem();
        Helper::deleteFile($file->file_id);
        $this->updateFiles();
    }

    /**
     * добавление коментария
     *
     * @param mixed $sender
     */
    public function OnMsgSubmit($sender) {
        $msg = new \ZippyERP\ERP\Entity\Message();
        $msg->message = $this->contentview->addmsgform->addmsg->getText();
        $msg->created = time();
        $msg->user_id = System::getUser()->user_id;
        $msg->item_id = $this->_customer->customer_id;
        $msg->item_type = \ZippyERP\ERP\Consts::MSG_ITEM_TYPE_CONTACT;
        if (strlen($msg->message) == 0)
            return;
        $msg->save();

        $this->contentview->addmsgform->addmsg->setText('');
        $this->updateMessages();
        $this->goAnkor('contentviewlink');
    }

    //список   комментариев
    private function updateMessages() {
        $this->_msglist = \ZippyERP\ERP\Entity\Message::find('item_type =4 and item_id=' . $this->_customer->customer_id);
        $this->contentview->dw_msglist->Reload();
    }

    //вывод строки  коментария
    public function msgListOnRow($row) {
        $item = $row->getDataItem();

        $row->add(new Label("msgdata", $item->message));
        $row->add(new Label("msgdate", date("Y-m-d H:i", $item->created)));
        $row->add(new Label("msguser", $item->userlogin));

        $row->add(new ClickLink('delmsg'))->onClick($this, 'deleteMsgOnClick');
    }

    //удаление коментария
    public function deleteMsgOnClick($sender) {
        $msg = $sender->owner->getDataItem();
        \ZippyERP\ERP\Entity\Message::delete($msg->message_id);
        $this->updateMessages();
    }

    public function OnEventSubmit($sender) {
        $event = new \ZippyERP\ERP\Entity\Event();
        $event->title = $this->contentview->addeventform->addeventtitle->getText();
        $event->description = $this->contentview->addeventform->addeventdesc->getText();
        $event->eventdate = $this->contentview->addeventform->addeventdate->getDate();
        $event->user_id = System::getUser()->user_id;
        $event->customer_id = $this->_customer->customer_id;

        if (strlen($event->title) == 0)
            return;
        $event->save();

        $nt = $this->contentview->addeventform->addeventnotify->getValue();
        if ($nt > 0) {

            $n = new \ZippyERP\System\Notify();
            $n->user_id = System::getUser()->user_id;
            $n->dateshow = $event->eventdate - ($nt * 3600);
            $n->message = "<b>" . $event->title . "</b>" . "<br>" . $event->description;
            $n->message .= "<br><br><b> Контакт: </b> {$this->_customer->customer_name} &nbsp;&nbsp; {$this->_customer->phone} ";
            $n->save();
        }
        $this->contentview->addeventform->clean();
        $this->updateEvents();
        $this->goAnkor('contentviewlink');
    }

    //список   событий
    private function updateEvents() {
        $this->_eventlist = \ZippyERP\ERP\Entity\Event::find('  customer_id=' . $this->_customer->customer_id);
        $this->contentview->dw_eventlist->Reload();
    }

    //вывод строки  коментария
    public function eventListOnRow($row) {
        $event = $row->getDataItem();


        $row->add(new BookmarkableLink('eventtitle', '#eventdesc_' . $row->getNumber()))->setValue($event->title);

        $row->add(new Label("eventdesc"))->setText($event->description);
        $row->add(new Label("eventdate", date("Y-m-d H:i", $event->eventdate)));

        $row->add(new ClickLink('delevent'))->onClick($this, 'deleteEventOnClick');
    }

    //удаление коментария
    public function deleteEventOnClick($sender) {
        $event = $sender->owner->getDataItem();
        \ZippyERP\ERP\Entity\Event::delete($event->event_id);
        $this->updateEvents();
    }

}
