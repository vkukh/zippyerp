<?php

namespace ZippyERP\ERP\Pages\Doc;

use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\DataList\DataView;
use \Zippy\Html\DataList\ArrayDataSource;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Form\RadioButton;
use \Zippy\Html\Form\SubmitButton;
use \Zippy\Html\Form\Button;
use \Zippy\Html\Form\Date;
use ZippyERP\ERP\Entity\Doc\Document;
use ZippyERP\ERP\Entity\Account;
use ZippyERP\ERP\Entity\Store;
use ZippyERP\ERP\Entity\Item;
use ZippyERP\ERP\Entity\Employee;
use ZippyERP\ERP\Entity\Customer;
use ZippyERP\ERP\Entity\MoneyFund;
use ZippyERP\System\Application as App;

/**
 * Ввод начальных  остатков 
 */
class StartData extends \ZippyERP\ERP\Pages\Base
{

    public $_accarr = array();
    public $_itemarr = array();
    public $_emparr = array();
    public $_carr = array();
    public $_farr = array();
    public $_saldo = 1;
    private $_doc;

    public function __construct($docid = 0)
    {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new Date('created'));
        $this->docform->add(new DataView('acctable', new ArrayDataSource($this, '_accarr'), $this, 'acctableOnRow'));
        $this->docform->add(new DropDownChoice('e_acclist', Account::findArray('acc_name', '', 'acc_code')));
        $this->docform->add(new RadioButton('e_saldod', new \Zippy\Binding\PropertyBinding($this, '_saldo'), 1, '_saldo'));
        $this->docform->add(new RadioButton('e_saldok', new \Zippy\Binding\PropertyBinding($this, '_saldo'), 2, '_saldo'));
        $this->docform->add(new TextInput('e_accsumma'));
        $this->docform->add(new SubmitButton('addaccbtn'))->setClickHandler($this, 'addaccbtnOnClick');
        $this->docform->add(new DataView('itemtable', new ArrayDataSource($this, '_itemarr'), $this, 'itemtableOnRow'));
        $this->docform->add(new DropDownChoice('e_storelist', Store::findArray('storename', '', 'storename')));
        $this->docform->add(new DropDownChoice('e_itemtypelist'))->setAjaxChangeHandler($this, 'OnAjaxItems');
        ;
        $this->docform->add(new DropDownChoice('e_itemlist', Item::findArray('itemname', 'item_type=1', 'itemname')));
        $this->docform->add(new TextInput('e_quantity'));
        $this->docform->add(new TextInput('e_price'));
        $this->docform->add(new SubmitButton('additembtn'))->setClickHandler($this, 'additembtnOnClick');
        $this->docform->add(new DataView('emptable', new ArrayDataSource($this, '_emparr'), $this, 'emptableOnRow'));
        $this->docform->add(new DropDownChoice('e_emplist', Employee::findArray('fullname', '', 'fullname')));
        $this->docform->add(new TextInput('e_empamount'));
        $this->docform->add(new SubmitButton('addempbtn'))->setClickHandler($this, 'addempbtnOnClick');
        $this->docform->add(new DataView('ctable', new ArrayDataSource($this, '_carr'), $this, 'ctableOnRow'));
        $this->docform->add(new DropDownChoice('e_сlist', Customer::findArray('customer_name', '', 'customer_name')));
        $this->docform->add(new TextInput('e_сamount'));
        $this->docform->add(new SubmitButton('addсbtn'))->setClickHandler($this, 'addсbtnOnClick');
        $this->docform->add(new DataView('ftable', new ArrayDataSource($this, '_farr'), $this, 'ftableOnRow'))->Reload();
        $this->docform->add(new DropDownChoice('e_flist', MoneyFund::findArray('title', '', 'title')));
        $this->docform->add(new TextInput('e_famount'));
        $this->docform->add(new SubmitButton('addfbtn'))->setClickHandler($this, 'addfbtnOnClick');
        $this->docform->add(new Button('backtolist'))->setClickHandler($this, 'backtolistOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->setClickHandler($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->setClickHandler($this, 'savedocOnClick');

        if ($docid > 0) {    //загружаем   содержимок  документа на страницу
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);
            $this->docform->created->setText(date('Y-m-d', $this->_doc->document_date));

            $this->_accarr = unserialize(base64_decode($this->_doc->headerdata['acc']));
            $this->_itemarr = unserialize(base64_decode($this->_doc->headerdata['item']));
            $this->_emparr = unserialize(base64_decode($this->_doc->headerdata['emp']));
            $this->_carr = unserialize(base64_decode($this->_doc->headerdata['c']));
            $this->_farr = unserialize(base64_decode($this->_doc->headerdata['f']));
            $this->docform->acctable->Reload();
            $this->docform->itemtable->Reload();
            $this->docform->emptable->Reload();
            $this->docform->ftable->Reload();
            $this->docform->ctable->Reload();
        } else {
            $this->_doc = Document::create('StartData');
            $this->docform->created->setDate(time());
        }
    }

    public function acctableOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('acccode', $item->acc_code));
        $row->add(new Label('accname', $item->acc_name));
        $row->add(new Label('accvalue', number_format($item->acc_val, 2) . " " . $item->dc));
        $row->add(new ClickLink('delacc'))->setClickHandler($this, 'delaccOnClick');
    }

    public function delaccOnClick($sender)
    {
        $item = $sender->owner->getDataItem();
        $this->_accarr = array_diff_key($this->_accarr, array($item->acc_id => $this->_accarr[$item->acc_id]));
        $this->docform->acctable->Reload();
        $this->goAnkor('a1');
    }

    public function addaccbtnOnClick($sender)
    {
        $id = $this->docform->e_acclist->getValue();
        if (isset($this->_accarr[$id])) {
            $this->setError('Дублирование строки');
            return;
        }
        $item = Account::load($id);
        $item->dc = $this->_saldo == 1 ? '+' : '-';
        $item->acc_val = $this->docform->e_accsumma->getText();
        $this->_accarr[$id] = $item;
        $this->docform->acctable->Reload();
        $item->acc_value = $this->docform->e_accsumma->setText('');
        $this->goAnkor('a1');
    }

    public function OnAjaxItems($sender)
    {
        $type = $sender->getValue();
        $this->docform->e_itemlist->setOptionList(Item::findArray('itemname', 'item_type=' . $type, 'itemname'));
        $this->updateAjax('e_itemlist');
    }

    public function itemtableOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('itemcode', $item->item_id));
        $row->add(new Label('store', $item->store_name));
        $row->add(new Label('itemname', $item->itemname));
        $row->add(new Label('itemcnt', $item->qty));
        $row->add(new Label('itemprice', number_format($item->partion, 2)));
        $row->add(new ClickLink('delitem'))->setClickHandler($this, 'delitemOnClick');
    }

    public function delitemOnClick($sender)
    {
        $item = $sender->owner->getDataItem();
        $this->_itemarr = array_diff_key($this->_itemarr, array($item->item_id => $this->_itemarr[$item->item_id]));
        $this->docform->itemtable->Reload();
        $this->goAnkor('a2');
    }

    public function additembtnOnClick($sender)
    {
        $id = $this->docform->e_itemlist->getValue();
        if (isset($this->_itemarr[$id])) {
            $this->setError('Дублирование строки');
            return;
        }

        $sid = $this->docform->e_storelist->getValue();
        $store = Store::load($sid);
        $item = Item::load($id);
        $item->store_id = $sid;
        $item->store_name = $store->storename;
        $item->qty = $this->docform->e_quantity->getText();
        $item->partion = $this->docform->e_price->getText();
        $item->price = $this->docform->e_price->getText();
        $this->_itemarr[$id] = $item;
        $this->docform->itemtable->Reload();
        $this->docform->e_quantity->setText('');
        $this->docform->e_price->setText('');
        $this->goAnkor('a2');
    }

    public function emptableOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('empname', $item->fullname));
        $row->add(new Label('empamount', number_format($item->val, 2)));
        $row->add(new ClickLink('delemp'))->setClickHandler($this, 'delempOnClick');
    }

    public function delempOnClick($sender)
    {
        $item = $sender->owner->getDataItem();
        $this->_emparr = array_diff_key($this->_emparr, array($item->employee_id => $this->_emparr[$item->employee_id]));
        $this->docform->emptable->Reload();
        $this->goAnkor('a3');
    }

    public function addempbtnOnClick($sender)
    {
        $id = $this->docform->e_emplist->getValue();
        if (isset($this->_emparr[$id])) {
            $this->setError('Дублирование строки');
            return;
        }
        $emp = Employee::load($id);
        $emp->val = $this->docform->e_empamount->getText();
        $this->_emparr[$id] = $emp;
        $this->docform->emptable->Reload();
        $this->docform->e_empamount->setText('');
        $this->goAnkor('a3');
    }

    public function ctableOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('сname', $item->customer_name));
        $row->add(new Label('сamount', $item->val));
        $row->add(new ClickLink('delс'))->setClickHandler($this, 'delсOnClick');
    }

    public function delсOnClick($sender)
    {
        $item = $sender->owner->getDataItem();
        $this->_carr = array_diff_key($this->_carr, array($item->customer_id => $this->_carr[$item->customer_id]));
        $this->docform->ctable->Reload();
        $this->goAnkor('a4');
    }

    public function addсbtnOnClick($sender)
    {
        $id = $this->docform->e_сlist->getValue();
        if (isset($this->_carr[$id])) {
            $this->setError('Дублирование строки');
            return;
        }
        $c = Customer::load($id);
        $c->val = $this->docform->e_сamount->getText();
        $this->_carr[$id] = $c;
        $this->docform->ctable->Reload();
        $this->docform->e_сamount->setText('');
        $this->goAnkor('a4');
    }

    public function ftableOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('fсname', $item->title));
        $row->add(new Label('famount', $item->val));
        $row->add(new ClickLink('delf'))->setClickHandler($this, 'delfOnClick');
    }

    public function delfOnClick($sender)
    {
        $item = $sender->owner->getDataItem();
        $this->_farr = array_diff_key($this->_farr, array($item->id => $this->_farr[$item->id]));
        $this->docform->ftable->Reload();
        $this->goAnkor('a5');
    }

    public function addfbtnOnClick($sender)
    {
        $id = $this->docform->e_flist->getValue();
        if (isset($this->_farr[$id])) {
            $this->setError('Дублирование строки');
            return;
        }
        $f = MoneyFund::load($id);
        $f->val = $this->docform->e_famount->getText();
        $this->_farr[$id] = $f;
        $this->docform->ftable->Reload();
        $this->docform->e_famount->setText('');
        $this->goAnkor('a5');
    }

    public function backtolistOnClick($sender)
    {
        App::Redirect("\\ZippyERP\\ERP\\Pages\\Register\\DocList");
    }

    public function savedocOnClick($sender)
    {
        $this->_doc->document_date = strtotime($this->docform->created->getText());
        $this->_doc->document_number = $this->docform->document_number->getText();


        $this->_doc->headerdata['acc'] = base64_encode(serialize($this->_accarr));
        $this->_doc->headerdata['emp'] = base64_encode(serialize($this->_emparr));
        $this->_doc->headerdata['item'] = base64_encode(serialize($this->_itemarr));
        $this->_doc->headerdata['c'] = base64_encode(serialize($this->_carr));
        $this->_doc->headerdata['f'] = base64_encode(serialize($this->_farr));
        $isEdited = $this->_doc->document_id > 0;

        $this->_doc->save();
        if($sender->id == 'execdoc'){
            $this->_doc->updateStatus(Document::STATE_EXECUTED);
        }else {
            $this->_doc->updateStatus( $isEdited ? Document::STATE_EDITED : Document::STATE_NEW);   
        }        
        App::Redirect('\ZippyERP\ERP\Pages\Register\DocList', $this->_doc->document_id);
    }

}

?>
