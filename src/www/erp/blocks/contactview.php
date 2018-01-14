<?php

namespace ZippyERP\ERP\Blocks;

use Zippy\Binding\PropertyBinding as Bind;
use Zippy\Html\DataList\ArrayDataSource;
use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\TextArea;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Link\BookmarkableLink;
use ZippyERP\ERP\Helper;
use ZippyERP\System\System;

/**
 * Виджет для  просмотра
 */
class ContactView extends \Zippy\Html\PageFragment
{

    private $_item;
    public $_fileslist = array();
    public $_msglist = array();
    public $_eventlist = array();

    /**
     *
     * @param mixed $id id компонента
     */
    public function __construct($id)
    {
        parent::__construct($id);

        $this->add(new Label('contentname'));
        $this->add(new Label('contentdescription'));

        $this->add(new Form('addfileform'))->onSubmit($this, 'OnFileSubmit');
        $this->addfileform->add(new \Zippy\Html\Form\File('addfile'));
        $this->addfileform->add(new TextInput('adddescfile'));
        $this->add(new DataView('dw_files', new ArrayDataSource(new Bind($this, '_fileslist')), $this, 'fileListOnRow'));
        
        $this->add(new Form('addmsgform'))->onSubmit($this, 'OnMsgSubmit');
        $this->addmsgform->add(new TextArea('addmsg'));
        $this->add(new DataView('dw_msglist', new ArrayDataSource(new Bind($this, '_msglist')), $this, 'msgListOnRow'));
        
        $this->add(new Form('addeventform'))->onSubmit($this, 'OnEventSubmit');
        $this->addeventform->add(new \ZCL\BT\DateTimePicker('addeventdate',time()));
        $this->addeventform->add(new TextInput('addeventtitle'));
        $this->addeventform->add(new TextArea('addeventdesc'));
        $this->addeventform->add(new DropDownChoice('addeventnotify',array(1=>"1 годину",2=>"2 години",4=>"4 години",8=>"8 годин",16=>"16 годин",24=>"24 години"),0));
        $this->add(new DataView('dw_eventlist', new ArrayDataSource(new Bind($this, '_eventlist')), $this, 'eventListOnRow'));
        $this->dw_eventlist->setPageSize(10);
        $this->add(new \Zippy\Html\DataList\Paginator('pag', $this->dw_eventlist));
         
        
        
    }

    /**
     * передает  данные для  редактирования
     *
     * @param mixed $item
     */
    public function open(\ZippyERP\ERP\Entity\Contact $item)
    {

        $this->_item = $item;
        $this->contentname->setText($item->lastname .' '. $item->firstname);
        $this->contentdescription->setText($item->description);

        $this->setVisible(true);
        $this->updateFiles();
        $this->updateMessages();
        $this->updateEvents();
    }

    /**
     * добавление прикрепленного файла
     *
     * @param mixed $sender
     */
    public function OnFileSubmit($sender)
    {

        $file = $this->addfileform->addfile->getFile();
        if ($file['size'] > 10000000) {
            $this->getOwnerPage()->setError("Файл більше 10М !");
            return;
        }

        Helper::addFile($file, $this->_item->contact_id, $this->addfileform->adddescfile->getText(), \ZippyERP\ERP\Consts::FILE_ITEM_TYPE_CONTACT);
        $this->addfileform->adddescfile->setText('');
        $this->updateFiles();
    }

    // обновление  списка  прикрепленных файлов
    private function updateFiles()
    {
        $this->_fileslist = Helper::getFileList($this->_item->contact_id, \ZippyERP\ERP\Consts::FILE_ITEM_TYPE_CONTACT);
        $this->dw_files->Reload();
    }

    //вывод строки  прикрепленного файла
    public function filelistOnRow($row)
    {
        $item = $row->getDataItem();

        $file = $row->add(new \Zippy\Html\Link\BookmarkableLink("filename", _BASEURL . '?p=ZippyERP/ERP/Pages/LoadFile&arg=' . $item->file_id));
        $file->setValue($item->filename);
        $file->setAttribute('title', $item->description);

        $row->add(new ClickLink('delfile'))->onClick($this, 'deleteFileOnClick');
    }

