<?php

namespace ZippyERP\ERP\Pages\Register;

use \ZCL\DB\EntityDataSource as EDS;
use \Zippy\Binding\PropertyBinding as Prop;
use \Zippy\Html\DataList\ArrayDataSource;
use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Form\AutocompleteTextInput;
use \Zippy\Html\Form\Button;
use \Zippy\Html\Form\Date;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Form\File;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\SubmitButton;
use \Zippy\Html\Form\TextArea;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Panel;
use \ZippyERP\ERP\Entity\Doc\Document;
use \ZippyERP\ERP\Entity\Project;
use \ZippyERP\ERP\Entity\Task;
use \ZippyERP\ERP\Entity\Store;
use \ZippyERP\ERP\Entity\Customer;
use \ZippyERP\ERP\Entity\Stock;
use \ZippyERP\ERP\Entity\Item;
use \ZippyERP\ERP\Entity\Employee;
use \ZippyERP\ERP\Helper as H;
use \ZippyERP\System\System;

class ProjectList extends \ZippyERP\ERP\Pages\Base
{

    private $_project;
    private $_projectds;
    public $_fileslist = array();
    public $_msglist = array();
    private $_task;
    private $_taskds;
    public $_users = array();
    public $_items = array();
    public $_store_id = 0;

    public function __construct() {
        parent::__construct();
        $this->_projectds = new EDS('\ZippyERP\ERP\Entity\Project', '', 'project_id desc');
        $this->_taskds = new EDS('\ZippyERP\ERP\Entity\Task', "", "task_id");


        $this->add(new Panel('listtab'));
        $this->listtab->add(new ClickLink('addnew'))->onClick($this, 'addnewOnClick');
        $this->listtab->add(new DataView('projectlist', $this->_projectds, $this, 'projectlistOnRow'))->Reload();

        $this->add(new Panel('edittab'))->setVisible(false);
        $editform = $this->edittab->add(new Form('editform'));

        $editform->add(new TextInput('editprojectname'));
        $editform->add(new Date('editstartdate', time()));
        $editform->add(new Date('editenddate', time()));

        $editform->add(new TextArea('editdesc'));
        $editform->add(new AutocompleteTextInput('editcustomer'))->onText($this, 'OnAutoCustomer');
        $editform->editcustomer->onChange($this, 'OnChangeCustomer');


        $editform->add(new AutocompleteTextInput('editbase'))->onText($this, 'editbaseOnAutocomplete');
        $editform->add(new SubmitButton('save'))->onClick($this, 'saveOnClick');
        $editform->add(new Button('cancel'))->onClick($this, 'cancelOnClick');
        $this->add(new Panel('contenttab'))->setVisible(false);
        $this->contenttab->add(new DataView('dw_msglist', new ArrayDataSource(new Prop($this, '_msglist')), $this, 'dw_msglistOnRow'));
        $this->contenttab->add(new Form('addmsgform'))->onSubmit($this, 'OnMsgSubmit');
        $this->contenttab->addmsgform->add(new TextArea('addmsg'));
        $this->contenttab->add(new DataView('dw_files', new ArrayDataSource(new Prop($this, '_fileslist')), $this, 'dw_filesOnRow'));
        $this->contenttab->add(new Form('addfileform'))->onSubmit($this, 'OnFileSubmit');
        $this->contenttab->addfileform->add(new File('addfile'));
        $this->contenttab->addfileform->add(new TextInput('adddescfile'));
        $this->contenttab->add(new Label('showname'));
        $this->contenttab->add(new Label('showdesc'));
        $this->contenttab->add(new ClickLink('tolist'))->onClick($this, 'cancelOnClick');

        //задачи
        $this->add(new Panel('taskstab'))->setVisible(false);

        $this->taskstab->add(new ClickLink('tolist2'))->onClick($this, 'cancelOnClick');
        $this->taskstab->add(new Label('showname2'));

        $this->taskstab->add(new ClickLink('taskaddnew'))->onClick($this, 'addnewtaskOnClick');

        $this->taskstab->add(new DataView('tasklist', $this->_taskds, $this, 'tasklistOnRow'));


        $this->add(new Panel('edittasktab'))->setVisible(false);
        $edittaskform = $this->edittasktab->add(new Form('edittaskform'));
        $edittaskform->add(new TextInput('edittaskname'));
        $edittaskform->add(new TextInput('edittaskhours'));
        $edittaskform->add(new TextInput('edittaskcost'));
        $edittaskform->add(new Date('edittaskstartdate'));
        $edittaskform->add(new TextArea('edittaskdesc'));
        $edittaskform->add(new DropDownChoice('edittaskstatus', Task::getStatusList(), 0));
        $edittaskform->add(new DropDownChoice('edittaskspriority', Task::getPriorityList(), 3));
        $edittaskform->add(new SubmitButton('tasksave'))->onClick($this, 'tasksaveOnClick');
        $edittaskform->add(new Button('taskcancel'))->onClick($this, 'taskcancelOnClick');



        $this->add(new Panel('edituserstab'))->setVisible(false);
        $this->edituserstab->add(new ClickLink('cancelusers'))->onClick($this, 'usersCancelOnClick');
        $this->edituserstab->add(new ClickLink('saveusers'))->onClick($this, 'usersSaveOnClick');
        $usersform = $this->edituserstab->add(new Form('usersform'));
        $usersform->add(new DropDownChoice('editassignedto', Employee::findArray('shortname', '', 'shortname'), 0));
        $this->edituserstab->add(new DataView('userslist', new ArrayDataSource(new Prop($this, '_users')), $this, 'usersOnRow'));
        $usersform->onSubmit($this, "onAddUser");


        $this->add(new Panel('edititemstab'))->setVisible(false);
        $this->edititemstab->add(new ClickLink('cancelitems'))->onClick($this, 'itemsCancelOnClick');
        $this->edititemstab->add(new ClickLink('saveitems'))->onClick($this, 'itemsSaveOnClick');
        $itemsform = $this->edititemstab->add(new Form('itemsform'));
        $itemsform->add(new AutocompleteTextInput('edititem'))->onText($this, 'edititemOnAutocomplete');
        $itemsform->edititem->onChange($this, 'onChangeItem', true);

        $itemsform->add(new TextInput('editqty'));
        $itemsform->add(new TextInput('editprice'));
        $itemsform->add(new Label('qtystock'));
        $itemsform->add(new SubmitButton('additem'))->onClick($this, 'onAddItem');

        $this->edititemstab->add(new DataView('itemslist', new ArrayDataSource(new Prop($this, '_items')), $this, 'itemsOnRow'));


        $itemsform->add(new Label('itemstotal'));

        $this->taskstab->add(new \ZCL\Gantt\Gantt('gantt'))->setAjaxEvent($this, 'OnGantt');
        //   $this->updateGantt();

        $this->_store_id = Store::getFirst("store_type=" . Store::STORE_TYPE_OPT)->store_id;
    }

