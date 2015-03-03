<?php

namespace ZippyERP\ERP\Pages\Doc;

use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\TextArea;
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
use \ZippyERP\ERP\Helper as H;

/**
 * Документ для ручных  операций
 * и  ввода начальных остатков
 */
class ManualEntry extends \ZippyERP\ERP\Pages\Base
{

    public $_entryarr = array();
    public $_itemarr = array();
    public $_emparr = array();
    public $_carr = array();
    public $_farr = array();
    private $_doc;
    private $_edited = false;

    public function __construct($docid = 0)
    {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new TextArea('description'));
        $this->docform->add(new Date('created'));
        //проводкт
        $this->docform->add(new DataView('acctable', new ArrayDataSource($this, '_entryarr'), $this, 'acctableOnRow'));
        $this->docform->add(new DropDownChoice('e_acclistd', Account::findArray('acc_code', 'acc_code not in(select acc_pid from erp_account_plan)', 'cast(acc_code as char)')));
        $this->docform->add(new DropDownChoice('e_acclistc', Account::findArray('acc_code', 'acc_code not in(select acc_pid from erp_account_plan)', 'cast(acc_code as char)')));
        $this->docform->add(new TextInput('e_accsumma'));
        $this->docform->add(new SubmitButton('addaccbtn'))->setClickHandler($this, 'addaccbtnOnClick');
        //ТМЦ
        $this->docform->add(new DataView('itemtable', new ArrayDataSource($this, '_itemarr'), $this, 'itemtableOnRow'));
        $this->docform->add(new DropDownChoice('e_storelist', Store::findArray('storename', 'store_type='.Store::STORE_TYPE_OPT, 'storename')));
        $this->docform->add(new DropDownChoice('e_itemtypelist'))->setAjaxChangeHandler($this, 'OnAjaxItems');
        $this->docform->add(new DropDownChoice('e_itemlist', Item::findArray('itemname', 'item_type=1', 'itemname')));
        $this->docform->add(new TextInput('e_quantity'));
        $this->docform->add(new TextInput('e_price'));
        $this->docform->add(new DropDownChoice('e_oper'));        
        $this->docform->add(new SubmitButton('additembtn'))->setClickHandler($this, 'additembtnOnClick');
        //Сотрудники
        $this->docform->add(new DataView('emptable', new ArrayDataSource($this, '_emparr'), $this, 'emptableOnRow'));
        $this->docform->add(new DropDownChoice('e_emplist', Employee::findArray('fullname', '', 'fullname')));
        $this->docform->add(new TextInput('e_empamount'));
        $this->docform->add(new SubmitButton('addempbtn'))->setClickHandler($this, 'addempbtnOnClick');
        $this->docform->add(new DropDownChoice('e_empoper'));          
        //контрагенты
        $this->docform->add(new DataView('ctable', new ArrayDataSource($this, '_carr'), $this, 'ctableOnRow'));
        $this->docform->add(new DropDownChoice('e_сlist', Customer::findArray('customer_name', '', 'customer_name')));
        $this->docform->add(new TextInput('e_сamount'));
        $this->docform->add(new SubmitButton('addсbtn'))->setClickHandler($this, 'addсbtnOnClick');
        $this->docform->add(new DropDownChoice('e_сoper'));          
        $this->docform->add(new DropDownChoice('e_сtype'));          
        //Денежные счета
        $this->docform->add(new DataView('ftable', new ArrayDataSource($this, '_farr'), $this, 'ftableOnRow'))->Reload();
        $this->docform->add(new DropDownChoice('e_flist', MoneyFund::findArray('title', '', 'title')));
        $this->docform->add(new TextInput('e_famount'));
        $this->docform->add(new SubmitButton('addfbtn'))->setClickHandler($this, 'addfbtnOnClick');
        $this->docform->add(new DropDownChoice('e_foper'));  
        
        
        
