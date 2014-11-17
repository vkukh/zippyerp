<?php

namespace ZippyERP\ERP\Pages\Reference;

use \Zippy\Html\Form\Form;
use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\AutocompleteTextInput;
use \Zippy\Html\Form\TextArea;
use \Zippy\Html\Form\SubmitButton;
use \Zippy\Html\Form\Button;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Form\CheckBox;
use \Zippy\Html\Panel;
use \ZippyERP\ERP\Entity\Employee;
use \ZippyERP\ERP\Entity\Contact;
use \ZippyERP\ERP\Entity\Department;
use \ZippyERP\ERP\Entity\Position;
use \Zippy\Html\DataList\Paginator;
use \ZCL\DB\EntityDataSource as EDS;

class EmployeeList extends \ZippyERP\ERP\Pages\Base
{

    private $_employee;

    public function __construct()
    {
        parent::__construct();

        $this->add(new Panel('employeetable'))->setVisible(true);
        $this->employeetable->add(new DataView('employeelist', new EDS('\ZippyERP\ERP\Entity\employee'), $this, 'employeelistOnRow'))->Reload();
        $this->employeetable->add(new ClickLink('add'))->setClickHandler($this, 'addOnClick');
        $this->add(new Form('employeedetail'))->setVisible(false);
        $this->employeedetail->add(new AutocompleteTextInput('editcontact'))->setAutocompleteHandler($this, "onContact");
        $this->employeedetail->add(new SubmitButton('save'))->setClickHandler($this, 'saveOnClick');
        $this->employeedetail->add(new Button('cancel'))->setClickHandler($this, 'cancelOnClick');
        $this->employeedetail->add(new DropDownChoice('editdepartment', Department::findArray('department_name', '', 'department_name')));
        $this->employeedetail->add(new DropDownChoice('editposition', Position::findArray('position_name', '', 'position_name')));
        $this->employeedetail->add(new TextInput('editlogin'));
        $this->employeedetail->add(new ClickLink('opencontact'))->setClickHandler($this, 'OpenOnClick');
        $this->employeedetail->add(new ClickLink('showcontact'))->setClickHandler($this, 'ShowOnClick');
        $this->employeedetail->add(new ClickLink('addcontact'))->setClickHandler($this, 'AddContactOnClick');

        $this->add(new \ZippyERP\ERP\Blocks\Contact('contactdetail', $this, 'OnDetail'))->setVisible(false);
        $this->add(new \ZippyERP\ERP\Blocks\ContactView('contactview'))->setVisible(false);
    }

    public function employeelistOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('name', $item->fullname));
        $row->add(new Label('department', $item->department_name));
        $row->add(new Label('position', $item->position_name));
        $row->add(new Label('login', $item->login));
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
        $this->employeedetail->editlogin->setText($this->_employee->login);
        $this->employeedetail->editcontact->setText($this->_employee->fullname);
        $this->employeedetail->editcontact->setKey($this->_employee->contact_id);
        $this->employeedetail->editcontact->setAttribute('readonly', 'readonly');
        $this->employeedetail->editposition->setValue($this->_employee->position_id);
        $this->employeedetail->editdepartment->setValue($this->_employee->department_id);
        $this->employeedetail->opencontact->setVisible(true);
        $this->employeedetail->showcontact->setVisible(true);
        $this->employeedetail->addcontact->setVisible(false);
    }

    public function addOnClick($sender)
    {
        $this->employeetable->setVisible(false);
        $this->employeedetail->setVisible(true);
        // Очищаем  форму
        $this->employeedetail->clean();
        $this->employeedetail->editcontact->setAttribute('readonly', null);

        $this->_employee = new Employee();
        $this->employeedetail->opencontact->setVisible(false);
        $this->employeedetail->showcontact->setVisible(false);
        $this->employeedetail->addcontact->setVisible(true);
    }

    public function saveOnClick($sender)
    {
        $this->_employee->contact_id = $this->employeedetail->editcontact->getKey();
        if ($this->_employee->contact_id == 0) {
            $this->setError("Выберите контакт");
            return;
        }

        $this->_employee->position_id = $this->employeedetail->editposition->getValue();
        $this->_employee->department_id = $this->employeedetail->editdepartment->getValue();
        $login = trim($this->employeedetail->editlogin->getText());
        if (strlen($login) > 0) {
            if ($login == "admin") {
                $this->setError('Недопустимое значение логина');
                return;
            }
            $_emp = Employee::getFirst("login = '{$login}'");
            if ($_emp != null && $_emp->employee_id != $this->_employee->employee_id) {
                $this->setError('Логин  занят сотрудником ' . $_emp->getInitName());
                return;
            }
            $user = \ZippyERP\System\User::getByLogin($login);
            if ($user == null) {
                $this->setError('Несуществующий логин');
                return;
            }
        }
        $this->_employee->login = $login;

        $this->_employee->Save();

        $this->employeedetail->setVisible(false);
        $this->employeetable->setVisible(true);
        $this->employeetable->employeelist->Reload();
        $this->contactdetail->setVisible(false);
        $this->contactview->setVisible(false);
    }

    public function cancelOnClick($sender)
    {
        $this->employeetable->setVisible(true);
        $this->employeedetail->setVisible(false);
        $this->contactdetail->setVisible(false);
        $this->contactview->setVisible(false);
    }

    public function onContact($sender)
    {
        $text = $sender->getValue();

        return Contact::findArray("fullname", " employee = 0 and customer = 0  and  fullname  like '%{$text}%' ", "fullname", null, 20);
    }

    //редактирование  контакта
    public function OpenOnClick($sender)
    {
        $contact = Contact::load($this->_employee->contact_id);
        $this->contactdetail->open($contact);
        $this->employeedetail->setVisible(false);
        $this->contactview->setVisible(false);
    }

    //просмотр  контакта
    public function ShowOnClick($sender)
    {
        $contact = Contact::load($this->_employee->contact_id);
        $this->contactview->open($contact);
        $this->contactdetail->setVisible(false);
    }

    // новый  контакт  для  нового  сотрудника
    public function AddContactOnClick($sender)
    {
        $this->contactdetail->open();
        $this->employeedetail->setVisible(false);
    }

    // вызывается  формой  контакта  после  редактирования
    public function OnDetail($saved = false, $id = 0)
    {
        $contact = Contact::load($this->contactdetail->getItem()->contact_id);
        $this->employeedetail->editcontact->setText($contact->fullname);
        $this->employeedetail->editcontact->setKey($contact->contact_id);
        $this->contactdetail->setVisible(false);
        $this->employeedetail->setVisible(true);
        if ($saved) {  //обновляем  данные  сотрудника  из  контакта
            $this->employeetable->employeelist->Reload();
            $this->employeedetail->opencontact->setVisible(true);
            $this->employeedetail->showcontact->setVisible(true);
            $this->employeedetail->addcontact->setVisible(false);
        }
    }

}