    //удаление прикрепленного файла
    public function deleteFileOnClick($sender)
    {
        $file = $sender->owner->getDataItem();
        Helper::deleteFile($file->file_id);
        $this->updateFiles();
    }

    /**
     * добавление коментария
     *
     * @param mixed $sender
     */
    public function OnMsgSubmit($sender)
    {
        $msg = new \ZippyERP\ERP\Entity\Message();
        $msg->message = $this->addmsgform->addmsg->getText();
        $msg->created = time();
        $msg->user_id = System::getUser()->user_id;
        $msg->item_id = $this->_item->contact_id;
        $msg->item_type = \ZippyERP\ERP\Consts::MSG_ITEM_TYPE_CONTACT;
        if (strlen($msg->message) == 0)
            return;
        $msg->save();

        $this->addmsgform->addmsg->setText('');
        $this->updateMessages();
    }

    //список   комментариев
    private function updateMessages()
    {
        $this->_msglist = \ZippyERP\ERP\Entity\Message::find('item_type =4 and item_id=' . $this->_item->contact_id);
        $this->dw_msglist->Reload();
    }


    //вывод строки  коментария
    public function msgListOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label("msgdata", $item->message));
        $row->add(new Label("msgdate", date("Y-m-d H:i", $item->created)));
        $row->add(new Label("msguser", $item->userlogin));

        $row->add(new ClickLink('delmsg'))->onClick($this, 'deleteMsgOnClick');
    }

    //удаление коментария
    public function deleteMsgOnClick($sender)
    {
        $msg = $sender->owner->getDataItem();
        \ZippyERP\ERP\Entity\Message::delete($msg->message_id);
        $this->updateMessages();
    }


    public function OnEventSubmit($sender)
    {
        $event = new \ZippyERP\ERP\Entity\Event();
        $event->title = $this->addeventform->addeventtitle->getText();
        $event->description = $this->addeventform->addeventdesc->getText();
        $event->eventdate = $this->addeventform->addeventdate->getDate();
        $event->user_id = System::getUser()->user_id;
        $event->contact_id = $this->_item->contact_id;
        
        if (strlen($event->title) == 0)
            return;
        $event->save();
        
        $nt = $this->addeventform->addeventnotify->getValue();
        if($nt  >0){
            
            $n = new \ZippyERP\System\Notify();
            $n->user_id =  System::getUser()->user_id;
            $n->dateshow = $event->eventdate - ($nt * 3600) ;
            $n->message =  "<b>".$event->title . "</b>" . "<br>" . $event->description ;
            $n->message .=  "<br><br><b> Контакт: </b> <a href=\"?p=ZippyERP/ERP/Pages/Reference/ContactList&arg={$this->_item->contact_id}\">{$this->_item->lastname} {$this->_item->firstname}</a>";            
            $n->save()  ;
        }
        $this->addeventform->clean();
        $this->updateEvents();
    }
   
    //список   событий
    private function updateEvents()
    {
        $this->_eventlist = \ZippyERP\ERP\Entity\Event::find('  contact_id=' . $this->_item->contact_id);
        $this->dw_eventlist->Reload();
    }
    //вывод строки  коментария
    public function eventListOnRow($row)
    {
        $event = $row->getDataItem();

        
    $row->add(new BookmarkableLink('eventtitle', '#eventdesc_' . $row->getNumber()))->setValue($event->title);
           
        $row->add(new Label("eventdesc" ))->setText($event->description);
        $row->add(new Label("eventdate", date("Y-m-d H:i", $event->eventdate)));

        $row->add(new ClickLink('delevent'))->onClick($this, 'deleteEventOnClick');
    }

    //удаление коментария
    public function deleteEventOnClick($sender)
    {
        $event = $sender->owner->getDataItem();
        \ZippyERP\ERP\Entity\Event::delete($event->event_id);
        $this->updateEvents();
    }    
}
