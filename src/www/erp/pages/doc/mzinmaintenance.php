<?php

namespace ZippyERP\ERP\Pages\Doc;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Form\AutocompleteTextInput;
use Zippy\Html\Form\Date;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Link\SubmitLink;
use Zippy\Html\Panel;
use ZippyERP\System\Application as App;
use ZippyERP\System\System;
use ZippyERP\ERP\Entity\Doc\Document;
use ZippyERP\ERP\Entity\Item;
use ZippyERP\ERP\Entity\CapitalAsset;
use ZippyERP\ERP\Entity\Store;
use ZippyERP\ERP\Entity\Stock;
use \ZippyERP\ERP\Helper as H;

/**
 * Страница  ввода  МЦ в  эксплуатацию
 */
class MZInMaintenance extends \ZippyERP\ERP\Pages\Base
{

    public $_tovarlist = array();
    private $_doc;
    private $_rowid = 0;
    private $_os = false;
    private $_expenses = array(23 => "Производство", 91 => "Общепроизводственные затраты", 92 => "Административные затраты");

    public function __construct($docid = 0)
    {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new Date('document_date'))->setDate(time());

        $this->docform->add(new DropDownChoice('store', Store::findArray("storename", "store_type = " . Store::STORE_TYPE_OPT)))->setChangeHandler($this, 'OnChangeStore');
        $this->docform->add(new DropDownChoice('expenses', $this->_expenses));

        $this->docform->add(new SubmitLink('addrow'))->setClickHandler($this, 'addrowOnClick');
        $this->docform->add(new SubmitLink('addrowos'))->setClickHandler($this, 'addrowOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->setClickHandler($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->setClickHandler($this, 'savedocOnClick');
        $this->docform->add(new Button('backtolist'))->setClickHandler($this, 'backtolistOnClick');

        $this->add(new Form('editdetail'))->setVisible(false);
        $this->editdetail->add(new TextInput('editquantity'))->setText("1");
        $this->editdetail->add(new TextInput('editprice'));
        $this->editdetail->add(new AutocompleteTextInput('edittovar'))->setAutocompleteHandler($this, "OnAutoItem");
        $this->editdetail->edittovar->setChangeHandler($this, 'OnChangeItem');



        $this->editdetail->add(new Button('cancelrow'))->setClickHandler($this, 'cancelrowOnClick');
        $this->editdetail->add(new SubmitButton('submitrow'))->setClickHandler($this, 'saverowOnClick');

        if ($docid > 0) {    //загружаем   содержимок  документа на страницу
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);

            $this->docform->document_date->setDate($this->_doc->document_date);


            $this->docform->store->setValue($this->_doc->headerdata['store']);
            $this->docform->expenses->setValue($this->_doc->headerdata['expenses']);

            foreach ($this->_doc->detaildata as $item) {
                $item = new Item($item);
                $this->_tovarlist[$item->item_id] = $item;
            }
        } else {
            $this->_doc = Document::create('MZInMaintenance');
            $this->docform->document_number->setText($this->_doc->nextNumber());
        }

        $this->docform->add(new DataView('detail', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, '_tovarlist')), $this, 'detailOnRow'))->Reload();
    }

    public function detailOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('tovar', $item->itemname));
        $row->add(new Label('measure', $item->measure_name));
        $row->add(new Label('quantity', $item->quantity / 1000));
        $row->add(new Label('price', H::fm($item->price)));
        $row->add(new Label('amount', H::fm(($item->quantity / 1000) * $item->price)));
        $row->add(new ClickLink('edit'))->setClickHandler($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->setClickHandler($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender)
    {
        $tovar = $sender->owner->getDataItem();
        // unset($this->_tovarlist[$tovar->tovar_id]);

        $this->_tovarlist = array_diff_key($this->_tovarlist, array($tovar->item_id => $this->_tovarlist[$tovar->item_id]));
        $this->docform->detail->Reload();
    }

    public function addrowOnClick($sender)
    {
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

    public function editOnClick($sender)
    {

        $stock = $sender->getOwner()->getDataItem();

        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);

        $this->editdetail->editquantity->setText($stock->quantity / 1000);
        $this->editdetail->editprice->setText(H::fm($stock->price));



        //  $list = Stock::findArrayEx("closed  <> 1   and store_id={$stock->store_id}");
        $this->editdetail->edittovar->setKey($stock->stock_id);
        $this->editdetail->edittovar->setText($stock->itemname);



        $this->_rowid = $stock->stock_id;
    }

    public function saverowOnClick($sender)
    {
        $id = $this->editdetail->edittovar->getKey();
        if ($id == 0) {
            $this->setError("Не выбран МЦ");
            return;
        }

        if ($this->_os) {
            $stock = new Stock();
            $stock->stock_id = time();
            $stock->measure_name = "шт.";
            $stock->item_id = $id;  //необоротный актив
            $stock->itemname = $this->editdetail->edittovar->getText();
        } else {
            $stock = Stock::load($id);
        }
        $stock->os = $this->_os;
        $stock->quantity = 1000 * $this->editdetail->editquantity->getText();
        $stock->partion = $stock->price;
        $stock->price = $this->editdetail->editprice->getText() * 100;

        unset($this->_tovarlist[$this->_rowid]);
        $this->_tovarlist[$stock->stock_id] = $stock;
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

        $this->calcTotal();

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


        $conn = \ZDB\DB\DB::getConnect();
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
            $this->setError($ee->message);
        } catch (\Exception $ee) {
            $conn->RollbackTrans();
            throw new \Exception($ee->message);
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

        if (count($this->_tovarlist) == 0) {
            $this->setError("Не введен ни один  товар");
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

    public function OnChangeStore($sender)
    {
        //очистка  списка  товаров
        $this->_tovarlist = array();
        $this->docform->detail->Reload();
    }

    public function OnAutoItem($sender)
    {
        $text = $sender->getValue();
        if ($this->_os) {
            return CapitalAsset::findArray("itemname", "detail like '%<typeos>11</typeos>%' and  itemname  like '%{$text}%'", "itemname");
        } else {
            $store_id = $this->docform->store->getValue();
            return Stock::findArrayEx("store_id={$store_id} and closed <> 1 and  itemname  like '%{$text}%' and stock_id in( select stock_id from erp_account_subconto where account_id = 22 or  account_id = 153)");
        }
    }

    public function OnChangeItem($sender)
    {
        if ($this->_os)
            return;
        $id = $sender->getKey();
        $stock = Stock::load($id);
        //   $item = Item::load($stock->item_id);
        $this->editdetail->editprice->setText(H::fm($stock->price));



        $this->updateAjax(array('editprice'));
    }

}
