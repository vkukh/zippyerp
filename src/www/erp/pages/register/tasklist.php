<?php

namespace ZippyERP\ERP\Pages\Register;

use ZCL\DB\EntityDataSource as EDS;
use Zippy\Binding\PropertyBinding as Prop;
use Zippy\Html\DataList\ArrayDataSource;
use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\File;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\TextArea;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Link\RedirectLink;
use Zippy\Html\Panel;
use ZippyERP\ERP\Entity\Project;
use ZippyERP\ERP\Entity\Task;
use ZippyERP\ERP\Helper;
use ZippyERP\System\System;

class TaskList extends \ZippyERP\System\Pages\Base
{

    private $_task;
    private $_taskds;
    public $_fileslist = array();
    public $_msglist = array();

    public function __construct($task_id = 0)
    {
        parent::__construct();

        $this->_taskds = new EDS('\ZippyERP\ERP\Entity\Task');

        $this->add(new Panel('listtab'));
        $this->listtab->add(new Form('filterform'))->setSubmitHandler($this, 'OnFilter');
        $this->listtab->filterform->add(new DropDownChoice('filterproject', Project::findArray('projectname'), 0));
        $this->listtab->filterform->add(new DropDownChoice('filterassignedto', Task::getAssignedList(), 0));
        $this->listtab->filterform->add(new DropDownChoice('filterstatus', Task::getStatusList(), -1));
        $this->listtab->filterform->add(new DropDownChoice('filtersorting'));

        //форма   поиска  по  коду
        $this->listtab->add(new Form('searchform'))->setSubmitHandler($this, 'OnSearch');
        $this->listtab->searchform->add(new TextInput('searchcode'));


        $this->listtab->add(new DataView('tasklist', $this->_taskds, $this, 'tasklistOnRow'));

        $this->add(new Panel('contenttab'))->setVisible(false);
        $this->contenttab->add(new Label('showtaskname'));
        $this->contenttab->add(new Form('editform'))->setSubmitHandler($this, 'OnEdit');
        $this->contenttab->editform->add(new DropDownChoice('editstatus', Task::getStatusList(), 0));
        $this->contenttab->editform->add(new DropDownChoice('editassignedto', Task::getAssignedList(), 0));


        $this->contenttab->add(new DataView('dw_msglist', new ArrayDataSource(new Prop($this, '_msglist')), $this, 'dw_msglistOnRow'));
        $this->contenttab->add(new Form('addmsgform'))->setSubmitHandler($this, 'OnMsgSubmit');
        $this->contenttab->addmsgform->add(new TextArea('addmsg'));
        $this->contenttab->add(new DataView('dw_files', new ArrayDataSource(new Prop($this, '_fileslist')), $this, 'dw_filesOnRow'));
        $this->contenttab->add(new Form('addfileform'))->setSubmitHandler($this, 'OnFileSubmit');
        $this->contenttab->addfileform->add(new File('addfile'));
        $this->contenttab->addfileform->add(new TextInput('adddescfile'));
        $this->contenttab->add(new ClickLink('tolist'))->setClickHandler($this, 'tolistOnClick');


        //$this->_taskds->setWhere('task_id=' . ($task_id > 0 ? $task_id : 0 ));
        //$this->listtab->tasklist->Reload();
        if ($task_id > 0) {
            $this->_task = Task::load($task_id);
            $this->OpenTask();
        }
    }

    public function OnFilter($sender)
    {
        $order = $sender->filtersorting->getValue();
        $orderby = "task_id asc";
        if ($order == 1)
            $orderby = "updated asc";
        if ($order == 2)
            $orderby = "priority asc";
        if ($order == 3)
            $orderby = "status asc";
        $this->_taskds->setOrder($orderby);

        $where = "1=1";
        $project = $sender->filterproject->getValue();
        if ($project > 0)
            $where .= " and  project_id=" . $project;
        $user = $sender->filterassignedto->getValue();
        if ($user > 0)
            $where .= " and  assignedto=" . $user;
        $status = $sender->filterstatus->getValue();
        if ($status == -1)
            $where .= " and  status <> 3 "; // все  назакрытые
        else
            $where .= " and  status = " . $status;

        $this->_taskds->setWhere($where);
        $this->listtab->tasklist->Reload();
    }

    public function OnSearch($sender)
    {
        $code = $this->listtab->searchform->searchcode->getText();
        $this->_taskds->setWhere('task_id=' . ($code > 0 ? $code : 0));
        $this->listtab->tasklist->Reload();
        $this->listtab->searchform->searchcode->setText('');
    }

