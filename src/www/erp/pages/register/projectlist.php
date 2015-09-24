<?php

namespace ZippyERP\ERP\Pages\Register;

use \Zippy\Html\Panel;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\CheckBox;
use \Zippy\Html\DataList\DataView;
use \ZCL\DB\EntityDataSource;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Link\RedirectLink;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\Date;
use \Zippy\Html\Form\TextArea;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Form\File;
use \Zippy\Html\Form\SubmitButton;
use \Zippy\Html\Form\Button;
use \Zippy\Html\Form\AutocompleteTextInput;
use \ZCL\DB\EntityDataSource as EDS;
use \ZippyERP\System\Application as App;
use \ZippyERP\System\System;
use \ZippyERP\System\Session;
use \ZippyERP\ERP\Entity\Project;
use \ZippyERP\ERP\Entity\Task;
use \ZippyERP\ERP\Entity\Doc\Document;
use \Zippy\Binding\PropertyBinding as Prop;
use \Zippy\Html\DataList\ArrayDataSource;
use \ZippyERP\ERP\Helper;

class ProjectList extends \ZippyERP\ERP\Pages\Base
{

    private $_project;
    private $_projectds;
    public $_fileslist = array();
    public $_msglist = array();
    private $_task;
    private $_taskds;

    public function __construct()
    {
        parent::__construct();
        $this->_projectds = new EDS('\ZippyERP\ERP\Entity\Project');
        $this->_taskds = new EDS('\ZippyERP\ERP\Entity\Task');

        $this->add(new Panel('listtab'));
        $this->listtab->add(new ClickLink('addnew'))->setClickHandler($this, 'addnewOnClick');
        $this->listtab->add(new DataView('projectlist', $this->_projectds, $this, 'projectlistOnRow'))->Reload();

        $this->add(new Panel('edittab'))->setVisible(false);
        $editform = $this->edittab->add(new Form('editform'));

        $editform->add(new TextInput('editprojectname'));
        $editform->add(new Date('editstartdate', time()));
        $editform->add(new Date('editenddate', time()));
        $editform->add(new TextArea('editdesc'));
        $editform->add(new AutocompleteTextInput('editbase'))->setAutocompleteHandler($this, 'editbaseOnAutocomplete');
        $editform->add(new SubmitButton('save'))->setClickHandler($this, 'saveOnClick');
        $editform->add(new Button('cancel'))->setClickHandler($this, 'cancelOnClick');
        $this->add(new Panel('contenttab'))->setVisible(false);
        $this->contenttab->add(new DataView('dw_msglist', new ArrayDataSource(new Prop($this, '_msglist')), $this, 'dw_msglistOnRow'));
        $this->contenttab->add(new Form('addmsgform'))->setSubmitHandler($this, 'OnMsgSubmit');
        $this->contenttab->addmsgform->add(new TextArea('addmsg'));
        $this->contenttab->add(new DataView('dw_files', new ArrayDataSource(new Prop($this, '_fileslist')), $this, 'dw_filesOnRow'));
        $this->contenttab->add(new Form('addfileform'))->setSubmitHandler($this, 'OnFileSubmit');
        $this->contenttab->addfileform->add(new File('addfile'));
        $this->contenttab->addfileform->add(new TextInput('adddescfile'));
        $this->contenttab->add(new Label('showname'));
        $this->contenttab->add(new Label('showdesc'));
        $this->contenttab->add(new ClickLink('tolist'))->setClickHandler($this, 'cancelOnClick');

        //задачи
        $this->add(new Panel('taskstab'))->setVisible(false);
        $this->taskstab->add(new Panel('tasklisttab'));
        $this->taskstab->add(new ClickLink('tolist2'))->setClickHandler($this, 'cancelOnClick');
        $this->taskstab->add(new Label('showname2'));

        $this->taskstab->tasklisttab->add(new ClickLink('taskaddnew'))->setClickHandler($this, 'addnewtaskOnClick');
        $this->taskstab->tasklisttab->add(new ClickLink('togantt'))->setClickHandler($this, 'toogleGantt');
        $this->taskstab->tasklisttab->add(new DataView('tasklist', $this->_taskds, $this, 'tasklistOnRow'));


        $this->add(new Panel('edittasktab'))->setVisible(false);
        $edittaskform = $this->edittasktab->add(new Form('edittaskform'));
        $edittaskform->add(new TextInput('edittaskname'));
        $edittaskform->add(new TextInput('edittaskhours'));
        $edittaskform->add(new Date('edittaskstartdate'));
        $edittaskform->add(new TextArea('edittaskdesc'));
        $edittaskform->add(new DropDownChoice('edittaskstatus', Task::getStatusList(), 0));
        $edittaskform->add(new DropDownChoice('edittaskspriority', Task::getPriorityList(), 3));
        $edittaskform->add(new DropDownChoice('editassignedto', Task::getAssignedList(), 0));
        $edittaskform->add(new SubmitButton('tasksave'))->setClickHandler($this, 'tasksaveOnClick');
        $edittaskform->add(new Button('taskcancel'))->setClickHandler($this, 'taskcancelOnClick');

        $this->taskstab->add(new Panel('ganttab'))->setVisible(false);
        $this->taskstab->ganttab->add(new ClickLink('fromgantt'))->setClickHandler($this, 'toogleGantt');
        $this->taskstab->ganttab->add(new \ZCL\Gantt\Gantt('gantt'))->setAjaxEvent($this, 'OnGantt');
    }

