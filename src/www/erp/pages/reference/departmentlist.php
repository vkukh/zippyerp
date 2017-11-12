<?php

namespace ZippyERP\ERP\Pages\Reference;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Panel;
use ZippyERP\ERP\Entity\Department;

class DepartmentList extends \ZippyERP\ERP\Pages\Base
{

    private $_department;

    public function __construct()
    {
        parent::__construct();

        $this->add(new Panel('departmenttable'))->setVisible(true);
        $this->departmenttable->add(new DataView('departmentlist', new \ZCL\DB\EntityDataSource('\ZippyERP\ERP\Entity\Department'), $this, 'departmentlistOnRow'))->Reload();
        $this->departmenttable->add(new ClickLink('addnew'))->onClick($this, 'addOnClick');
        $this->add(new Form('departmentdetail'))->setVisible(false);
        $this->departmentdetail->add(new TextInput('editdepartmentname'));
        $this->departmentdetail->add(new SubmitButton('save'))->onClick($this, 'saveOnClick');
        $this->departmentdetail->add(new Button('cancel'))->onClick($this, 'cancelOnClick');
    }

    public function departmentlistOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('department_name', $item->department_name));
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender)
    {
        Department::delete($sender->owner->getDataItem()->department_id);
        $this->departmenttable->departmentlist->Reload();
    }

    public function editOnClick($sender)
    {
        $this->_department = $sender->owner->getDataItem();
        $this->departmenttable->setVisible(false);
        $this->departmentdetail->setVisible(true);
        $this->departmentdetail->editdepartmentname->setText($this->_department->department_name);
    }

    public function addOnClick($sender)
    {
        $this->departmenttable->setVisible(false);
        $this->departmentdetail->setVisible(true);
        // Очищаем  форму
        $this->departmentdetail->clean();

        $this->_department = new Department();
    }

    public function saveOnClick($sender)
    {
        $this->_department->department_name = $this->departmentdetail->editdepartmentname->getText();
        if ($this->_department->department_name == '') {
            $this->setError("Введите имя");
            return;
        }

        $this->_department->Save();
        $this->departmentdetail->setVisible(false);
        $this->departmenttable->setVisible(true);
        $this->departmenttable->departmentlist->Reload();
    }

    public function cancelOnClick($sender)
    {
        $this->departmenttable->setVisible(true);
        $this->departmentdetail->setVisible(false);
    }

}
