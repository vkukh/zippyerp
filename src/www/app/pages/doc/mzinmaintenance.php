<?php

namespace App\Pages\Doc;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\Date;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Form\AutocompleteTextInput;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Link\SubmitLink;
use App\Entity\CAsset;
use App\Entity\Doc\Document;
use App\Entity\Item;
use App\Entity\Stock;
use App\Entity\Store;
use App\Helper as H;
use App\Application as App;

/**
 * Страница  ввода  МЦ в  эксплуатацию
 */
class MZInMaintenance extends \App\Pages\Base
{

    public $_tovarlist = array();
    private $_doc;
    private $_rowid = 0;
    private $_os = false;
    private $_expenses = array(23 => "Производство", 91 => "Общепроизводственные затраты", 92 => "Административные затраты");

    public function __construct($docid = 0) {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new Date('document_date'))->setDate(time());



        $this->docform->add(new DropDownChoice('store', Store::findArray("storename", "")))->onChange($this, 'OnChangeStore');
        $this->docform->store->selectFirst();
        $this->docform->add(new DropDownChoice('expenses', $this->_expenses));

        $this->docform->add(new SubmitLink('addrow'))->onClick($this, 'addrowOnClick');
        $this->docform->add(new SubmitLink('addrowos'))->onClick($this, 'addrowOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new Button('backtolist'))->onClick($this, 'backtolistOnClick');

        $this->add(new Form('editdetail'))->setVisible(false);
        $this->editdetail->add(new TextInput('editquantity'))->setText("1");
        $this->editdetail->add(new TextInput('editprice'))->setText("0");
        $this->editdetail->add(new AutocompleteTextInput('edittovar'))->onText($this, 'OnAutoItem');
        $this->editdetail->edittovar->onChange($this, 'OnChangeItem');


        $this->editdetail->add(new Button('cancelrow'))->onClick($this, 'cancelrowOnClick');
        $this->editdetail->add(new SubmitButton('submitrow'))->onClick($this, 'saverowOnClick');

        if ($docid > 0) {    //загружаем   содержимок  документа на страницу
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);

            $this->docform->document_date->setDate($this->_doc->document_date);


            $this->docform->store->setValue($this->_doc->headerdata['store']);
            $this->docform->expenses->setValue($this->_doc->headerdata['expenses']);

            foreach ($this->_doc->detaildata as $_item) {
                $st = new Stock($_item);
                $this->_tovarlist[$st->stock_id] = $st;
            }
        } else {
            $this->_doc = Document::create('MZInMaintenance');
            $this->docform->document_number->setText($this->_doc->nextNumber());
        }

        $this->docform->add(new DataView('detail', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, '_tovarlist')), $this, 'detailOnRow'))->Reload();
    }

    public function detailOnRow($row) {
        $item = $row->getDataItem();

        $row->add(new Label('tovar', $item->itemname));
        $row->add(new Label('measure', $item->measure_name));
        $row->add(new Label('quantity', H::fqty($item->quantity)));
        $row->add(new Label('price', H::famt($item->price)));
        $row->add(new Label('amount', H::famt(($item->quantity ) * $item->price)));
        //$row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender) {
        $tovar = $sender->owner->getDataItem();

        $this->_tovarlist = array_diff_key($this->_tovarlist, array($tovar->stock_id => $this->_tovarlist[$tovar->stock_id]));

        $this->docform->detail->Reload();
    }

    public function addrowOnClick($sender) {
        $this->_os = $sender->id == "addrowos";
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);
        $this->_rowid = 0;
        //очищаем  форму
        $this->editdetail->edittovar->setKey(0);
        $this->editdetail->edittovar->setText('');

        $this->editdetail->editquantity->setText("1");

        $this->editdetail->editprice->setText("");
    }

    public function saverowOnClick($sender) {
        $id = $this->editdetail->edittovar->getKey();
        if ($id == 0) {
            $this->setError("Не выбран  МЦ");
            return;
        }

        if ($this->_os) {
            $stock = new Stock();
            $stock->stock_id = time();
            $stock->measure_name = "шт.";
            $stock->ca_id = $id;  //необоротный актив
            $stock->itemname = $this->editdetail->edittovar->getText();
        } else {
            $stock = Stock::load($id);
        }
        $stock->os = $this->_os;
        $stock->quantity = $this->editdetail->editquantity->getText();
        $stock->partion = $stock->price;
        $stock->price = $this->editdetail->editprice->getText();

        // unset($this->_tovarlist[$this->_rowid]);
        $this->_tovarlist[$stock->stock_id] = $stock;

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
            'store' => $this->docform->store->getValue(),
            'expenses' => $this->docform->expenses->getValue(),
            'expensesname' => $this->_expenses[$this->docform->expenses->getValue()]
        );
        $this->_doc->detaildata = array();
        foreach ($this->_tovarlist as $tovar) {
            $this->_doc->detaildata[] = $tovar->getData();
        }


        $this->_doc->document_number = $this->docform->document_number->getText();
        $this->_doc->document_date = $this->docform->document_date->getDate();
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

        if (count($this->_tovarlist) == 0) {
            $this->setError("Не введений ні один  товар");
        }

        return !$this->isError();
    }

    public function backtolistOnClick($sender) {
        App::RedirectBack();
    }

    public function OnChangeStore($sender) {
        //очистка  списка  товаров
        $this->_tovarlist = array();
        $this->docform->detail->Reload();
    }

    public function OnAutoItem($sender) {

        $text = Item::qstr('%' . $sender->getText() . '%');

        if ($this->_os) {
            $list = CAsset::findArray("ca_name", "acc_code='112' ");
        } else {
            $store_id = $this->docform->store->getValue();
            $list = Stock::findArrayEx("store_id={$store_id}   and stock_id in( select stock_id from entrylist_view where acc_code = '22' )", "itemname");
        }
        return $list;
    }

    public function OnChangeItem($sender) {
        if ($this->_os)
            return;
        $id = $sender->getKey();
        $stock = Stock::load($id);

        $this->editdetail->editprice->setText(H::famt($stock->partion));


        $this->updateAjax(array('editprice'));
    }

}
