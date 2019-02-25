<?php

namespace App\Pages\Doc;

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
use Zippy\Html\Link\SubmitLink;
use App\Entity\Doc\Document;
use App\Entity\Employee;
use App\Helper as H;
use App\Application as App;
use App\System;

/**
 * Страница    начисление зарплаты
 */
class InSalary extends \App\Pages\Base
{

    public $_emplist = array();
    private $_doc;
    private $_rowid = 0;

    public function __construct($docid = 0) {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new CheckBox('isavans'))->onChange($this, "onAvans");
        $this->docform->add(new Date('document_date'))->setDate(time());


        $this->docform->add(new DropDownChoice('year', H::getYears(), date('Y')));
        $this->docform->add(new DropDownChoice('month', H::getMonth(), date('m')));

        $this->docform->add(new SubmitLink('addrow'))->onClick($this, 'addrowOnClick');

        $this->docform->add(new SubmitButton('savedoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new Button('backtolist'))->onClick($this, 'backtolistOnClick');

        $this->add(new Form('editdetail'))->setVisible(false);
        //   $this->editdetail->add(new TextInput('editpayed')) ;
        //    $this->editdetail->add(new TextInput('editamount'));
        $this->editdetail->add(new DropDownChoice('editemployee', Employee::findArray("emp_name", "detail like '%<fired>0</fired>%'  ", "emp_name")))->onChange($this, 'OnChangeEmployee');



        $this->editdetail->add(new TextInput('basesalary', 0));
        $this->editdetail->add(new TextInput('vacation', 0));
        $this->editdetail->add(new TextInput('sick', 0));

        $this->editdetail->add(new TextInput('taxfl', 0));
        $this->editdetail->add(new TextInput('taxecb', 0));
        $this->editdetail->add(new TextInput('taxfot', 0));
        $this->editdetail->add(new TextInput('taxmil', 0));


        $this->editdetail->add(new Button('cancelrow'))->onClick($this, 'cancelrowOnClick');
        $this->editdetail->add(new SubmitButton('submitrow'))->onClick($this, 'saverowOnClick');
        $this->editdetail->add(new SubmitButton('submitcalc'))->onClick($this, 'calcrowOnClick');

        if ($docid > 0) {    //загружаем   содержимок  документа на страницу
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);

            $this->docform->document_date->setDate($this->_doc->document_date);
            $this->docform->isavans->setChecked($this->_doc->headerdata['isavans']);


            $this->docform->year->setValue($this->_doc->headerdata['year']);
            $this->docform->month->setValue($this->_doc->headerdata['month']);

            foreach ($this->_doc->detaildata as $_emp) {
                $emp = new Employee($_emp);
                $this->_emplist[$emp->employee_id] = $emp;
            }
        } else {
            $this->_doc = Document::create('InSalary');
            $this->docform->document_number->setText($this->_doc->nextNumber());
        }

        $this->docform->add(new DataView('detail', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, '_emplist')), $this, 'detailOnRow'))->Reload();
    }

    public function detailOnRow($row) {
        $emp = $row->getDataItem();

        $row->add(new Label('employee', $emp->emp_name));

        $row->add(new Label('rsalary', H::famt($emp->salary)));
        $row->add(new Label('rvacation', H::famt($emp->vacation)));
        $row->add(new Label('rsick', H::famt($emp->sick)));
        $row->add(new Label('recb', H::famt($emp->taxecb)));
        $row->add(new Label('rfl', H::famt($emp->taxfl)));
        $row->add(new Label('rmil', H::famt($emp->taxmil)));
        $row->add(new Label('amount', H::famt($emp->amount)));
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function onAvans($sender) {
        
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
        $this->editdetail->clean();
    }

    public function editOnClick($sender) {

        $emp = $sender->getOwner()->getDataItem();

        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);


        $this->editdetail->editemployee->setValue($emp->employee_id);



        $this->editdetail->basesalary->setText(H::famt($emp->salary));
        $this->editdetail->vacation->setText(H::famt($emp->vacation));
        $this->editdetail->sick->setText(H::famt($emp->sick));
        $this->editdetail->taxfl->setText(H::famt($emp->taxfl));
        $this->editdetail->taxecb->setText(H::famt($emp->taxecb));
        $this->editdetail->taxmil->setText(H::famt($emp->taxmil));
        $this->editdetail->taxfot->setText(H::famt($emp->taxfot));


        $this->_rowid = $emp->employee_id;
    }

    //расчет удержаний
    public function calcrowOnClick($sender) {

        $id = $this->editdetail->editemployee->getValue();
        if ($id == 0) {
            $this->setError("Не выбран сотрудник");
            return;
        }


        /*
          $date = new Carbon();
          $date->year($this->docform->year->getValue());
          $date->month($this->docform->month->getValue());
          $date->endOfMonth();
          $to =  $date->timestamp;
          $from =  $date->startOfMonth()->timestamp -1;
         */
        $emp = Employee::load($id);

        $avans = 0;
        $tax = System::getOptions("tax");

        if ($this->docform->isavans->isChecked() == false) {
            //ищем     аванс

            $list = Document::search($this->_doc->meta_id, null, null, array('year' => $this->docform->year->getValue(), 'month' => $this->docform->month->getValue(), 'isavans' => 1));
            if (count($list) == 0) {
                $this->setError('Не найдено начисление аванса');
                return;
            }
            $list = array_values($list);
            $prevdoc = $list[0];
            foreach ($prevdoc->detaildata as $_emp) {
                if ($_emp['employee_id'] == $emp->employee_id) {
                    $avans = $_emp['salary'];
                }
            }
        }


        $salary = $this->editdetail->basesalary->getText();
        $salary += $this->editdetail->vacation->getText();
        $salary += $this->editdetail->sick->getText();

        $ndfl = $salary * $tax['taxfl'];
        $ecb = $salary * $tax['ecbfot'];
        if ($emp->invalid == 1) {
            $ecb = $salary * $tax['ecbinv'];
        }
        $mil = $salary * $tax['military'];


        if ($avans > 0) {  // была оплата  за первую  половину
            if ($salary + $avans < $tax['minnsl']) { //НСЛ
                $salary = $salary - $tax['nsl'];
                if ($salary < 0) {
                    $this->setWarn("НДФЛ: " . H::famt($salary * $tax['taxfl'] / 100));
                    $salary = 0;
                }
                $ndfl = $salary * $tax['taxfl'];
            };
            if ($salary + $avans < $tax['minsalary']) {
                $ecb = $tax['minsalary'];
                if ($emp->invalid == 1) {
                    $ecb = $salary * $tax['ecbinv'];
                }
            }
        }

        $this->editdetail->taxfl->setText(H::famt($ndfl));
        $this->editdetail->taxecb->setText(H::famt($ecb));
        $this->editdetail->taxmil->setText(H::famt($mil));
        $this->editdetail->taxfot->setText(H::famt($salary + $avans));
    }

    public function saverowOnClick($sender) {
        $id = $this->editdetail->editemployee->getValue();
        if ($id == 0) {
            $this->setError("Не выбран сотрудник");
            return;
        }


        $emp = Employee::load($id);

        $emp->salary = $this->editdetail->basesalary->getText();
        $emp->vacation = $this->editdetail->vacation->getText();
        $emp->sick = $this->editdetail->sick->getText();
        $emp->taxfl = $this->editdetail->taxfl->getText();
        $emp->taxecb = $this->editdetail->taxecb->getText();
        $emp->taxmil = $this->editdetail->taxmil->getText();
        $emp->taxfot = $this->editdetail->taxfot->getText();

        $emp->amount = $emp->salary - $emp->taxfl - $emp->taxmil;


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

        $amount = 0;

        $this->_doc->headerdata = array(
            'isavans' => $this->docform->isavans->isChecked() ? 1 : 0,
            'year' => $this->docform->year->getValue(),
            'month' => $this->docform->month->getValue()
        );
        $this->_doc->detaildata = array();
        foreach ($this->_emplist as $emp) {
            $this->_doc->detaildata[] = $emp->getData();
            $amount += $emp->amount;
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
            $this->setError("Не вибраний ні один  співробітник");
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
        //$amount = 0;


        if ($this->docform->isavans->isChecked()) {
            $this->editdetail->basesalary->setText(H::famt($emp->avans));
        } else {
            if ($emp->stype == 0) { //ставка
                $this->editdetail->basesalary->setText(H::famt($emp->salary - $emp->avans));
            }
        }


        $this->updateAjax(array('basesalary'));
    }

}
