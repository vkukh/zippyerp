<?php

namespace App\Pages\Reference;

use ZCL\DB\EntityDataSource as EDS;
use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\CheckBox;
use Zippy\Html\Form\Date;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Form\TextArea;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Panel;
use App\Entity\Employee;

class EmployeeList extends \App\Pages\Base
{

    private $_employee;

    public function __construct() {
        parent::__construct();
        if (false == \App\ACL::checkShowRef('EmployeeList'))
            return;

        $this->add(new Panel('employeetable'))->setVisible(true);
        $this->employeetable->add(new DataView('employeelist', new EDS('\App\Entity\Employee'), $this, 'employeelistOnRow'))->Reload();
        $this->employeetable->add(new ClickLink('addnew'))->onClick($this, 'addOnClick');
        $this->add(new Form('employeedetail'))->setVisible(false);

        $this->employeedetail->add(new SubmitButton('save'))->onClick($this, 'saveOnClick');
        $this->employeedetail->add(new Button('cancel'))->onClick($this, 'cancelOnClick');
        $this->employeedetail->add(new TextInput('editlogin'));
        $this->employeedetail->add(new TextInput('editemp_name'));
        $this->employeedetail->add(new TextInput('editemail'));
        $this->employeedetail->add(new TextArea('editcomment'));
        $this->employeedetail->add(new TextInput('editphone'));
        $this->employeedetail->add(new Date('edithiredate'));
        $this->employeedetail->add(new Date('editfiredate'));
        $this->employeedetail->add(new TextInput('editinn'));
        $this->employeedetail->add(new CheckBox('editfired'));
        $this->employeedetail->add(new CheckBox('editinvalid'));
        $this->employeedetail->add(new CheckBox('editcombined'));
        $this->employeedetail->add(new TextInput('editsalary'));
        $this->employeedetail->add(new TextInput('editavans'));
        $this->employeedetail->add(new DropDownChoice('editstype', array(0 => 'Оклад', 1 => 'Почасовка', 2 => 'Сдельная')));
        $this->employeedetail->add(new DropDownChoice('editexpense', array(91 => 'Общепроизводственные затраты', 92 => 'Адмистративные затраты', 23 => 'Прямые производствинные  затраты')));
    }

    public function employeelistOnRow($row) {
        $item = $row->getDataItem();
        $row->setAttribute('style', $item->fired == 1 ? 'color: #aaa' : null);

        $row->add(new Label('emp_name', $item->emp_name));
        $row->add(new Label('phone', $item->phone));
        $row->add(new Label('login', $item->login));
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender) {
        if (false == \App\ACL::checkEditRef('EmployeeList'))
            return;

        $del = Employee::delete($sender->owner->getDataItem()->employee_id);
        if(strlen($del) > 0){
            $this->setError($del);
            return;
        }   

        $this->employeetable->employeelist->Reload();
    }

    public function addOnClick($sender) {
        $this->employeetable->setVisible(false);
        $this->employeedetail->setVisible(true);
        // Очищаем  форму
        $this->employeedetail->clean();


        $this->_employee = new Employee();
    }

    public function editOnClick($sender) {
        $this->_employee = $sender->owner->getDataItem();
        $this->employeetable->setVisible(false);
        $this->employeedetail->setVisible(true);
        $this->employeedetail->editlogin->setText($this->_employee->login);
        $this->employeedetail->editemp_name->setText($this->_employee->emp_name);
        $this->employeedetail->editcomment->setText($this->_employee->comment);
        $this->employeedetail->editemail->setText($this->_employee->email);
        $this->employeedetail->editphone->setText($this->_employee->phone);
        $this->employeedetail->editinn->setText($this->_employee->inn);
        $this->employeedetail->edithiredate->setDate($this->_employee->hiredate);
        $this->employeedetail->editfiredate->setDate($this->_employee->firedate);
        $this->employeedetail->editsalary->setText($this->_employee->salary);
        $this->employeedetail->editavans->setText($this->_employee->avans);
        $this->employeedetail->editfired->setChecked($this->_employee->fired);
        $this->employeedetail->editinvalid->setChecked($this->_employee->invalid);
        $this->employeedetail->editcombined->setChecked($this->_employee->combined);
        $this->employeedetail->editstype->setValue($this->_employee->stype);
        $this->employeedetail->editexpense->setValue($this->_employee->expense);
    }

    public function saveOnClick($sender) {
        if (false == \App\ACL::checkEditRef('EmployeeList'))
            return;

        $login = trim($this->employeedetail->editlogin->getText());
        if (strlen($login) > 0) {
            if ($login == "admin") {
                $this->setError('Недопустимый логин');
                return;
            }
            $_emp = Employee::getFirst("login = '{$login}'");
            if ($_emp != null && $_emp->employee_id != $this->_employee->employee_id) {
                $this->setError('Логин уже назначен  ' . $_emp->emp_name());
                return;
            }
            $user = \App\Entity\User::getByLogin($login);
            if ($user == null) {
                $this->setError('Несуществующий логин');
                return;
            }
        }
        $this->_employee->login = $login;
        $this->_employee->emp_name = $this->employeedetail->editemp_name->getText();
        $this->_employee->email = $this->employeedetail->editemail->getText();
        $this->_employee->phone = $this->employeedetail->editphone->getText();
        $this->_employee->comment = $this->employeedetail->editcomment->getText();
        $this->_employee->inn = $this->employeedetail->editinn->getText();
        $this->_employee->salary = $this->employeedetail->editsalary->getText();
        $this->_employee->avans = $this->employeedetail->editavans->getText();
        $this->_employee->hiredate = $this->employeedetail->edithiredate->getDate();
        $this->_employee->firedate = $this->employeedetail->editfiredate->getDate();
        $this->_employee->fired = $this->employeedetail->editfired->isChecked() ? 1 : 0;
        $this->_employee->invalid = $this->employeedetail->editinvalid->isChecked() ? 1 : 0;
        $this->_employee->combined = $this->employeedetail->editcombined->isChecked() ? 1 : 0;
        $this->_employee->stype = $this->employeedetail->editstype->getValue();
        $this->_employee->expense = $this->employeedetail->editexpense->getValue();

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