    // новый   проект
    public function addnewOnClick($sender)
    {

        $this->listtab->setVisible(false);
        $this->edittab->setVisible(true);
        $this->edittab->editform->clean();

        $this->_project = new Project();
    }

    public function projectlistOnRow($row)
    {
        $project = $row->getDataItem();

        $row->add(new Label('name', $project->projectname));
        if ($project->start_date > 0)
            $row->add(new Label('startdate', date('Y-m-d', $project->start_date)));
        if ($project->end_date > 0)
            $row->add(new Label('enddate', date('Y-m-d', $project->end_date)));

        $row->add(new Label('ready', "{$project->taskclosed} из {$project->taskall}"));
        $row->add(new ClickLink('edit'))->setClickHandler($this, 'editOnClick');
        $row->add(new ClickLink('tasks'))->setClickHandler($this, 'tasksOnClick');
        $row->add(new ClickLink('show'))->setClickHandler($this, 'showOnClick');
        $row->add(new ClickLink('delete'))->setClickHandler($this, 'deleteOnClick');
    }

    public function editOnClick($sender)
    {
        $this->_project = $sender->owner->getDataItem();
        $this->edittab->editform->editprojectname->setText($this->_project->projectname);
        $this->edittab->editform->editdesc->setText($this->_project->description);
        $this->edittab->editform->editbase->setKey($this->_project->doc_id);

        $this->edittab->editform->editbase->setValue(Document::load($this->_project->doc_id)->document_number);

        if ($this->_project->start_date > 0)
            $this->edittab->editform->editstartdate->setText(date('Y-m-d', $this->_project->start_date));
        else
            $this->edittab->editform->editstartdate->setText('');

        if ($this->_project->end_date > 0)
            $this->edittab->editform->editenddate->setText(date('Y-m-d', $this->_project->end_date));
        else
            $this->edittab->editform->editenddate->setText('');

        $this->listtab->setVisible(false);
        $this->edittab->setVisible(true);
        $this->updateFiles();
        $this->updateMessages();
    }

    public function showOnClick($sender)
    {
        $this->_project = $sender->owner->getDataItem();
        $this->listtab->setVisible(false);
        $this->taskstab->setVisible(false);
        $this->contenttab->setVisible(true);
        $this->contenttab->showname->setText($this->_project->projectname);
        $this->contenttab->showdesc->setText($this->_project->description);
    }

