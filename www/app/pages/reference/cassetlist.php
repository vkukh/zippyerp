<?php

namespace App\Pages\Reference;

use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\TextArea;
use \Zippy\Html\Form\CheckBox;
use \Zippy\Html\Form\Button;
use \Zippy\Html\Form\SubmitButton;
use \Zippy\Html\Form\Date;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Panel;
use \App\Entity\CAsset;
use \App\Entity\Employee;
use \Zippy\Html\DataList\ArrayDataSource;
use \Zippy\Binding\PropertyBinding as Bind;
use \App\Helper as H;
use \App\System;
use \Zippy\Html\Link\BookmarkableLink;

//справочник  ОС и НМА
class CAssetList extends \App\Pages\Base
{

    private $_item;
    private $_typed = array();
    private $_expenses = array(23 => "Производство", 91 => "Общепроизводственные затраты", 92 => "Административные затраты");

    public function __construct() {
        parent::__construct();
        if (false == \App\ACL::checkShowRef('CAssetList'))
            return;

        $this->_typed['104'] = 'Машины и оборудование';
        $this->_typed['106'] = 'Инструменты';
        $this->_typed['112'] = 'МНМА';
        $this->_typed['12'] = 'Нематериальные активы';

        $this->add(new Form('filter'))->onSubmit($this, 'OnFilter');
        $this->filter->add(new TextInput('searchkey'));
        $this->filter->add(new DropDownChoice('searchemp', Employee::findArray("emp_name", "detail like '%<fired>0</fired>%'  ", "emp_name"), 0));

        $this->add(new Panel('itemtable'))->setVisible(true);
        $this->itemtable->add(new DataView('eqlist', new EQDS($this), $this, 'eqlistOnRow'));
        $this->itemtable->add(new ClickLink('addnew'))->onClick($this, 'addOnClick');
        $this->itemtable->eqlist->setPageSize(25);
        $this->itemtable->add(new \Zippy\Html\DataList\Paginator('pag', $this->itemtable->eqlist));
        $this->itemtable->eqlist->setSelectedClass('table-success');
        $this->itemtable->eqlist->Reload();

        $this->add(new Form('itemdetail'))->setVisible(false);
        $this->itemdetail->add(new TextInput('editname'));
        $this->itemdetail->add(new TextInput('editserial'));
        $this->itemdetail->add(new DropDownChoice('editemp', Employee::findArray('emp_name', "detail like '%<fired>0</fired>%'  ", 'emp_name'), 0));
        $this->itemdetail->add(new DropDownChoice('editacc_code', $this->_typed));
        $this->itemdetail->add(new TextInput('editcode'));
        $this->itemdetail->add(new TextArea('editdescription'));
        $this->itemdetail->add(new DropDownChoice('editexpenses', $this->_expenses));

        $this->itemdetail->add(new DropDownChoice('editgroup'));
        $this->itemdetail->add(new DropDownChoice('editdepreciation'));
        $this->itemdetail->add(new TextInput('editnorma'));
        $this->itemdetail->add(new TextInput('editterm'));
        $this->itemdetail->add(new TextInput('editvalue'));
        $this->itemdetail->add(new TextInput('editcancelvalue'));
        $this->itemdetail->add(new Date('editdatemaint'));

        $this->itemdetail->add(new SubmitButton('save'))->onClick($this, 'OnSubmit');
        $this->itemdetail->add(new Button('cancel'))->onClick($this, 'cancelOnClick');
    }

    public function eqlistOnRow($row) {
        $item = $row->getDataItem();
        $row->add(new Label('ca_name', $item->ca_name));
        $row->add(new Label('code', $item->code));
        $row->add(new Label('serial', $item->serial));

        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender) {
        if (false == \App\ACL::checkEditRef('CAssetList'))
            return;

        $item = $sender->owner->getDataItem();
     
        $del = CAsset::delete($item->ca_id);
        if(strlen($del) > 0){
            $this->setError($del);
            return;
        }

        $this->itemtable->eqlist->Reload();
    }

