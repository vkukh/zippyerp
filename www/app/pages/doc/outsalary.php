<?php

namespace App\Pages\Doc;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\Date;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Link\SubmitLink;
use App\Entity\Doc\Document;
use App\Entity\Employee;
use App\Helper as H;
use App\Application as App;

/**
 * Страница    выплата зарплаты
 */
class OutSalary extends \App\Pages\Base
{

    public $_emplist = array();
    private $_doc;
    private $_rowid = 0;

    public function __construct($docid = 0, $basedocid = 0) {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new Date('document_date'))->setDate(time());


        $this->docform->add(new DropDownChoice('year', H::getYears(), date('Y')));
        $this->docform->add(new DropDownChoice('month', H::getMonth(), date('m')));

        $this->docform->add(new SubmitLink('addrow'))->onClick($this, 'addrowOnClick');
        $this->docform->add(new SubmitLink('loadall'))->onClick($this, 'loadallOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new Button('backtolist'))->onClick($this, 'backtolistOnClick');

        $this->add(new Form('editdetail'))->setVisible(false);
        $this->editdetail->add(new TextInput('editpayed'));
        $this->editdetail->add(new TextInput('editamount'));
        $this->editdetail->add(new DropDownChoice('editemployee', Employee::findArray("emp_name", "detail like '%<fired>0</fired>%'  ", "emp_name")))->onChange($this, 'OnChangeEmployee');


        $this->editdetail->add(new Button('cancelrow'))->onClick($this, 'cancelrowOnClick');
        $this->editdetail->add(new SubmitButton('submitrow'))->onClick($this, 'saverowOnClick');

        if ($docid > 0) {    //загружаем   содержимок  документа на страницу
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);

            $this->docform->document_date->setDate($this->_doc->document_date);

            $this->docform->year->setValue($this->_doc->headerdata['year']);
            $this->docform->month->setValue($this->_doc->headerdata['month']);

            foreach ($this->_doc->detaildata as $_emp) {
                $emp = new Employee($_emp);
                $this->_emplist[$emp->employee_id] = $emp;
            }
        } else {
            $this->_doc = Document::create('OutSalary');
            $this->docform->document_number->setText($this->_doc->nextNumber());
            if ($basedocid > 0) {  //создание на  основании
                $basedoc = Document::load($basedocid);
                if ($basedoc instanceof Document) {
                    $this->_basedocid = $basedocid;

                    if ($basedoc->meta_name == 'InSalary') {


                        $this->docform->year->setValue($basedoc->headerdata['year']);
                        $this->docform->month->setValue($basedoc->headerdata['month']);


                        foreach ($basedoc->detaildata as $_emp) {
                            $emp = new Employee($_emp);
                            $emp->payed = $emp->amount;
                            $this->_emplist[$emp->employee_id] = $emp;
                        }
                    }
                }
            }
        }

        $this->docform->add(new DataView('detail', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, '_emplist')), $this, 'detailOnRow'))->Reload();
    }

    public function detailOnRow($row) {
        $item = $row->getDataItem();

        $row->add(new Label('employee', $item->fullname));

        $row->add(new Label('payed', H::famt($item->payed)));
        $row->add(new Label('amount', H::famt($item->amount)));
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function loadallOnClick($sender) {
        $list = Employee::find("detail like '%<fired>0</fired>%'  ", "emp_name");
        $this->_emplist = array();
        foreach ($list as $emp) {

            $emp->amount = abs($emp->getForPayed($this->docform->document_date->getDate()));

            $emp->payed = $emp->amount;
            if ($emp->payed == 0)
                continue;
            $this->_emplist[$emp->employee_id] = $emp;
        }
        $this->docform->detail->Reload();
    }

    public function deleteOnClick($sender) {
        $emp = $sender->owner->getDataItem();

        $this->_emplist = array_diff_key($this->_emplist, array($emp->employee_id => $this->_emplist[$emp->employee_id]));
        $this->docform->detail->Reload();
    }

    public function addrowOnClick($sender) {
        $this->_os = $sender->id == "addrowos";
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);
        $this->_rowid = 0;
        //очищаем  форму
        $this->editdetail->editemployee->setValue(0);

        $this->editdetail->editamount->setText("0");
        $this->editdetail->editpayed->setText("0");
    }

    public function editOnClick($sender) {

        $emp = $sender->getOwner()->getDataItem();

        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);

        $this->editdetail->editamount->setText(H::famt($emp->amount));
        $this->editdetail->editpayed->setText(H::famt($emp->payed));

        $this->editdetail->editemployee->setValue($emp->employee_id);



        $this->_rowid = $emp->employee_id;
    }

    public function saverowOnClick($sender) {
        $id = $this->editdetail->editemployee->getValue();
        if ($id == 0) {
            $this->setError("Не вибраний співробітник");
            return;
        }


        $emp = Employee::load($id);


        $emp->amount = 100 * $this->editdetail->editamount->getText();
        $emp->payed = 100 * $this->editdetail->editpayed->getText();


        unset($this->_emplist[$this->_rowid]);
        $this->_emplist[$emp->employee_id] = $emp;
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
        $this->docform->detail->Reload();
    }

    public function cancelrowOnClick($sender) {
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
    }

    public function savedocOnClick($sender) {
        if ($this->checkForm() == false) {
            return;
        }



        $this->_doc->headerdata = array(
            'year' => $this->docform->year->getValue(),
            'month' => $this->docform->month->getValue()
        );
        $amount = 0;
        $this->_doc->detaildata = array();
        foreach ($this->_emplist as $emp) {
            if ($emp->payed > 0) {
                $this->_doc->detaildata[] = $emp->getData();
                $amount += $emp->payed;
            }
        }


        $this->_doc->document_number = $this->docform->document_number->getText();
        $this->_doc->document_date = $this->docform->document_date->getDate();
        $this->_doc->amount = $amount;
        $isEdited = $this->_doc->document_id > 0;


        $conn = \ZDB\DB::getConnect();
        $conn->BeginTrans();
        try {
            $this->_doc->save();
            if ($sender->id == 'execdoc') {
                $this->_doc->updateStatus(Document::STATE_EXECUTED);
            } else {
                $this->_doc->updateStatus($isEdited ? Document::STATE_EDITED : Document::STATE_NEW);
            }

            $conn->CommitTrans();
            App::RedirectBack();
        } catch (\Exception $ee) {
            global $logger;
            $conn->RollbackTrans();
            $this->setError("Помилка запису документу. Деталізація в лог файлі  ");

            $logger->error($ee);
            return;
        }
    }

    /**
     * Валидация   формы
     *
     */
    private function checkForm() {

        if (count($this->_emplist) == 0) {
            $this->setError("Не введено жодного співробітника");
        }

        return !$this->isError();
    }

    public function backtolistOnClick($sender) {
        App::RedirectBack();
    }

    public function OnChangeEmployee($sender) {
        if ($this->_os)
            return;
        $id = $sender->getValue();
        $emp = Employee::load($id);
        $amount = abs($emp->getForPayed($this->docform->document_date->getDate()));
        $this->editdetail->editamount->setText(H::famt($amount));
        $this->editdetail->editpayed->setText(H::famt($amount));


        $this->updateAjax(array('editamount', "editpayed"));
    }

}
