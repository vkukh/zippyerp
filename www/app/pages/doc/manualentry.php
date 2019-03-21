<?php

namespace App\Pages\Doc;

use Zippy\Binding\PropertyBinding as Bind;
use Zippy\Html\DataList\ArrayDataSource;
use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\Date;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextArea;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use App\Entity\Account;
use App\Entity\Customer;
use App\Entity\Doc\Document;
use App\Entity\Employee;
use App\Entity\Item;
use App\Entity\CAsset;
use App\Entity\AccountEntry;
use App\Entity\Store;
use Zippy\Html\Form\AutocompleteTextInput;
use App\Helper as H;
use App\Application as App;

/**
 * Документ для ручных  операций
 * и  ввода начальных остатков
 */
class ManualEntry extends \App\Pages\Base
{

    public $_entryarr = array();
    public $_itemarr = array();
    public $_emparr = array();
    public $_carr = array();
    public $_caarr = array();
    public $_acclist = array();  //список  счетов  из  проводок
    public $_accalllist = array();  //список  счетов  из  проводок
    private $_doc;
    private $_edited = false;

    public function __construct($docid = 0) {
        parent::__construct();
        $this->_accalllist = Account::findArrayEntry();
        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new TextArea('description'));
        $this->docform->add(new Date('document_date'));
        //проводки
        $this->docform->add(new DataView('acctable', new ArrayDataSource($this, '_entryarr'), $this, 'acctableOnRow'));
        $this->docform->add(new DropDownChoice('e_acclistd', $this->_accalllist));
        $this->docform->add(new DropDownChoice('e_acclistc', $this->_accalllist));
        $this->docform->add(new TextInput('e_accsumma'));
        $this->docform->add(new SubmitButton('addaccbtn'))->onClick($this, 'addaccbtnOnClick');
        //ТМЦ
        $this->docform->add(new DataView('itemtable', new ArrayDataSource($this, '_itemarr'), $this, 'itemtableOnRow'));
        $this->docform->add(new DropDownChoice('e_storelist', Store::findArray('storename', '', 'storename')));
        $this->docform->add(new AutocompleteTextInput('e_itemlist'))->onText($this, 'OnAutoItem');

        $this->docform->add(new TextInput('e_quantity'));
        $this->docform->add(new TextInput('e_price'));
        $this->docform->add(new DropDownChoice('e_itemop', new Bind($this, '_acclist')));
        $this->docform->add(new SubmitButton('additembtn'))->onClick($this, 'additembtnOnClick');
        //Сотрудники
        $this->docform->add(new DataView('emptable', new ArrayDataSource($this, '_emparr'), $this, 'emptableOnRow'));
        $this->docform->add(new DropDownChoice('e_empop', new Bind($this, '_acclist')));
        $this->docform->add(new DropDownChoice('e_emplist', Employee::findArray('emp_name', "", 'emp_name')));
        $this->docform->add(new TextInput('e_empamount'));
        $this->docform->add(new SubmitButton('addempbtn'))->onClick($this, 'addempbtnOnClick');

        //контрагенты
        $this->docform->add(new DataView('ctable', new ArrayDataSource($this, '_carr'), $this, 'ctableOnRow'));
        $this->docform->add(new AutocompleteTextInput('e_сlist'))->onText($this, 'OnAutoCustomer');

        $this->docform->add(new TextInput('e_сamount'));
        $this->docform->add(new SubmitButton('addсbtn'))->onClick($this, 'addсbtnOnClick');
        $this->docform->add(new DropDownChoice('e_cop', new Bind($this, '_acclist')));


        //ОС и НМА
        $this->docform->add(new DataView('catable', new ArrayDataSource($this, '_caarr'), $this, 'catableOnRow'));
        $this->docform->add(new DropDownChoice('e_calist', CAsset::findArray('ca_name', "", 'ca_name')));

        $this->docform->add(new TextInput('e_caprice'));
        $this->docform->add(new DropDownChoice('e_caop', new Bind($this, '_acclist')));
        $this->docform->add(new SubmitButton('addcabtn'))->onClick($this, 'addcabtnOnClick');


