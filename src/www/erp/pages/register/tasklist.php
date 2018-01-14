<?php

namespace ZippyERP\ERP\Pages\Register;

use \ZCL\DB\EntityDataSource as EDS;
use \Zippy\Binding\PropertyBinding as Prop;
use \Zippy\Html\DataList\ArrayDataSource;
use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Form\File;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextArea;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Link\RedirectLink;
use \Zippy\Html\Panel;
use \ZippyERP\ERP\Entity\Project;
use \ZippyERP\ERP\Entity\Task;
use \ZippyERP\ERP\Helper;
use \ZippyERP\System\System;
use \ZippyERP\ERP\Entity\Employee;

class TaskList extends \ZippyERP\ERP\Pages\Base
{

    private $_task;
    private $_taskds;
    public $_fileslist = array();
    public $_msglist = array();
    public $_shlist = array();

    public function __construct($task_id = 0)
    {
        parent::__construct();

        $this->_taskds = new EDS('\ZippyERP\ERP\Entity\Task',"","start_date");

        $this->add(new Panel('listtab'));
        $this->listtab->add(new Form('filterform'))->onSubmit($this, 'OnFilter');
        $this->listtab->filterform->add(new DropDownChoice('filterproject', Project::findArray('projectname'), 0));
        $this->listtab->filterform->add(new DropDownChoice('filterassignedto', Employee::findArray('shortname','employee_id in(select employee_id from erp_task_task_emp )','shortname'), 0));
        $this->listtab->filterform->add(new DropDownChoice('filterstatus', Task::getStatusList(), -1));
        $this->listtab->filterform->add(new DropDownChoice('filtersorting'));

        //форма   поиска  по  коду
        $this->listtab->add(new Form('searchform'))->onSubmit($this, 'OnSearch');
        $this->listtab->searchform->add(new TextInput('searchcode'));


        $this->listtab->add(new DataView('tasklist', $this->_taskds, $this, 'tasklistOnRow'));

        $this->add(new Panel('contenttab'))->setVisible(false);
        $this->contenttab->add(new Label('showtaskname'));
        $this->contenttab->add(new Form('editform'))->onSubmit($this, 'OnEdit');
        $this->contenttab->editform->add(new DropDownChoice('editstatus', Task::getStatusList(), 0));
        $this->contenttab->editform->add(new \ZCL\BT\DateTimePicker('editdatestatus',time()));
        

        $this->contenttab->add(new DataView('dw_msglist', new ArrayDataSource(new Prop($this, '_msglist')), $this, 'dw_msglistOnRow'));
        $this->contenttab->add(new Form('addmsgform'))->onSubmit($this, 'OnMsgSubmit');
        $this->contenttab->addmsgform->add(new TextArea('addmsg'));
        $this->contenttab->add(new DataView('dw_files', new ArrayDataSource(new Prop($this, '_fileslist')), $this, 'dw_filesOnRow'));
        $this->contenttab->add(new Form('addfileform'))->onSubmit($this, 'OnFileSubmit');
        $this->contenttab->addfileform->add(new File('addfile'));
        $this->contenttab->addfileform->add(new TextInput('adddescfile'));
        $this->contenttab->add(new ClickLink('tolist'))->onClick($this, 'tolistOnClick');


        $this->contenttab->add(new DataView('history', new ArrayDataSource(new Prop($this, '_shlist')), $this, 'historyOnRow'));
        
        
        //$this->_taskds->setWhere('task_id=' . ($task_id > 0 ? $task_id : 0 ));
        //$this->listtab->tasklist->Reload();
        if ($task_id > 0) {
            $this->_task = Task::load($task_id);
            $this->OpenTask();
        }
        
        $this->listtab->add(new \ZCL\Gantt\Gantt('gantt'))->setAjaxEvent($this, 'OnGantt');
        
        $this->OnFilter($this->listtab->filterform);
        $this->updateGantt();        
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
            $where .= " and  task_id in (select task_id from erp_task_task_emp where employee_id={$user}) "   ;
        $status = $sender->filterstatus->getValue();
        if ($status == -1)
            $where .= " and  status <> 3 "; // все  незакрытые
        else
            $where .= " and  status = " . $status;

        $this->_taskds->setWhere($where);
        $this->listtab->tasklist->Reload();
         $this->updateGantt(); 
    }

    public function OnSearch($sender)
    {
        $code = $this->listtab->searchform->searchcode->getText();
        $this->_taskds->setWhere('task_id=' . ($code > 0 ? $code : 0));
        $this->listtab->tasklist->Reload();
        $this->listtab->searchform->searchcode->setText('');
         $this->updateGantt();  
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
        $row->add(new Label('cost', $task->cost));
        $statuslist = Task::getStatusList();
        $row->add(new Label('status', $statuslist[$task->status]));
    
        if ($task->updated > 0)
            $row->add(new Label('updated', date('Y-m-d H:i', $task->updated)));

        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
    }

    //смена статуса
    public function OnEdit($sender)
    {
        $this->_task->status = $this->contenttab->editform->editstatus->getValue();
        $this->_task->save();
        
         
        $author = System::getUser()->userlogin;
        $date =  $this->contenttab->editform->editdatestatus->getDate();       
        $this->_task->addStatus($date,$author);
        $this->updateHistory();
        
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
     
        $this->listtab->setVisible(false);

        $this->contenttab->setVisible(true);
        $this->updateFiles();
        $this->updateMessages();
        $this->updateHistory();
    }

    public function tolistOnClick($sender)
    {
        $this->listtab->setVisible(true);
        $this->contenttab->setVisible(false);
        $this->listtab->tasklist->Reload();
         $this->updateGantt(); 
    }

    public function dw_msglistOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label("msgdata", nl2br($item->message), true));
        $row->add(new Label("msgdate", date("Y-m-d H:i", $item->created)));
        $row->add(new Label("msguser", $item->userlogin));
        $row->add(new ClickLink('delmsg'))->onClick($this, 'delmsgOnClick');
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
        $row->add(new ClickLink('delfile'))->onClick($this, 'delfileOnClick');
    }

    public function OnFileSubmit($sender)
    {

        $file = $this->contenttab->addfileform->addfile->getFile();
        if ($file['size'] > 10000000) {
            $this->getOwnerPage()->setError("Файл більше 10М !");
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

    
    public function historyOnRow($row)
    {
        $statuslist = Task::getStatusList();
 
        $item = $row->getDataItem();
        $row->add(new Label("huser",  $item->username   ));
        $row->add(new Label("hdate",  date("Y-m-d H:i",strtotime($item->sdate))   ));
        $row->add(new Label("hstatus",  $statuslist[$item->status] ));

        
    }  
    
    private function updateHistory()
    {
        $this->_shlist =  $this->_task->getStatusHistory();
        $this->contenttab->history->Reload();
    }      
   
   
    public function updateGantt()
    {
 
            $tasks = array();
            $items = $this->_taskds->getItems();
            foreach ($items as $item) {
                $col = "#00ff00";
                if($item->priority==0)  $col = "#ff0000";
                if($item->priority==3)  $col = "#00ff00";
                if($item->priority==5)  $col = "#ffdd00";
                if($item->status==3)  $col = "#a0a0a0";
                $tasks[] = new \ZCL\Gantt\GanttItem($item->task_id, $item->taskname  , $item->start_date, $item->end_date, $col);
            }

            $this->listtab->gantt->setData($tasks);
        
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
        $this->listtab->tasklist->Reload();
         
    }    
}