    public function deleteOnClick($sender)
    {
        $item = $sender->owner->getDataItem();
        Project::delete($item->project_id);
        $this->listtab->projectlist->Reload();
    }

    public function editbaseOnAutocomplete($sender)
    {
        $text = $sender->getValue();
        $answer = array();
        $conn = \ZCL\DB\DB::getConnect();
        $sql = "select document_id,document_number from erp_document where document_number  like '%{$text}%'  order  by document_id desc  limit 0,20";
        $rs = $conn->Execute($sql);
        foreach ($rs as $row) {
            $answer[$row['document_id']] = $row['document_number'];
        }
        return $answer;
    }

    public function saveOnClick($sender)
    {


        $this->_project->projectname = $this->edittab->editform->editprojectname->getText();
        $this->_project->doc_id = $this->edittab->editform->editbase->getKey();
        $this->_project->description = $this->edittab->editform->editdesc->getText();
        $this->_project->start_date = $this->edittab->editform->editstartdate->getDate();
        $this->_project->end_date = $this->edittab->editform->editenddate->getDate();
        $this->_project->save();

        $this->listtab->setVisible(true);
        $this->edittab->setVisible(false);
        $this->listtab->projectlist->Reload();
    }

    public function cancelOnClick($sender)
    {
        $this->listtab->setVisible(true);
        $this->edittab->setVisible(false);
        $this->taskstab->setVisible(false);
        $this->contenttab->setVisible(false);
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
        $msg->item_id = $this->_project->project_id;
        $msg->item_type = \ZippyERP\ERP\Consts::MSG_ITEM_TYPE_PRJ;
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

        Helper::addFile($file, $this->_project->project_id, $this->contenttab->addfileform->adddescfile->getText(), \ZippyERP\ERP\Consts::FILE_ITEM_TYPE_PRJ);
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
        $this->_fileslist = Helper::getFileList($this->_project->project_id, \ZippyERP\ERP\Consts::FILE_ITEM_TYPE_PRJ);
        $this->contenttab->dw_files->Reload();
    }

    private function updateMessages()
    {
        $this->_msglist = \ZippyERP\ERP\Entity\Message::find('item_type = 2 and item_id=' . $this->_project->project_id);
        $this->contenttab->dw_msglist->Reload();
    }

    //  к  списку  задач  по  проекту
    public function tasksOnClick($sender)
    {
        $this->_project = $sender->owner->getDataItem();
        //App::Redirect('\ZippyERP\ERP\Pages\Register\TaskList',$project->project_id);
        $this->listtab->setVisible(false);
        $this->taskstab->setVisible(true);

        $this->taskstab->showname2->setText($this->_project->projectname);

        $this->_taskds->setWhere('project_id =' . $this->_project->project_id);
        $this->taskstab->tasklisttab->tasklist->Reload();
    }

    // новая  задача
    public function addnewtaskOnClick($sender)
    {

        $this->taskstab->setVisible(false);
        $this->edittasktab->setVisible(true);
        $this->edittasktab->edittaskform->clean();
        $this->_task = new Task();
        $this->_task->project_id = $this->_project->project_id;
        $this->_task->createdby = System::getUser()->user_id;
        $this->_task->created = time();
        $this->_task->updated = time();
        $this->edittasktab->edittaskform->edittaskspriority->setValue(3);
    }

    public function tasklistOnRow($row)
    {
        $task = $row->getDataItem();

        $row->add(new Label('taskcode', $task->task_id));
        $row->add(new Label('taskname', $task->taskname));
        if ($task->start_date > 0)
            $row->add(new Label('taskstartdate', date('Y-m-d', $task->start_date)));
        $row->add(new Label('taskhours', $task->hours));
        $statuslist = Task::getStatusList();
        $row->add(new Label('taskstatus', $statuslist[$task->status]));
        $row->add(new Label('taskassignedtoname', $task->assignedtoname));
        $row->add(new ClickLink('taskedit'))->setClickHandler($this, 'taskeditOnClick');
        $row->add(new ClickLink('taskdelete'))->setClickHandler($this, 'taskdeleteOnClick');
    }