        $this->docform->add(new Button('backtolist'))->setClickHandler($this, 'backtolistOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->setClickHandler($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->setClickHandler($this, 'savedocOnClick');

        if ($docid > 0) {    //загружаем   содержимок  документа на страницу
            $this->_edited = true;
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);
            $this->docform->description->setText($this->_doc->headerdata['description']);
            $this->docform->created->setText(date('Y-m-d', $this->_doc->document_date));

            $this->_entryarr = unserialize(base64_decode($this->_doc->headerdata['entry']));
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
            $this->_doc = Document::create('ManualEntry');
            $this->docform->created->setDate(time());
        }
    }

    public function acctableOnRow($row)
    {
        $entry = $row->getDataItem();

        $row->add(new Label('acccodec', $entry->acc_c == -1 ? "" : $entry->acc_c));
        $row->add(new Label('acccoded', $entry->acc_d == -1 ? "" : $entry->acc_d));
        $row->add(new Label('accvalue', H::fm($entry->amount) . " " . $item->dc));
        $row->add(new ClickLink('delacc'))->setClickHandler($this, 'delaccOnClick');
    }

    public function delaccOnClick($sender)
    {
        $item = $sender->owner->getDataItem();
        $this->_entryarr = array_diff_key($this->_entryarr, array($item->entry_id => $this->_entryarr[$item->entry_id]));
        $this->docform->acctable->Reload();
    }

    public function addaccbtnOnClick($sender)
    {
        $dt = $this->docform->e_acclistd->getValue();
        $ct = $this->docform->e_acclistc->getValue();

        $entry = new \ZippyERP\ERP\Entity\Entry();
        $entry->entry_id = time();
        $entry->acc_c = $ct;
        $entry->acc_d = $dt;
        $entry->amount = 100 * $this->docform->e_accsumma->getText();
        $this->_entryarr[$entry->entry_id] = $entry;
        $this->docform->acctable->Reload();
        $this->docform->e_accsumma->setText('0');
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
        $row->add(new Label('itemop', $item->op == 1 ? "+":"-"));
        $row->add(new Label('itemcode', $item->code));
        $row->add(new Label('store', $item->store_name));
        $row->add(new Label('itemname', $item->itemname));
        $row->add(new Label('itemcnt', $item->qty));
        $row->add(new Label('itemprice', H::fm($item->price)));
        $row->add(new ClickLink('delitem'))->setClickHandler($this, 'delitemOnClick');
    }

    public function delitemOnClick($sender)
    {
        $item = $sender->owner->getDataItem();
        $this->_itemarr = array_diff_key($this->_itemarr, array($item->item_id => $this->_itemarr[$item->item_id]));
        $this->docform->itemtable->Reload();
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
        // $item->partion = 100 * $this->docform->e_price->getText();
        $item->price = 100 * $this->docform->e_price->getText();
        $item->op =  $this->docform->e_oper->getValue();            
        if($item->op == 2){
             $stock = \ZippyERP\ERP\Entity\Stock::getStock($sid,$id,$item->price,false);
             if($stock == null ){
                $this->setError("Не найдена  партия " . H::fm($item->price));
                return;
             }
        }
        
        $this->_itemarr[$id] = $item;
        $this->docform->itemtable->Reload();
        $this->docform->e_quantity->setText('');
        $this->docform->e_price->setText('');
    }

    public function emptableOnRow($row)
    {
        $item = $row->getDataItem();
        $op = $item->op == 1 || $item->op == 3 ? "+":"-" ;
        $op = ($op . ' ') . ($item->op == 1 || $item->op == 2 ? "зарплата":"подотчет") ;
        $row->add(new Label('empop', $op));
        
        $row->add(new Label('empname', $item->fullname));
        $row->add(new Label('empamount', H::fm($item->val)));
        $row->add(new ClickLink('delemp'))->setClickHandler($this, 'delempOnClick');
    }

    public function delempOnClick($sender)
    {
        $item = $sender->owner->getDataItem();
        $this->_emparr = array_diff_key($this->_emparr, array($item->employee_id => $this->_emparr[$item->employee_id]));
        $this->docform->emptable->Reload();
    }

    public function addempbtnOnClick($sender)
    {
        $id = $this->docform->e_emplist->getValue();
        if (isset($this->_emparr[$id])) {
          //  $this->setError('Дублирование строки');
            return;
        }        
        $emp = Employee::load($id);
        $emp->val = 100 * $this->docform->e_empamount->getText();
        $emp->op =  $this->docform->e_empoper->getValue();           
        $this->_emparr[$id] = $emp;
        $this->docform->emptable->Reload();
        $this->docform->e_empamount->setText('');
        
    }

    public function ctableOnRow($row)
    {
        $item = $row->getDataItem();
        $row->add(new Label('cop', $item->op == 1 ? "Долг нам":"Долг наш"));
        $row->add(new Label('ctp', $item->type == 1 ? "Покупатель":"Поставщик"));
        $row->add(new Label('сname', $item->customer_name));
        $row->add(new Label('сamount', $item->val));
        $row->add(new ClickLink('delс'))->setClickHandler($this, 'delсOnClick');
    }

    public function delсOnClick($sender)
    {
        $item = $sender->owner->getDataItem();
        $this->_carr = array_diff_key($this->_carr, array($item->customer_id => $this->_carr[$item->customer_id]));
        $this->docform->ctable->Reload();
    }

    public function addсbtnOnClick($sender)
    {
        $id = $this->docform->e_сlist->getValue();
        if (isset($this->_carr[$id])) {
            $this->setError('Дублирование строки');
            return;
        }
        $c = Customer::load($id);
        $c->val = 100 * $this->docform->e_сamount->getText();
        $c->op =  $this->docform->e_сoper->getValue();        
        $c->type =  $this->docform->e_сtype->getValue();        
        $this->_carr[$id] = $c;
        $this->docform->ctable->Reload();
        $this->docform->e_сamount->setText('');
        
    }

    public function ftableOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('fop', $item->op == 1 ? "+":"-"));
        $row->add(new Label('fname', $item->title));
        $row->add(new Label('famount', $item->val));
        $row->add(new ClickLink('delf'))->setClickHandler($this, 'delfOnClick');
    }

    public function delfOnClick($sender)
    {
        $item = $sender->owner->getDataItem();
        $this->_farr = array_diff_key($this->_farr, array($item->id => $this->_farr[$item->id]));
        $this->docform->ftable->Reload();
    }

    public function addfbtnOnClick($sender)
    {
        $id = $this->docform->e_flist->getValue();
        if (isset($this->_farr[$id])) {
            $this->setError('Дублирование строки');
            return;
        }
        $f = MoneyFund::load($id);
        $f->val = 100 * $this->docform->e_famount->getText();
        $f->op =  $this->docform->e_foper->getValue();
        $this->_farr[$id] = $f;
        $this->docform->ftable->Reload();
        $this->docform->e_famount->setText('');
    }

    public function backtolistOnClick($sender)
    {

        App::RedirectBack();
    }

    public function savedocOnClick($sender)
    {
        $this->_doc->document_date = strtotime($this->docform->created->getText());
        $this->_doc->document_number = $this->docform->document_number->getText();


        $this->_doc->headerdata['entry'] = base64_encode(serialize($this->_entryarr));
        $this->_doc->headerdata['emp'] = base64_encode(serialize($this->_emparr));
        $this->_doc->headerdata['item'] = base64_encode(serialize($this->_itemarr));
        $this->_doc->headerdata['c'] = base64_encode(serialize($this->_carr));
        $this->_doc->headerdata['f'] = base64_encode(serialize($this->_farr));
        $this->_doc->headerdata['description'] = $this->docform->description->getText();

        $this->_doc->save();

        if ($sender->id == 'execdoc') {
            $this->_doc->updateStatus(Document::STATE_EXECUTED);
        } else {
            $this->_doc->updateStatus($this->_edited ? Document::STATE_EDITED : Document::STATE_NEW);
        }
        $this->backtolistOnClick(null);
    }
    
    
}