    // новый   проект
    public function addnewOnClick($sender) {

        $this->listtab->setVisible(false);
        $this->edittab->setVisible(true);
        $this->edittab->editform->clean();

        $this->_project = new Project();
    }

    public function projectlistOnRow($row) {
        $project = $row->getDataItem();

        $row->add(new Label('name', $project->projectname));
        if ($project->start_date > 0)
            $row->add(new Label('startdate', date('Y-m-d', $project->start_date)));
        if ($project->end_date > 0)
            $row->add(new Label('enddate', date('Y-m-d', $project->end_date)));

        $row->add(new Label('ready', "{$project->taskclosed} из {$project->taskall}"));
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('tasks'))->onClick($this, 'tasksOnClick');
        $row->add(new ClickLink('show'))->onClick($this, 'showOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function editOnClick($sender) {
        $this->_project = $sender->owner->getDataItem();
        $this->edittab->editform->editprojectname->setText($this->_project->projectname);
        $this->edittab->editform->editdesc->setText($this->_project->description);
        $this->edittab->editform->editbase->setKey($this->_project->doc_id);

        $this->edittab->editform->editbase->setValue(Document::load($this->_project->doc_id)->document_number);

        if ($this->_project->start_date > 0)
            $this->edittab->editform->editstartdate->setText(date('Y-m-d', $this->_project->start_date));
        else
            $this->edittab->editform->editstartdate->setText('');

        if ($this->_project->customer_id > 0) {
            $customer = Customer::load($this->_project->customer_id);
            $this->edittab->editform->editcustomer->setKey($customer->customer_id);
            $this->edittab->editform->editcustomer->setText($customer->customer_name);
        }
        if ($this->_project->end_date > 0)
            $this->edittab->editform->editenddate->setText(date('Y-m-d', $this->_project->end_date));
        else
            $this->edittab->editform->editenddate->setText('');

        $this->listtab->setVisible(false);
        $this->edittab->setVisible(true);
        $this->updateFiles();
        $this->updateMessages();
    }

