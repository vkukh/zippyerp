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
use \ZippyERP\ERP\Entity\Employee;
use \Zippy\Html\DataList\Paginator;

class EmployeeList extends \ZippyERP\ERP\Pages\Base
{

    private $_employee;

    public function __construct()
    {
        parent::__construct();

        $this->add(new Panel('employeetable'))->setVisible(true);
        $this->employeetable->add(new DataView('employeelist', new \ZCL\DB\EntityDataSource('\ZippyERP\ERP\Entity\employee'), $this, 'employeelistOnRow'))->Reload();
        $this->employeetable->add(new ClickLink('add'))->setClickHandler($this, 'addOnClick');
        $this->add(new Form('employeedetail'))->setVisible(false);
        $this->employeedetail->add(new TextInput('editfirstname'));
        $this->employeedetail->add(new SubmitButton('save'))->setClickHandler($this, 'saveOnClick');
        $this->employeedetail->add(new Button('cancel'))->setClickHandler($this, 'cancelOnClick');
    }

    public function employeelistOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('firstname', $item->getShortName()));
        $row->add(new ClickLink('edit'))->setClickHandler($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->setClickHandler($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender)
    {
        employee::delete($sender->owner->getDataItem()->employee_id);
        $this->employeetable->employeelist->Reload();
    }

    public function editOnClick($sender)
    {
        $this->_employee = $sender->owner->getDataItem();
        $this->employeetable->setVisible(false);
        $this->employeedetail->setVisible(true);
        $this->employeedetail->editfirstname->setText($this->_employee->firstname);
    }

    public function addOnClick($sender)
    {
        $this->employeetable->setVisible(false);
        $this->employeedetail->setVisible(true);
        // Очищаем  форму
        $this->employeedetail->clean();

        $this->_employee = new employee();
    }

    public function saveOnClick($sender)
    {
        $this->_employee->firstname = $this->employeedetail->editfirstname->getText();
        if ($this->_employee->firstname == '') {
            $this->setError("Введите имя");
            return;
        }

        $this->_employee->Save();
        $this->employeedetail->setVisible(false);
        $this->employeetable->setVisible(true);
        $this->employeetable->employeelist->Reload();
    }

    public function cancelOnClick($sender)
    {
        $this->employeetable->setVisible(true);
        $this->employeedetail->setVisible(false);
    }

}
