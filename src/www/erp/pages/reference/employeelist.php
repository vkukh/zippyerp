<?php

namespace ZippyERP\ERP\Pages\Reference;

use ZCL\DB\EntityDataSource as EDS;
use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\CheckBox;
use Zippy\Html\Form\Date;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Panel;
use ZippyERP\ERP\Entity\Department;
use ZippyERP\ERP\Entity\Employee;
use ZippyERP\ERP\Entity\Position;

class EmployeeList extends \ZippyERP\ERP\Pages\Base
{

    private $_employee;

    public function __construct() {
        parent::__construct();

        $this->add(new Panel('employeetable'))->setVisible(true);
        $this->employeetable->add(new DataView('employeelist', new EDS('\ZippyERP\ERP\Entity\employee'), $this, 'employeelistOnRow'))->Reload();
        $this->employeetable->add(new ClickLink('addnew'))->onClick($this, 'addOnClick');
        $this->add(new Form('employeedetail'))->setVisible(false);

        $this->employeedetail->add(new SubmitButton('save'))->onClick($this, 'saveOnClick');
        $this->employeedetail->add(new Button('cancel'))->onClick($this, 'cancelOnClick');
        $this->employeedetail->add(new DropDownChoice('editdepartment', Department::findArray('department_name', '', 'department_name')));
        $this->employeedetail->add(new DropDownChoice('editposition', Position::findArray('position_name', '', 'position_name')));
        $this->employeedetail->add(new TextInput('editlogin'));
        $this->employeedetail->add(new DropDownChoice('editsalarytype'))->setValue(1);
        $this->employeedetail->add(new DropDownChoice('editexptype'))->setValue(91);
        $this->employeedetail->add(new TextInput('editsalary'));
        $this->employeedetail->add(new TextInput('editavans'));
        $this->employeedetail->add(new TextInput('editinn'));
        $this->employeedetail->add(new TextInput('editlastname'));
        $this->employeedetail->add(new TextInput('editfirstname'));
        $this->employeedetail->add(new TextInput('editmiddlename'));
        $this->employeedetail->add(new TextInput('editemail'));
        $this->employeedetail->add(new TextInput('editphone'));
        $this->employeedetail->add(new Date('editfiredate'));
        $this->employeedetail->add(new Date('edithiredate'));
        $this->employeedetail->add(new CheckBox('editcombined'));
        $this->employeedetail->add(new CheckBox('editinvalid'));
    }

    public function employeelistOnRow($row) {
        $item = $row->getDataItem();

        $row->add(new Label('name', $item->fullname));
        $row->add(new Label('department', $item->department_name));
        $row->add(new Label('position', $item->position_name));
        $row->add(new Label('login', $item->login));
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender) {
        Employee::delete($sender->owner->getDataItem()->employee_id);
        $this->employeetable->employeelist->Reload();
    }

    public function editOnClick($sender) {
        $this->_employee = $sender->owner->getDataItem();
        $this->employeetable->setVisible(false);
        $this->employeedetail->setVisible(true);
        $this->employeedetail->editlogin->setText($this->_employee->login);
        $this->employeedetail->editposition->setValue($this->_employee->position_id);
        $this->employeedetail->editdepartment->setValue($this->_employee->department_id);
        $this->employeedetail->editsalarytype->setValue($this->_employee->salarytype);
        $this->employeedetail->editexptype->setValue($this->_employee->exptype);
        $this->employeedetail->editsalary->setText($this->_employee->salary);
        $this->employeedetail->editinn->setText($this->_employee->inn);
        $this->employeedetail->editfirstname->setText($this->_employee->firstname);
        $this->employeedetail->editmiddlename->setText($this->_employee->middlename);
        $this->employeedetail->editlastname->setText($this->_employee->lastname);
        $this->employeedetail->editphone->setText($this->_employee->phone);
        $this->employeedetail->editemail->setText($this->_employee->email);
        $this->employeedetail->editavans->setText($this->_employee->avans);
        $this->employeedetail->editcombined->setChecked($this->_employee->combined);
        $this->employeedetail->editinvalid->setChecked($this->_employee->invalid);
        $this->employeedetail->editfiredate->setDate($this->_employee->firedate);
        if ($this->_employee->hiredate > 0)
            $this->employeedetail->edithiredate->setDate($this->_employee->hiredate);
    }

    public function addOnClick($sender) {
        $this->employeetable->setVisible(false);
        $this->employeedetail->setVisible(true);
        // Очищаем  форму
        $this->employeedetail->clean();

        $this->employeedetail->edithiredate->setDate(time());


        $this->_employee = new Employee();
    }

    public function saveOnClick($sender) {

        $this->_employee->position_id = $this->employeedetail->editposition->getValue();
        $this->_employee->department_id = $this->employeedetail->editdepartment->getValue();
        $login = trim($this->employeedetail->editlogin->getText());
        if (strlen($login) > 0) {
            if ($login == "admin") {
                $this->setError('Недопустимий логін');
                return;
            }
            $_emp = Employee::getFirst("login = '{$login}'");
            if ($_emp != null && $_emp->employee_id != $this->_employee->employee_id) {
                $this->setError('Логін вже має  ' . $_emp->getInitName());
                return;
            }
            $user = \ZippyERP\System\User::getByLogin($login);
            if ($user == null) {
                $this->setError('Неіснуючий логін');
                return;
            }
        }
        $this->_employee->login = $login;
        $this->_employee->salarytype = $this->employeedetail->editsalarytype->getValue();
        $this->_employee->exptype = $this->employeedetail->editexptype->getValue();
        $this->_employee->salary = $this->employeedetail->editsalary->getText();
        $this->_employee->inn = $this->employeedetail->editinn->getText();
        $this->_employee->middlename = $this->employeedetail->editmiddlename->getText();
        $this->_employee->firstname = $this->employeedetail->editfirstname->getText();
        $this->_employee->lastname = $this->employeedetail->editlastname->getText();
        $this->_employee->phone = $this->employeedetail->editphone->getText();
        $this->_employee->email = $this->employeedetail->editemail->getText();
        $this->_employee->avans = $this->employeedetail->editavans->getText();
        $this->_employee->combined = $this->employeedetail->editcombined->isChecked();
        $this->_employee->invalid = $this->employeedetail->editinvalid->isChecked();
        $this->_employee->firedate = $this->employeedetail->editfiredate->getDate();
        $this->_employee->hiredate = $this->employeedetail->edithiredate->getDate();
        if ($this->_employee->hiredate == 0) {
            $this->setError('Введіть  дату коли прийнятий.');
            return;
        }

        $this->_employee->Save();

        $this->employeedetail->setVisible(false);
        $this->employeetable->setVisible(true);
        $this->employeetable->employeelist->Reload();
    }

    public function cancelOnClick($sender) {
        $this->employeetable->setVisible(true);
        $this->employeedetail->setVisible(false);
    }

}