    public function showOnClick($sender) {
        $this->_project = $sender->owner->getDataItem();
        $this->listtab->setVisible(false);
        $this->taskstab->setVisible(false);
        $this->contenttab->setVisible(true);
        $this->contenttab->showname->setText($this->_project->projectname);
        $this->contenttab->showdesc->setText($this->_project->description);
    }

    public function deleteOnClick($sender) {
        $item = $sender->owner->getDataItem();
        $cnt = Task::findCnt("project_id=" . $item->project_id);
        if ($cnt > 0) {
            $this->setError("Не можна видалити проект з завданнями");
            return;
        }
        Project::delete($item->project_id);
        $this->listtab->projectlist->Reload();
        $this->updateGantt();
    }

    public function editbaseOnAutocomplete($sender) {
        $text = $sender->getValue();
        $answer = array();
        $conn = \ZDB\DB::getConnect();
        $sql = "select document_id,document_number from erp_document where document_number  like '%{$text}%'  order  by document_id desc  limit 0,20";
        $rs = $conn->Execute($sql);
        foreach ($rs as $row) {
            $answer[$row['document_id']] = $row['document_number'];
        }
        return $answer;
    }

    public function saveOnClick($sender) {


        $this->_project->projectname = $this->edittab->editform->editprojectname->getText();
        $this->_project->doc_id = $this->edittab->editform->editbase->getKey();
        $this->_project->description = $this->edittab->editform->editdesc->getText();
        $this->_project->start_date = $this->edittab->editform->editstartdate->getDate();
        $this->_project->end_date = $this->edittab->editform->editenddate->getDate();
        $this->_project->customer_id = $this->edittab->editform->editcustomer->getKey();
        $this->_project->save();

        $this->listtab->setVisible(true);
        $this->edittab->setVisible(false);
        $this->listtab->projectlist->Reload();
        $this->updateGantt();
    }

    public function cancelOnClick($sender) {
        $this->listtab->setVisible(true);
        $this->edittab->setVisible(false);
        $this->taskstab->setVisible(false);
        $this->contenttab->setVisible(false);
    }

    public function dw_msglistOnRow($row) {
        $item = $row->getDataItem();

        $row->add(new Label("msgdata", nl2br($item->message), true));
        $row->add(new Label("msgdate", date("Y-m-d H:i", $item->created)));
        $row->add(new Label("msguser", $item->userlogin));
        $row->add(new ClickLink('delmsg'))->onClick($this, 'delmsgOnClick');
    }

