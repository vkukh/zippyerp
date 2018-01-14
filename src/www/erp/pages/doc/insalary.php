<?php

namespace ZippyERP\ERP\Pages\Doc;

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
use ZippyERP\ERP\Entity\Doc\Document;
use ZippyERP\ERP\Entity\Employee;
use ZippyERP\ERP\Helper as H;
use Zippy\WebApplication as App;
use ZippyERP\System\System;

/**
 * Страница    начисление зарплаты
 */
class InSalary extends \ZippyERP\ERP\Pages\Base
{

    public $_emplist = array();
    private $_doc;
    private $_rowid = 0;

    public function __construct($docid = 0)
    {
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
        $this->editdetail->add(new DropDownChoice('editemployee',Employee::findArray("fullname", " hiredate is not null  ","fullname")))->onChange($this, 'OnChangeEmployee');
        


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

    public function detailOnRow($row)
    {
        $emp = $row->getDataItem();

        $row->add(new Label('employee', $emp->fullname));

        $row->add(new Label('rsalary', H::fm($emp->salary)));
        $row->add(new Label('rvacation', H::fm($emp->vacation)));
        $row->add(new Label('rsick', H::fm($emp->sick)));
        $row->add(new Label('recb', H::fm($emp->taxecb)));
        $row->add(new Label('rfl', H::fm($emp->taxfl)));
        $row->add(new Label('rmil', H::fm($emp->taxmil)));
        $row->add(new Label('amount', H::fm($emp->amount)));
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function onAvans($sender)
    {
        
    }

    public function deleteOnClick($sender)
    {
        $emp = $sender->owner->getDataItem();

        $this->_emplist = array_diff_key($this->_emplist, array($emp->employee_id => $this->_emplist[$emp->employee_id]));
        $this->docform->detail->Reload();
    }

    public function addrowOnClick($sender)
    {
        $this->_os = $sender->id == "addrowos";
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);
        $this->_rowid = 0;
        //очищаем  форму
        $this->editdetail->clean();
    }

    public function editOnClick($sender)
    {

        $emp = $sender->getOwner()->getDataItem();

        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);


        $this->editdetail->editemployee->setValue($emp->employee_id);
        


        $this->editdetail->basesalary->setText(H::fm($emp->salary));
        $this->editdetail->vacation->setText(H::fm($emp->vacation));
        $this->editdetail->sick->setText(H::fm($emp->sick));
        $this->editdetail->taxfl->setText(H::fm($emp->taxfl));
        $this->editdetail->taxecb->setText(H::fm($emp->taxecb));
        $this->editdetail->taxmil->setText(H::fm($emp->taxmil));
        $this->editdetail->taxfot->setText(H::fm($emp->taxfot));


        $this->_rowid = $emp->employee_id;
    }

    //расчет удержаний
    public function calcrowOnClick($sender)
    {

        $id = $this->editdetail->editemployee->getValue();
        if ($id == 0) {
            $this->setError("Не вибраний співробітник");
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

            $list = Document::search($this->_doc->type_id, null, null, array('year' => $this->docform->year->getValue(), 'month' => $this->docform->month->getValue(), 'isavans' => 1));
            if (count($list) == 0) {
                $this->setError('Не знайлено начислення авансу');
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


        $salary = 100 * $this->editdetail->basesalary->getText();
        $salary += 100 * $this->editdetail->vacation->getText();
        $salary += 100 * $this->editdetail->sick->getText();

        $ndfl = $salary * $tax['taxfl'] / 100;
        $ecb = $salary * $tax['ecbfot'] / 100;
        if ($emp->invalid == 1) {
            $ecb = $salary * $tax['ecbinv'] / 100;
        }
        $mil = $salary * $tax['military'] / 100;


        if ($avans > 0) {  // была оплата  за первую  половину
            if ($salary + $avans < $tax['minnsl']) { //НСЛ
                $salary = $salary - $tax['nsl'];
                if ($salary < 0) {
                    $this->setWarn("НДФЛ: " . H::fm($salary * $tax['taxfl'] / 100));
                    $salary = 0;
                }
                $ndfl = $salary * $tax['taxfl'] / 100;
            };
            if ($salary + $avans < $tax['minsalary']) {
                $ecb = $tax['minsalary'] / 100;
                if ($emp->invalid == 1) {
                    $ecb = $salary * $tax['ecbinv'] / 100;
                }
            }
        }

        $this->editdetail->taxfl->setText(H::fm($ndfl));
        $this->editdetail->taxecb->setText(H::fm($ecb));
        $this->editdetail->taxmil->setText(H::fm($mil));
        $this->editdetail->taxfot->setText(H::fm($salary + $avans));
    }

    public function saverowOnClick($sender)
    {
        $id = $this->editdetail->editemployee->getValue();
        if ($id == 0) {
            $this->setError("Не вибраний співробітник");
            return;
        }


        $emp = Employee::load($id);

        $emp->salary = 100 * $this->editdetail->basesalary->getText();
        $emp->vacation = 100 * $this->editdetail->vacation->getText();
        $emp->sick = 100 * $this->editdetail->sick->getText();
        $emp->taxfl = 100 * $this->editdetail->taxfl->getText();
        $emp->taxecb = 100 * $this->editdetail->taxecb->getText();
        $emp->taxmil = 100 * $this->editdetail->taxmil->getText();
        $emp->taxfot = 100 * $this->editdetail->taxfot->getText();

        $emp->amount = $emp->salary - $emp->taxfl - $emp->taxmil;


        unset($this->_emplist[$this->_rowid]);
        $this->_emplist[$emp->employee_id] = $emp;
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
        $this->docform->detail->Reload();
    }

    public function cancelrowOnClick($sender)
    {
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
    }

    public function savedocOnClick($sender)
    {
        if ($this->checkForm() == false) {
            return;
        }

        $amount = 0;

        $this->_doc->headerdata = array(
            'isavans' => $this->docform->isavans->isChecked(),
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
        } catch (\ZippyERP\System\Exception $ee) {
            $conn->RollbackTrans();
            $this->setError($ee->getMessage());
        } catch (\Exception $ee) {
            $conn->RollbackTrans();
            throw new \Exception($ee->getMessage());
        }
    }

    /**
     * Расчет  итого
     *
     */
    private function calcTotal()
    {
        
    }

    /**
     * Валидация   формы
     *
     */
    private function checkForm()
    {

        if (count($this->_emplist) == 0) {
            $this->setError("Не вибраний ні один  співробітник");
        }


        return !$this->isError();
    }

    public function beforeRender()
    {
        parent::beforeRender();

        $this->calcTotal();
    }

    public function backtolistOnClick($sender)
    {
        App::RedirectBack();
    }

    

    public function OnChangeEmployee($sender)
    {
        if ($this->_os)
            return;
        $id = $sender->getValue();
        $emp = Employee::load($id);
        //$amount = 0;


        if ($this->docform->isavans->isChecked()) {
            $this->editdetail->basesalary->setText(H::fm($emp->avans));
        } else {
            if ($emp->salarytype == 1) { //ставка
                $this->editdetail->basesalary->setText(H::fm($emp->salary - $emp->avans));
            }
        }


        $this->updateAjax(array('basesalary'));
    }

}