    public function taskeditOnClick($sender)
    {
        $this->_task = $sender->owner->getDataItem();
        $this->taskstab->setVisible(false);
        $this->edittasktab->setVisible(true);
        //$this->edittasktab->edittaskform->clean(); 
        $this->edittasktab->edittaskform->edittaskname->setText($this->_task->taskname);
        $this->edittasktab->edittaskform->edittaskdesc->setText($this->_task->description);
        $this->edittasktab->edittaskform->edittaskhours->setText($this->_task->hours);
        $this->edittasktab->edittaskform->edittaskstatus->setValue($this->_task->status);
        $this->edittasktab->edittaskform->edittaskspriority->setValue($this->_task->priority);
        $this->edittasktab->edittaskform->editassignedto->setValue($this->_task->assignedto);
        $this->edittasktab->edittaskform->edittaskstartdate->setDate($this->_task->start_date);
    }

    public function tasksaveOnClick($sender)
    {


        $this->_task->taskname = $this->edittasktab->edittaskform->edittaskname->getText();
        $this->_task->description = $this->edittasktab->edittaskform->edittaskdesc->getText();
        $this->_task->hours = $this->edittasktab->edittaskform->edittaskhours->getText();
        $this->_task->status = $this->edittasktab->edittaskform->edittaskstatus->getValue();
        $this->_task->priority = $this->edittasktab->edittaskform->edittaskspriority->getValue();
        $this->_task->assignedto = $this->edittasktab->edittaskform->editassignedto->getValue();
        $this->_task->start_date = $this->edittasktab->edittaskform->edittaskstartdate->getDate();
        $this->_task->end_date = $this->_task->start_date + 3600 * $this->_task->hours;
        $this->_task->updated = time();

        $this->_task->save();

        $this->taskstab->setVisible(true);
        $this->edittasktab->setVisible(false);
        $this->taskstab->tasklisttab->tasklist->Reload();
    }

    public function taskdeleteOnClick($sender)
    {
        $task = $sender->owner->getDataItem();
        Task::delete($task->task_id);
        $this->taskstab->tasklisttab->tasklist->Reload();
    }

    public function taskcancelOnClick($sender)
    {
        $this->taskstab->setVisible(true);
        $this->edittasktab->setVisible(false);
    }

    //Изменения  на  диаграмме
    public function OnGantt($sender, $eventdata)
    {

        $action = $eventdata['action'];
        $task = Task::load($eventdata['id']);
        if ($action == "drag") {
            $task->start_date = $eventdata['start'];
            $task->end_date = $eventdata['end'];
            $task->save();
        }
        if ($action == "resize") {
            $task->start_date = $eventdata['start'];
            $task->end_date = $eventdata['end'];
            $task->hours = ($task->end_date - $task->start_date) / 3600;
            $task->updated = time();
            $task->save();
        }
    }

    public function toogleGantt($sender)
    {
        if ($sender->id == "togantt") {
            $this->taskstab->ganttab->setVisible(true);
            $this->taskstab->tasklisttab->setVisible(false);
            $tasks = array();
            $items = $this->_taskds->getItems();
            foreach ($items as $item) {
                $tasks[] = new \ZCL\Gantt\GanttItem($item->task_id, $item->taskname, $item->start_date, $item->end_date, "#ffaaaa");
            }

            $this->taskstab->ganttab->gantt->setData($tasks);
        } else {
            $this->taskstab->ganttab->setVisible(false);
            $this->taskstab->tasklisttab->setVisible(true);
            $this->taskstab->tasklisttab->tasklist->Reload();
        }
    }

}