    public function OnMsgSubmit($sender) {
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

    public function delmsgOnClick($sender) {
        $msg = $sender->owner->getDataItem();
        \ZippyERP\ERP\Entity\Message::delete($msg->message_id);
        $this->updateMessages();
    }

    public function dw_filesOnRow($row) {
        $item = $row->getDataItem();

        $file = $row->add(new \Zippy\Html\Link\BookmarkableLink("filename", _BASEURL . '?p=ZippyERP/ERP/Pages/LoadFile&arg=' . $item->file_id));
        $file->setValue($item->filename);
        $file->setAttribute('title', $item->description);
        $row->add(new ClickLink('delfile'))->onClick($this, 'delfileOnClick');
    }

    public function OnFileSubmit($sender) {

        $file = $this->contenttab->addfileform->addfile->getFile();
        if ($file['size'] > 10000000) {
            $this->getOwnerPage()->setError("Файл більше 10М !");
            return;
        }

        H::addFile($file, $this->_project->project_id, $this->contenttab->addfileform->adddescfile->getText(), \ZippyERP\ERP\Consts::FILE_ITEM_TYPE_PRJ);
        $this->contenttab->addfileform->adddescfile->setText('');
        $this->updateFiles();
    }

    public function delfileOnClick($sender) {
        $file = $sender->owner->getDataItem();
        H::deleteFile($file->file_id);
        $this->updateFiles();
    }

    private function updateFiles() {
        $this->_fileslist = H::getFileList($this->_project->project_id, \ZippyERP\ERP\Consts::FILE_ITEM_TYPE_PRJ);
        $this->contenttab->dw_files->Reload();
    }

    private function updateMessages() {
        $this->_msglist = \ZippyERP\ERP\Entity\Message::find('item_type = 2 and item_id=' . $this->_project->project_id);
        $this->contenttab->dw_msglist->Reload();
    }

    //  к  списку  задач  по  проекту
    public function tasksOnClick($sender) {
        $this->_project = $sender->owner->getDataItem();
        //App::Redirect('\ZippyERP\ERP\Pages\Register\TaskList',$project->project_id);
        $this->listtab->setVisible(false);
        $this->taskstab->setVisible(true);

        $this->taskstab->showname2->setText($this->_project->projectname);

        $this->_taskds->setWhere('project_id =' . $this->_project->project_id);
        $this->taskstab->tasklist->Reload();
        $this->updateGantt();
    }

    // новая  задача
    public function addnewtaskOnClick($sender) {

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

    public function tasklistOnRow($row) {
        $task = $row->getDataItem();

        $row->add(new Label('taskcode', $task->task_id));
        $row->add(new Label('taskname', $task->taskname));
        if ($task->start_date > 0)
            $row->add(new Label('taskstartdate', date('Y-m-d', $task->start_date)));
        $row->add(new Label('taskhours', $task->hours));
        $row->add(new Label('taskcost', $task->cost));
        $statuslist = Task::getStatusList();
        $row->add(new Label('taskstatus', $statuslist[$task->status]));
        $prlist = Task::getPriorityList();
        $row->add(new Label('taskpriority', $prlist[$task->priority]));


        $row->add(new ClickLink('taskedit'))->onClick($this, 'taskeditOnClick');
        $row->add(new ClickLink('taskdelete'))->onClick($this, 'taskdeleteOnClick');
        $row->add(new ClickLink('taskusers'))->onClick($this, 'usersOnClick');
        $row->add(new ClickLink('taskitems'))->onClick($this, 'itemsOnClick');
    }

    public function taskeditOnClick($sender) {
        $this->_task = $sender->owner->getDataItem();
        $this->taskstab->setVisible(false);
        $this->edittasktab->setVisible(true);
        //$this->edittasktab->edittaskform->clean(); 
        $this->edittasktab->edittaskform->edittaskname->setText($this->_task->taskname);
        $this->edittasktab->edittaskform->edittaskdesc->setText($this->_task->description);
        $this->edittasktab->edittaskform->edittaskhours->setText($this->_task->hours);
        $this->edittasktab->edittaskform->edittaskcost->setText($this->_task->cost);
        $this->edittasktab->edittaskform->edittaskstatus->setValue($this->_task->status);
        $this->edittasktab->edittaskform->edittaskstatus->setOptionList(Task::getStatusList($this->_task->status));
        $this->edittasktab->edittaskform->edittaskspriority->setValue($this->_task->priority);

        $this->edittasktab->edittaskform->edittaskstartdate->setDate($this->_task->start_date);
    }

    public function tasksaveOnClick($sender) {


        $this->_task->taskname = $this->edittasktab->edittaskform->edittaskname->getText();
        $this->_task->description = $this->edittasktab->edittaskform->edittaskdesc->getText();
        $this->_task->hours = $this->edittasktab->edittaskform->edittaskhours->getText();
        $this->_task->cost = $this->edittasktab->edittaskform->edittaskcost->getText();
        $this->_task->status = $this->edittasktab->edittaskform->edittaskstatus->getValue();
        $this->_task->priority = $this->edittasktab->edittaskform->edittaskspriority->getValue();

        $this->_task->start_date = $this->edittasktab->edittaskform->edittaskstartdate->getDate();
        $this->_task->end_date = $this->_task->start_date + 3 * 3600 * $this->_task->hours;
        $this->_task->updated = time();

        $this->_task->save();

        $this->taskstab->setVisible(true);
        $this->edittasktab->setVisible(false);
        $this->taskstab->tasklist->Reload();
        $this->updateGantt();
    }

    public function taskdeleteOnClick($sender) {
        $task = $sender->owner->getDataItem();
        Task::delete($task->task_id);
        $this->taskstab->tasklist->Reload();
        $this->updateGantt();
    }

    public function taskcancelOnClick($sender) {
        $this->taskstab->setVisible(true);
        $this->edittasktab->setVisible(false);
    }

    //панель исполнителей
    public function usersOnClick($sender) {
        $this->_task = $sender->owner->getDataItem();
        $this->_users = $this->_task->getAssignedList();
        $this->edituserstab->userslist->Reload();


        $this->taskstab->setVisible(false);
        $this->edituserstab->setVisible(true);
    }

    public function usersOnRow($row) {
        $user = $row->getDataItem();
        $row->add(new Label('usershortname', $user->shortname));

        $row->add(new ClickLink('usersdelete'))->onClick($this, 'onDeleteUser');
    }

    public function onAddUser($sender) {
        $employee = Employee::load($sender->editassignedto->getValue());
        $this->_users[$employee->employee_id] = $employee;
        $this->edituserstab->userslist->Reload();
    }

    public function onDeleteUser($sender) {
        $user = $sender->getOwner()->getDataItem();
        $this->_users = array_diff_key($this->_users, array($user->employee_id => $this->_users[$user->employee_id]));

        $this->edituserstab->userslist->Reload();
    }

    public function usersSaveOnClick($sender) {
        $this->_task->updateAssignedList($this->_users);
        $this->taskstab->setVisible(true);
        $this->edituserstab->setVisible(false);
    }

    public function usersCancelOnClick($sender) {
        $this->taskstab->setVisible(true);
        $this->edituserstab->setVisible(false);
    }

    //панель ТМЦ
    public function itemsOnClick($sender) {
        $this->_task = $sender->owner->getDataItem();
        $this->_items = $this->_task->getItemsList();
        $this->edititemstab->itemslist->Reload();


        $this->taskstab->setVisible(false);
        $this->edititemstab->setVisible(true);
        $this->edititemstab->itemsform->clean();



        $this->itemsCalcTotal();
    }

    public function itemsOnRow($row) {
        $item = $row->getDataItem();
        $row->add(new Label('itemname', $item->itemname));
        $row->add(new Label('itemqty', $item->qty));
        $row->add(new Label('itemprice', H::fm($item->price)));

        $row->add(new ClickLink('itemdelete'))->onClick($this, 'onDeleteItem');
    }

    public function onAddItem($sender) {
        $form = $this->edititemstab->itemsform;
        $id = $form->edititem->getKey();
        if ($id == 0) {
            $this->setError('Не введено ТМЦ');
            return;
        }
        $item = Stock::load($id);
        $item->qty = $form->editqty->getText();
        $item->price = 100 * $form->editprice->getText();
        $this->_items[$item->stock_id] = $item;
        $this->edititemstab->itemslist->Reload();
        $form->clean();
        $this->itemsCalcTotal();
    }

    public function onDeleteItem($sender) {
        $item = $sender->getOwner()->getDataItem();
        $this->_items = array_diff_key($this->_items, array($item->stock_id => $this->_items[$item->stock_id]));

        $this->edititemstab->itemslist->Reload();
        $this->itemsCalcTotal();
    }

    public function itemsSaveOnClick($sender) {
        $this->_task->updateItemsList($this->_items);
        $this->_task->save();
        $this->taskstab->setVisible(true);
        $this->edititemstab->setVisible(false);
    }

    public function itemsCancelOnClick($sender) {
        $this->taskstab->setVisible(true);
        $this->edititemstab->setVisible(false);
    }

    private function itemsCalcTotal() {
        $total = 0;
        foreach ($this->_items as $tovar) {
            $total = $total + $tovar->price * ($tovar->qty );
        }



        $this->edititemstab->itemsform->itemstotal->setText(H::fm($total));
    }

    public function onChangeItem($sender) {
        $id = $sender->getKey();
        $stock = Stock::load($id);

        $item = Item::load($stock->item_id);
        $this->edititemstab->itemsform->editprice->setText(H::fm($stock->price));

        $this->edititemstab->itemsform->qtystock->setText(Stock::getQuantity($id) / 1000 . ' ' . $stock->measure_name);

        $this->updateAjax(array('editprice', 'qtystock'));
    }

    public function edititemOnAutocomplete($sender) {
        $r = array();

        $text = $sender->getText();
        $list = Stock::findArrayEx("store_id={$this->_store_id} and closed <> 1 and (itemname like " . Stock::qstr('%' . $text . '%') . " or item_code like " . Stock::qstr('%' . $text . '%') . "  ) and item_type=" . Item::ITEM_TYPE_STUFF);
        foreach ($list as $k => $v) {
            $r[$k] = $v;
        }
        return $r;
    }

    //Изменения  на  диаграмме
    public function OnGantt($sender, $eventdata) {

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
        $this->taskstab->tasklist->Reload();
    }

    public function updateGantt() {

        $tasks = array();
        $items = $this->_taskds->getItems();
        foreach ($items as $item) {
            $col = "#00ff00";
            if ($item->priority == 0)
                $col = "#ff0000";
            if ($item->priority == 3)
                $col = "#00ff00";
            if ($item->priority == 5)
                $col = "#ffdd00";
            if ($item->status == 3)
                $col = "#a0a0a0";
            $tasks[] = new \ZCL\Gantt\GanttItem($item->task_id, $item->taskname, $item->start_date, $item->end_date, $col);
        }

        $this->taskstab->gantt->setData($tasks);
    }

    public function OnAutoCustomer($sender) {
        $text = $sender->getText();
        $text = Customer::qstr('%' . $text . '%');
        return Customer::findArray("customer_name", "customer_name like {$text}  ");
    }

}