        $this->docform->add(new Button('backtolist'))->onClick($this, 'backtolistOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->onClick($this, 'savedocOnClick');

        if ($docid > 0) {    //загружаем   содержимок  документа на страницу
            $this->_edited = true;
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);
            $this->docform->description->setText($this->_doc->headerdata['description']);
            $this->docform->document_date->setText(date('Y-m-d', $this->_doc->document_date));

            $this->_entryarr = unserialize(base64_decode($this->_doc->headerdata['entry']));
            $this->_itemarr = unserialize(base64_decode($this->_doc->headerdata['item']));
            $this->_emparr = unserialize(base64_decode($this->_doc->headerdata['emp']));
            $this->_carr = unserialize(base64_decode($this->_doc->headerdata['c']));
            $this->_caarr = unserialize(base64_decode($this->_doc->headerdata['ca']));

            $this->docform->acctable->Reload();
            $this->updateAccList();
            $this->docform->itemtable->Reload();
            $this->docform->emptable->Reload();

            $this->docform->ctable->Reload();
            $this->docform->catable->Reload();
        } else {
            $this->_doc = Document::create('ManualEntry');
            $this->docform->document_date->setDate(time());
        }
    }

    public function acctableOnRow($row) {
        $entry = $row->getDataItem();

        $row->add(new Label('acccodec', $entry->acc_c == -1 ? "" : $this->_accalllist[$entry->acc_c]));
        $row->add(new Label('acccoded', $entry->acc_d == -1 ? "" : $this->_accalllist[$entry->acc_d]));
        $row->add(new Label('accvalue', H::famt($entry->amount) . " " . $entry->dc));
        $row->add(new ClickLink('delacc'))->onClick($this, 'delaccOnClick');
    }

    public function delaccOnClick($sender) {
        $item = $sender->owner->getDataItem();
        $this->_entryarr = array_diff_key($this->_entryarr, array($item->entry_id => $this->_entryarr[$item->entry_id]));
        $this->docform->acctable->Reload();
        $this->updateAccList();
        $this->goAnkor("a1");
    }

    public function addaccbtnOnClick($sender) {
        $dt = $this->docform->e_acclistd->getValue();
        $ct = $this->docform->e_acclistc->getValue();

        $entry = new AccountEntry();
        $entry->entry_id = time();
        $entry->acc_c = $ct;
        $entry->acc_d = $dt;
        $entry->amount = $this->docform->e_accsumma->getText();
        if ($entry->amount == 0) {
            $this->setError('Введите сумму');
            return;
        }
        $this->_entryarr[$entry->entry_id] = $entry;
        $this->docform->acctable->Reload();
        $this->docform->e_accsumma->setText('0');
        $this->updateAccList();
        $this->goAnkor("a1");
    }

    public function updateAccList() {
        $this->_acclist = array();
        foreach ($this->_entryarr as $entry) {
            if ($entry->acc_d > 0) {
                $this->_acclist[$entry->acc_d . '_d'] = "Дебет " . $entry->acc_d;
            }

            if ($entry->acc_c > 0) {
                $this->_acclist[$entry->acc_c . '_c'] = "Кредит " . $entry->acc_c;
            }
        }
    }

    public function itemtableOnRow($row) {
        $item = $row->getDataItem();
        $_oplist = $this->docform->e_itemop->getOptionList();

        $row->add(new Label('itemop', $_oplist[$item->op]));
        //  $row->add(new Label('itemcode', $item->item_code));
        $row->add(new Label('store', $item->store_name));
        $row->add(new Label('itemname', $item->itemname));
        $row->add(new Label('itemcnt', H::fqty($item->qty)));
        $row->add(new Label('itemprice', H::famt($item->price)));
        $row->add(new ClickLink('delitem'))->onClick($this, 'delitemOnClick');
    }

    public function delitemOnClick($sender) {
        $item = $sender->owner->getDataItem();
        $this->_itemarr = array_diff_key($this->_itemarr, array($item->item_id => $this->_itemarr[$item->item_id]));
        $this->docform->itemtable->Reload();
        $this->goAnkor("a2");
    }

    public function additembtnOnClick($sender) {
        $id = $this->docform->e_itemlist->getKey();
        if (isset($this->_itemarr[$id])) {
            $this->setError('Дублирование  строки');
            return;
        }
        if ($id == 0) {
            $this->setError('Не выбран  ТМЦ');
            return;
        }
        $item = Item::load($id);
        $item->op = $this->docform->e_itemop->getValue();
        if ($item->op == 0) {
            $this->setError('Не выбран счет');
            return;
        }
        $item->store_id = $this->docform->e_storelist->getValue();
        if ($item->store_id == 0) {
            $this->setError('Не выбран склад');
            return;
        }

        $store = Store::load($item->store_id);

        $item->store_name = $store->storename;
        $item->qty = $this->docform->e_quantity->getText();
        // $item->partion =   $this->docform->e_price->getText();
        $item->price = $this->docform->e_price->getText();
        if ($item->price == 0) {
            $this->setError('Не задана цена');
            return;
        }
        if (strpos($item->op, '_c') > 0) {   //списание
            $stock = \App\Entity\Stock::getStock($item->store_id, $id, $item->price, false);
            if ($stock == null) {
                $this->setError("Не найдена партия " . H::famt($item->price));
                return;
            }
        }

        $this->_itemarr[$id] = $item;
        $this->docform->itemtable->Reload();
        $this->docform->e_quantity->setText('1');
        $this->docform->e_price->setText('0');
        $this->docform->e_itemlist->setKey(0);
        $this->docform->e_itemlist->setText('');
        $this->goAnkor("a2");
    }

    public function emptableOnRow($row) {
        $_oplist = $this->docform->e_empop->getOptionList();
        $item = $row->getDataItem();
        $row->add(new Label('empop', $_oplist[$item->op]));

        $row->add(new Label('empname', $item->emp_name));
        $row->add(new Label('empamount', H::famt($item->val)));
        $row->add(new ClickLink('delemp'))->onClick($this, 'delempOnClick');
    }

    public function delempOnClick($sender) {
        $item = $sender->owner->getDataItem();
        $this->_emparr = array_diff_key($this->_emparr, array($item->employee_id => $this->_emparr[$item->employee_id]));
        $this->docform->emptable->Reload();
        $this->goAnkor("a3");
    }

    public function addempbtnOnClick($sender) {
        $id = $this->docform->e_emplist->getValue();

        $emp = Employee::load($id);

        $emp->op = $this->docform->e_empop->getValue();
        if ($emp->op == 0) {
            $this->setError('Не выбран счет');
            return;
        }

        if (isset($this->_emparr[$id])) {
            $this->setError('Дублироваие строки');
            return;
        }
        $emp->val = $this->docform->e_empamount->getText();
        $this->_emparr[$id] = $emp;
        $this->docform->emptable->Reload();
        $this->docform->e_empamount->setText('');
        $this->goAnkor("a3");
    }

    public function ctableOnRow($row) {
        $c = $row->getDataItem();
        $_oplist = $this->docform->e_cop->getOptionList();

        $row->add(new Label('cop', $_oplist[$c->op]));
        $row->add(new Label('сname', $c->customer_name));
        $row->add(new Label('сamount', H::famt($c->val)));
        $row->add(new ClickLink('delс'))->onClick($this, 'delсOnClick');
    }

    public function delсOnClick($sender) {
        $item = $sender->owner->getDataItem();
        $this->_carr = array_diff_key($this->_carr, array($item->customer_id => $this->_carr[$item->customer_id]));
        $this->docform->ctable->Reload();
        $this->goAnkor("a4");
    }

    public function addсbtnOnClick($sender) {
        $id = $this->docform->e_сlist->getKey();
        if (isset($this->_carr[$id])) {
            $this->setError('Дублирование строки');
            return;
        }
        $c = Customer::load($id);
        $c->val = $this->docform->e_сamount->getText();
        $c->op = $this->docform->e_cop->getValue();
        $this->_carr[$id] = $c;
        $this->docform->ctable->Reload();
        $this->docform->e_сamount->setText('0');
        $this->docform->e_сlist->setKey(0);
        $this->docform->e_сlist->setText('');
        $this->goAnkor("a4");
    }

    public function catableOnRow($row) {
        $item = $row->getDataItem();
        $_oplist = $this->docform->e_caop->getOptionList();

        $row->add(new Label('caop', $_oplist[$item->op]));
        $row->add(new Label('caname', $item->ca_name));
        $row->add(new Label('cacnt', H::fqty($item->qty)));
        $row->add(new Label('caprice', H::famt($item->price)));
        $row->add(new ClickLink('delca'))->onClick($this, 'delcaOnClick');
    }

    public function delcaOnClick($sender) {
        $item = $sender->owner->getDataItem();
        $this->_caarr = array_diff_key($this->_caarr, array($item->item_id => $this->_caarr[$item->item_id]));
        $this->docform->catable->Reload();
        $this->goAnkor("a6");
    }

    public function addcabtnOnClick($sender) {
        $id = $this->docform->e_calist->getValue();
        if (isset($this->_caarr[$id])) {
            $this->setError('Дублирование строки');
        }
        if ($id == 0) {
            $this->setError('Не выбран  ТМЦ');
        }
        $item = CAsset::load($id);
        $item->op = $this->docform->e_caop->getValue();
        if ($item->op == 0) {
            $this->setError('Не выбран счет');
        }


        $item->qty = 1;
        $item->price = $this->docform->e_caprice->getText();
        if ($item->price == 0) {
            $this->setError('Не введена цена');
        }

        if ($this->isError())
            return;

        $this->_caarr[$id] = $item;
        $this->docform->catable->Reload();

        $this->docform->e_caprice->setText('0');
        $this->goAnkor("a6");
    }

    public function backtolistOnClick($sender) {

        App::RedirectBack();
    }

    public function savedocOnClick($sender) {
        $this->_doc->document_date = strtotime($this->docform->document_date->getText());
        $this->_doc->document_number = $this->docform->document_number->getText();


        $this->_doc->headerdata['entry'] = base64_encode(serialize($this->_entryarr));
        $this->_doc->headerdata['emp'] = base64_encode(serialize($this->_emparr));
        $this->_doc->headerdata['item'] = base64_encode(serialize($this->_itemarr));
        $this->_doc->headerdata['c'] = base64_encode(serialize($this->_carr));
        $this->_doc->headerdata['ca'] = base64_encode(serialize($this->_caarr));
        $this->_doc->headerdata['description'] = $this->docform->description->getText();

        $conn = \ZDB\DB::getConnect();
        $conn->BeginTrans();
        try {
            $this->_doc->save();

            if ($sender->id == 'execdoc') {
                $this->_doc->updateStatus(Document::STATE_EXECUTED);
            } else {
                $this->_doc->updateStatus($this->_edited ? Document::STATE_EDITED : Document::STATE_NEW);
            }
            $conn->CommitTrans();
            App::RedirectBack();
        } catch (\Exception $ee) {
            global $logger;
            $conn->RollbackTrans();
            $this->setError($ee->getMessage());

            $logger->error($ee);
            return;
        }
    }

    public function afterRequest() {
        $this->updateAccList();
    }

    public function OnAutoCustomer($sender) {
        $text = Customer::qstr('%' . $sender->getText() . '%');
        return Customer::findArray("customer_name", "Customer_name like " . $text);
    }

    public function OnAutoItem($sender) {
        $text = Item::qstr('%' . $sender->getText() . '%');
        return Item::findArray("itemname", "disabled <> 1 and (itemname like {$text} or item_code like {$text})    ");
    }

}