    public function tasklistOnRow($row)
    {
        $task = $row->getDataItem();

        $row->add(new RedirectLink('code', "\\ZippyERP\\ERP\\Pages\\Register\\TaskList", array($task->task_id)))->setValue($task->task_id);
        $row->add(new Label('name', $task->taskname));
        $row->add(new Label('projectname', $task->projectname));
        if ($task->start_date > 0)
            $row->add(new Label('startdate', date('Y-m-d', $task->start_date)));
        $row->add(new Label('hours', $task->hours));
        $statuslist = Task::getStatusList();
        $row->add(new Label('status', $statuslist[$task->status]));
        $row->add(new Label('assignedtoname', $task->assignedtoname));
        if ($task->updated > 0)
            $row->add(new Label('updated', date('Y-m-d H:i', $task->updated)));

        $row->add(new ClickLink('edit'))->setClickHandler($this, 'editOnClick');
    }

    public function OnEdit($sender)
    {
        $this->_task->status = $this->contenttab->editform->editstatus->getValue();
        $this->_task->assignedto = $this->contenttab->editform->editassignedto->getValue();

        $this->_task->save();
    }

    public function editOnClick($sender)
    {
        $this->_task = $sender->owner->getDataItem();
        $this->OpenTask();
    }

    public function OpenTask()
    {
        $this->contenttab->showtaskname->setText($this->_task->taskname);
        $this->contenttab->editform->editstatus->setValue($this->_task->status);
        $this->contenttab->editform->editassignedto->setValue($this->_task->assignedto);

        $this->listtab->setVisible(false);

        $this->contenttab->setVisible(true);
        $this->updateFiles();
        $this->updateMessages();
    }

    public function tolistOnClick($sender)
    {
        $this->listtab->setVisible(true);
        $this->contenttab->setVisible(false);
        $this->listtab->tasklist->Reload();
    }

    public function dw_msglistOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label("msgdata", nl2br($item->message), true));
        $row->add(new Label("msgdate", date("Y-m-d H:i", $item->created)));
        $row->add(new Label("msguser", $item->userlogin));
        $row->add(new ClickLink('delmsg'))->setClickHandler($this, 'delmsgOnClick');
    }

    public function OnMsgSubmit($sender)
    {
        $msg = new \ZippyERP\ERP\Entity\Message();
        $msg->message = $this->contenttab->addmsgform->addmsg->getText();
        $msg->created = time();
        $msg->user_id = System::getUser()->user_id;
        $msg->item_id = $this->_task->task_id;
        $msg->item_type = \ZippyERP\ERP\Consts::MSG_ITEM_TYPE_TASK;
        if (strlen($msg->message) == 0)
            return;
        $msg->save();

        $this->contenttab->addmsgform->addmsg->setText('');
        $this->updateMessages();
    }

    public function delmsgOnClick($sender)
    {
        $msg = $sender->owner->getDataItem();
        \ZippyERP\ERP\Entity\Message::delete($msg->message_id);
        $this->updateMessages();
    }

    public function dw_filesOnRow($row)
    {
        $item = $row->getDataItem();

        $file = $row->add(new \Zippy\Html\Link\BookmarkableLink("filename", _BASEURL . '?p=ZippyERP/ERP/Pages/LoadFile&arg=' . $item->file_id));
        $file->setValue($item->filename);
        $file->setAttribute('title', $item->description);
        $row->add(new ClickLink('delfile'))->setClickHandler($this, 'delfileOnClick');
    }

    public function OnFileSubmit($sender)
    {

        $file = $this->contenttab->addfileform->addfile->getFile();
        if ($file['size'] > 10000000) {
            $this->getOwnerPage()->setError("Файл более 10М !");
            return;
        }

        Helper::addFile($file, $this->_task->task_id, $this->contenttab->addfileform->adddescfile->getText(), \ZippyERP\ERP\Consts::FILE_ITEM_TYPE_TASK);
        $this->contenttab->addfileform->adddescfile->setText('');
        $this->updateFiles();
    }

    public function delfileOnClick($sender)
    {
        $file = $sender->owner->getDataItem();
        Helper::deleteFile($file->file_id);
        $this->updateFiles();
    }

    private function updateFiles()
    {
        $this->_fileslist = Helper::getFileList($this->_task->task_id, \ZippyERP\ERP\Consts::FILE_ITEM_TYPE_TASK);
        $this->contenttab->dw_files->Reload();
    }

    private function updateMessages()
    {
        $this->_msglist = \ZippyERP\ERP\Entity\Message::find('item_type = 3 and item_id=' . $this->_task->task_id);
        $this->contenttab->dw_msglist->Reload();
    }

}
