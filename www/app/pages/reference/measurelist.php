<?php

namespace App\Pages\Reference;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Panel;
use App\Entity\Measure;

class MeasureList extends \App\Pages\Base
{

    private $_msr;

    public function __construct() {
        parent::__construct();
        if (false == \App\ACL::checkShowRef('MeasureList'))
            return;

        $this->add(new Panel('msrtable'))->setVisible(true);
        $this->msrtable->add(new DataView('msrlist', new \ZCL\DB\EntityDataSource('\App\Entity\Measure'), $this, 'msrlistOnRow'))->Reload();
        $this->msrtable->add(new ClickLink('addnew'))->onClick($this, 'addOnClick');
        $this->add(new Form('msrdetail'))->setVisible(false);
        $this->msrdetail->add(new TextInput('editmsr_name'));
        $this->msrdetail->add(new TextInput('editmsr_code'));
        $this->msrdetail->add(new SubmitButton('save'))->onClick($this, 'saveOnClick');
        $this->msrdetail->add(new Button('cancel'))->onClick($this, 'cancelOnClick');
    }

    public function msrlistOnRow($row) {
        $item = $row->getDataItem();

        $row->add(new Label('msr_name', $item->measure_name));
        $row->add(new Label('msr_code', $item->measure_code));
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender) {
        if (false == \App\ACL::checkEditRef('MeasureList'))
            return;


        $msr_id = $sender->owner->getDataItem()->measure_id;
        $del= Measure::delete($msr_id)  ;
        if(strlen($del) > 0){
            $this->setError($del);
            return;
        }        
        
       
        $this->msrtable->msrlist->Reload();
    }

    public function editOnClick($sender) {
        $this->_msr = $sender->owner->getDataItem();
        $this->msrtable->setVisible(false);
        $this->msrdetail->setVisible(true);
        $this->msrdetail->editmsr_name->setText($this->_msr->measure_name);
        $this->msrdetail->editmsr_code->setText($this->_msr->measure_code);
    }

    public function addOnClick($sender) {
        $this->msrtable->setVisible(false);
        $this->msrdetail->setVisible(true);
        // Очищаем  форму
        $this->msrdetail->clean();

        $this->_msr = new Measure();
    }

    public function saveOnClick($sender) {
        if (false == \App\ACL::checkEditRef('MeasureList'))
            return;

        $this->_msr->measure_code = $this->msrdetail->editmsr_code->getText();
        $this->_msr->measure_name = $this->msrdetail->editmsr_name->getText();
        if ($this->_msr->measure_name == '') {
            $this->setError("Введите наименование");
            return;
        }

        $this->_msr->Save();
        $this->msrdetail->setVisible(false);
        $this->msrtable->setVisible(true);
        $this->msrtable->msrlist->Reload();
    }

    public function cancelOnClick($sender) {
        $this->msrtable->setVisible(true);
        $this->msrdetail->setVisible(false);
    }

}
