<?php

namespace ZippyERP\ERP\Pages\Doc;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Form\Date;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Link\SubmitLink;
use Zippy\Html\Panel;
use ZippyERP\System\Application as App;
use ZippyERP\System\System;
use ZippyERP\ERP\Entity\Doc\Document;
use ZippyERP\ERP\Entity\Item;
use ZippyERP\ERP\Entity\Customer;
use ZippyERP\ERP\Entity\Store;
use ZippyERP\ERP\Entity\Stock;
use Zippy\Html\Form\CheckBox;
use ZippyERP\ERP\Entity\GroupItem;
use ZippyERP\ERP\Helper as H;

/**
 * Страница  ввода  расходной  накладной
 */
class GoodsIssue extends \ZippyERP\ERP\Pages\Base
{

    public $_tovarlist = array();
    private $_doc;
    private $_basedocid = 0;
    private $_rowid = 0;

    public function __construct($docid = 0, $basedocid = 0)
    {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));

        $this->docform->add(new Date('created'))->setDate(time());

        $this->docform->add(new DropDownChoice('store', Store::findArray("storename", "store_type = " . Store::STORE_TYPE_OPT)))->setChangeHandler($this, 'OnChangeStore');
        $this->docform->add(new DropDownChoice('customer', Customer::getBuyers()));
        $this->docform->add(new CheckBox('isnds', true))->setChangeHandler($this, 'onIsnds');
        $this->docform->add(new CheckBox('cash'));
        $this->docform->add(new TextInput('based'));

        $this->docform->add(new SubmitLink('addrow'))->setClickHandler($this, 'addrowOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->setClickHandler($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->setClickHandler($this, 'savedocOnClick');
        $this->docform->add(new Button('backtolist'))->setClickHandler($this, 'backtolistOnClick');

        $this->docform->add(new Label('total'));
        $this->docform->add(new Label('totalnds'));
        $this->add(new Form('editdetail'))->setVisible(false);
        $this->editdetail->add(new TextInput('editquantity'))->setText("1");
        $this->editdetail->add(new TextInput('editprice'));
        $this->editdetail->add(new TextInput('editpricends'));
        $this->editdetail->add(new DropDownChoice('editgroup'))->setAjaxChangeHandler($this, 'OnGroup');
        $this->editdetail->editgroup->setOptionList(GroupItem::getList());
        $this->editdetail->add(new DropDownChoice('edittovar'))->setAjaxChangeHandler($this, 'OnItem');

        $this->editdetail->add(new Label('qtystock'));

        $this->editdetail->add(new Button('cancelrow'))->setClickHandler($this, 'cancelrowOnClick');
        $this->editdetail->add(new SubmitButton('submitrow'))->setClickHandler($this, 'saverowOnClick');

        if ($docid > 0) {    //загружаем   содержимок  документа настраницу
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);

            //  $this->docform->nds->setText($this->_doc->headerdata['nds'] / 100);
            $this->docform->created->setDate($this->_doc->document_date);

            $this->docform->store->setValue($this->_doc->headerdata['store']);
            $this->docform->isnds->setChecked($this->_doc->headerdata['isnds']);
            $this->docform->cash->setChecked($this->_doc->headerdata['cash']);
            $this->docform->based->setText($this->_doc->headerdata['based']);
            $this->docform->customer->setValue($this->_doc->headerdata['customer']);

            foreach ($this->_doc->detaildata as $item) {
                $stock = new Stock($item);
                $this->_tovarlist[$stock->stock_id] = $stock;
            }
        } else {
            $this->_doc = Document::create('GoodsIssue');
            $this->docform->document_number->setText($this->_doc->nextNumber());

            if ($basedocid > 0) {  //создание на  основании
                $basedoc = Document::load($basedocid);
                if ($basedoc instanceof Document) {
                    $this->_basedocid = $basedocid;
                    $this->docform->based->setText($basedoc->meta_desc . " №" . $basedoc->document_number);


                    if ($basedoc->meta_name == 'Invoice') {
                        //  $this->docform->nds->setText($basedoc->headerdata['nds'] / 100);
                        $this->docform->customer->setValue($basedoc->headerdata['customer']);
                        $this->docform->isnds->setChecked($basedoc->headerdata['isnds']);

                        foreach ($basedoc->detaildata as $item) {
                            $item = new Item($item);
                            //находим  последнюю партию по  первому складу
                            $options = $this->docform->store->getOptionList();
                            $keys = array_keys($options);
                            $stock = Stock::getFirst("closed <> 1 and group_id={$item->group_id} and item_id={$item->item_id} and store_id=" . $keys[0], 'stock_id', 'desc');
                            if ($stock instanceof Stock) {
                                $stock->quantity = $this->editdetail->editquantity->getText();
                                $stock->quantity = $item->quantity;
                                //  $stock->partion = $item->priceopt;
                                //   $stock->group_id = $item->group_id;
                                $stock->pricends = $item->pricends;
                                $stock->price = $item->price;
                                $this->_tovarlist[$stock->stock_id] = $stock;
                            } else {
                                $this->setError('Не найден на складе  товар ' . $item->itemname);
                            }
                        }
                    }
                }
            }
        }

        $this->docform->add(new DataView('detail', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, '_tovarlist')), $this, 'detailOnRow'))->Reload();
    }

    public function detailOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('tovar', $item->itemname));
        $row->add(new Label('partion', H::fm($item->partion)));
        $row->add(new Label('measure', $item->measure_name));
        $row->add(new Label('quantity', $item->quantity));
        $row->add(new Label('price', H::fm($item->price)));
        $row->add(new Label('pricends', H::fm($item->pricends)));
        $row->add(new Label('amount', H::fm($item->pricends * $item->quantity)));
        $row->add(new ClickLink('delete'))->setClickHandler($this, 'deleteOnClick');
        $row->add(new ClickLink('edit'))->setClickHandler($this, 'editOnClick');
    }

    public function deleteOnClick($sender)
    {
        $tovar = $sender->owner->getDataItem();
        // unset($this->_tovarlist[$tovar->tovar_id]);

        $this->_tovarlist = array_diff_key($this->_tovarlist, array($tovar->stock_id => $this->_tovarlist[$tovar->stock_id]));
        $this->docform->detail->Reload();
    }

    public function addrowOnClick($sender)
    {
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);
        $this->_rowid = 0;
    }

    public function editOnClick($sender)
    {
        $stock = $sender->getOwner()->getDataItem();
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);

        $this->editdetail->editquantity->setText($stock->quantity);
        $this->editdetail->editprice->setText(H::fm($stock->price));
        $this->editdetail->editpricends->setText(H::fm($stock->pricends));

        $this->editdetail->editgroup->setValue($stock->group_id);
        $list = Stock::findArrayEx("closed  <> 1 and group_id={$stock->group_id} and store_id={$stock->store_id}");
        $this->editdetail->edittovar->setOptionList($list);
        $this->editdetail->edittovar->setValue($stock->stock_id);
        //  $this->editdetail->editid->setText($item->item_id);
        $this->editdetail->qtystock->setText(Stock::getQuantity($stock->stock_id, $this->docform->created->getDate()) . ' ' . $stock->measure_name);

        $this->_rowid = $stock->stock_id;
    }

    public function saverowOnClick($sender)
    {
        $id = $this->editdetail->edittovar->getValue();
        if ($id == 0) {
            $this->setError("Не выбран товар");
            return;
        }

        $stock = Stock::load($id);
        $stock->quantity = $this->editdetail->editquantity->getText();
        $stock->partion = $stock->price;
        $stock->price = $this->editdetail->editprice->getText() * 100;
        $stock->pricends = $this->editdetail->editpricends->getText() * 100;

        unset($this->_tovarlist[$this->_rowid]);
        $this->_tovarlist[$stock->stock_id] = $stock;
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
        $this->docform->detail->Reload();

        //очищаем  форму
        $this->editdetail->edittovar->setValue(0);
        $this->editdetail->editquantity->setText("1");

        $this->editdetail->editprice->setText("");
        $this->editdetail->editpricends->setText("");
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
            'customer' => $this->docform->customer->getValue(),
            'store' => $this->docform->store->getValue(),
            'isnds' => $this->docform->isnds->isChecked(),
            'cash' => $this->docform->cash->isChecked(),
            'based' => $this->docform->based->getText(),
            'totalnds' => $this->docform->totalnds->getText() * 100,
            'total' => $this->docform->total->getText() * 100
        );
        $this->_doc->detaildata = array();
        foreach ($this->_tovarlist as $tovar) {
            $this->_doc->detaildata[] = $tovar->getData();
        }

        $this->_doc->amount = 100 * $this->docform->total->getText();
        $this->_doc->document_number = $this->docform->document_number->getText();
        $this->_doc->document_date = strtotime($this->docform->created->getText());
        $isEdited = $this->_doc->document_id > 0;

        $this->_doc->save();
        if ($sender->id == 'execdoc') {
            $this->_doc->updateStatus(Document::STATE_EXECUTED);
        } else {
            $this->_doc->updateStatus($isEdited ? Document::STATE_EDITED : Document::STATE_NEW);
        }
        if ($this->_basedocid > 0) {
            $this->_doc->AddConnectedDoc($this->_basedocid);
            $this->_basedocid = 0;
        }
        App::RedirectBack();
    }

    /**
     * Расчет  итого
     * 
     */
    private function calcTotal()
    {

        $total = 0;
        $totalnds = 0;
        foreach ($this->_tovarlist as $item) {
            $item->amount = $item->pricends * $item->quantity;
            $item->nds = $item->amount - $item->price * $item->quantity;
            $total = $total + $item->amount;
            $totalnds = $totalnds + $item->nds;
        }
        $this->docform->total->setText(H::fm($total));
        $this->docform->totalnds->setText(H::fm($totalnds));
    }

    /**
     * Валидация   формы
     * 
     */
    private function checkForm()
    {

        if (count($this->_tovarlist) == 0) {
            $this->setError("Не введен ни один  товар");
            return false;
        }
        return true;
    }

    public function beforeRender()
    {
        parent::beforeRender();
        $this->docform->totalnds->setVisible($this->docform->isnds->isChecked());

        $this->calcTotal();
        App::$app->getResponse()->addJavaScript("var _nds = " . H::nds() . ";var nds_ = " . H::nds(true) . ";");
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

    public function onIsnds($sender)
    {
        foreach ($this->_tovarlist as $item) {
            if ($sender->isChecked() == false) {
                $item->price = $item->pricends;
            } else {
                $item->price = $item->pricends - $item->pricends * H::nds(true);
            }
        }
        $this->docform->detail->Reload();
    }

    public function OnGroup(DropDownChoice $sender)
    {
        $id = $sender->getValue();
        $store_id = $this->docform->store->getValue();
        $list = Stock::findArrayEx("closed  <> 1 and group_id={$id} and store_id={$store_id}");
        $list = array_replace(array(0 => 'Выбрать'), $list);
        $this->editdetail->edittovar->setOptionList($list);
        $this->updateAjax(array('edittovar'));
    }

    public function OnItem(DropDownChoice $sender)
    {
        $id = $sender->getValue();
        $stock = Stock::load($id);
        $item = Item::load($stock->item_id);
        $this->editdetail->editprice->setText(H::fm($item->priceopt));
        $nds = 0;
        if ($this->docform->isnds->isChecked()) {
            $nds = H::nds();
        }

        $this->editdetail->editpricends->setText(H::fm($item->priceopt + $item->priceopt * $nds));
        $this->editdetail->qtystock->setText(Stock::getQuantity($id, $this->docform->created->getDate()) . ' ' . $stock->measure_name);

        $this->updateAjax(array('editprice', 'editpricends', 'qtystock'));
    }

}