    public function editOnClick($sender) {
        $this->_item = $sender->owner->getDataItem();
        $this->itemtable->setVisible(false);
        $this->itemdetail->setVisible(true);

        $this->itemdetail->editname->setText($this->_item->ca_name);

        $this->itemdetail->editemp->setValue($this->_item->emp_id);
        $this->itemdetail->editacc_code->setValue($this->_item->acc_code);

        $this->itemdetail->editdescription->setText($this->_item->description);
        $this->itemdetail->editcode->setText($this->_item->code);
        $this->itemdetail->editserial->setText($this->_item->serial);

        $this->itemdetail->editdatemaint->setDate($this->_item->datemaint);
        $this->itemdetail->editterm->setText($this->_item->term);
        $this->itemdetail->editvalue->setText(H::famt($this->_item->value));
        $this->itemdetail->editcancelvalue->setText(H::famt($this->_item->cancelvalue));

        $this->itemdetail->editexpenses->setValue($this->_item->expenses);
        $this->itemdetail->editdepreciation->setValue($this->_item->depreciation);
        $this->itemdetail->editnorma->setValue($this->_item->norma);
        $this->itemdetail->editgroup->setValue($this->_item->group);
    }

    public function addOnClick($sender) {
        $this->itemtable->setVisible(false);
        $this->itemdetail->setVisible(true);
        // Очищаем  форму
        $this->itemdetail->clean();
        $this->_item = new CAsset();
    }

    public function cancelOnClick($sender) {
        $this->itemtable->setVisible(true);
        $this->itemdetail->setVisible(false);
    }

    public function OnFilter($sender) {
        $this->itemtable->eqlist->Reload();
    }

    public function OnSubmit($sender) {
        if (false == \App\ACL::checkEditRef('CAssetLists'))
            return;

        $this->itemtable->setVisible(true);
        $this->itemdetail->setVisible(false);

        $this->_item->ca_name = $this->itemdetail->editname->getText();
        $this->_item->acc_code = $this->itemdetail->editacc_code->getValue();
        $this->_item->emp_id = $this->itemdetail->editemp->getValue();
        $this->_item->emp_name = $this->itemdetail->editemp->getValueName();

        $this->_item->code = $this->itemdetail->editcode->getText();

        $this->_item->serial = $this->itemdetail->editserial->getText();
        $this->_item->description = $this->itemdetail->editdescription->getText();
        $this->_item->depreciation = $this->itemdetail->editdepreciation->getValue();
        $this->_item->cancelvalue = $this->itemdetail->editcancelvalue->getText();
        $this->_item->value = $this->itemdetail->editvalue->getText();
        $this->_item->term = $this->itemdetail->editterm->getText();

        $this->_item->datemaint = $this->itemdetail->editdatemaint->getDate();

        $this->_item->expenses = $this->itemdetail->editexpenses->getValue();
        $this->_item->norma = $this->itemdetail->editnorma->getText();
        $this->_item->group = $this->itemdetail->editgroup->getValue();



        $this->_item->Save();

        $this->itemtable->eqlist->Reload();
    }

}

class EQDS implements \Zippy\Interfaces\DataSource
{

    private $page;

    public function __construct($page) {
        $this->page = $page;
    }

    private function getWhere() {

        $form = $this->page->filter;
        $where = "1=1";
        $text = trim($form->searchkey->getText());
        $emp = $form->searchemp->getValue();

        if ($emp > 0) {
            $where = $where . " and detail like '%<emp_id>{$emp}</emp_id>%' ";
        }
        if (strlen($text) > 0) {
            $text = CAsset::qstr('%' . $text . '%');
            $where = $where . " and (ca_name like {$text} or detail like {$text} )  ";
        }
        return $where;
    }

    public function getItemCount() {
        return CAsset::findCnt($this->getWhere());
    }

    public function getItems($start, $count, $sortfield = null, $asc = null) {
        return CAsset::find($this->getWhere(), "ca_name asc", $count, $start);
    }

    public function getItem($id) {
        return CAsset::load($id);
    }

}
