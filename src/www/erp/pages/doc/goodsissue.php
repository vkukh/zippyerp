<?php

namespace ZippyERP\ERP\Pages\Doc;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\AutocompleteTextInput;
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
use ZippyERP\ERP\Entity\Customer;
use ZippyERP\ERP\Entity\Doc\Document;
use ZippyERP\ERP\Entity\Item;
use ZippyERP\ERP\Entity\Stock;
use ZippyERP\ERP\Entity\Store;
use ZippyERP\ERP\Helper as H;
use ZippyERP\System\Application as App;

/**
 * Страница  ввода  расходной  накладной
 */
class GoodsIssue extends \ZippyERP\System\Pages\Base
{

    public $_tovarlist = array();
    private $_doc;
    private $_basedocid = 0;
    private $_rowid = 0;
    private $_itemtype = array(281 => 'Товар', 26 => 'Готовая продукция');

    public function __construct($docid = 0, $basedocid = 0)
    {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));

        $this->docform->add(new Date('document_date'))->setDate(time());
        $this->docform->add(new CheckBox('plan'));

        $this->docform->add(new DropDownChoice('store', Store::findArray("storename", "store_type = " . Store::STORE_TYPE_OPT)))->setChangeHandler($this, 'OnChangeStore');
        $this->docform->add(new AutocompleteTextInput('customer'))->setAutocompleteHandler($this, "OnAutoContragent");
        $this->docform->add(new CheckBox('isnds', true))->setChangeHandler($this, 'onIsnds');
        $this->docform->add(new CheckBox('cash'));
        $this->docform->add(new CheckBox('prepayment'))->setChecked(true);
        $this->docform->add(new AutocompleteTextInput('contract'))->setAutocompleteHandler($this, "OnAutoContract");
        $this->docform->plan->setChecked($this->_doc->headerdata['plan']);

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
        $this->editdetail->add(new AutocompleteTextInput('edittovar'))->setAutocompleteHandler($this, "OnAutoItem");
        $this->editdetail->edittovar->setChangeHandler($this, 'OnChangeItem');
        $this->editdetail->add(new DropDownChoice('edittype', $this->_itemtype))->setChangeHandler($this, "OnItemType");

        $this->editdetail->add(new Label('qtystock'));

        $this->editdetail->add(new Button('cancelrow'))->setClickHandler($this, 'cancelrowOnClick');
        $this->editdetail->add(new SubmitButton('submitrow'))->setClickHandler($this, 'saverowOnClick');

        if ($docid > 0) {    //загружаем   содержимок  документа настраницу
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);

            //  $this->docform->nds->setText($this->_doc->headerdata['nds'] / 100);
            $this->docform->document_date->setDate($this->_doc->document_date);

            $this->docform->store->setValue($this->_doc->headerdata['store']);
            $this->docform->isnds->setChecked($this->_doc->headerdata['isnds']);
            $this->docform->prepayment->setChecked($this->_doc->headerdata['prepayment']);
            $this->docform->cash->setChecked($this->_doc->headerdata['cash']);
            $this->docform->customer->setKey($this->_doc->headerdata['customer']);
            $this->docform->customer->setText($this->_doc->headerdata['customername']);
            $this->docform->contract->setKey($this->_doc->headerdata['contract']);
            $this->docform->contract->setText($this->_doc->headerdata['contractnumber']);

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


                    if ($basedoc->meta_name == 'Invoice') {
                        $this->docform->customer->setKey($basedoc->headerdata['customer']);
                        $this->docform->customer->setText($basedoc->headerdata['customername']);
                        $this->docform->contract->setKey($basedoc->headerdata['contract']);
                        $this->docform->contract->setText($basedoc->headerdata['contractnumber']);
                        $this->docform->isnds->setChecked($basedoc->headerdata['isnds']);

                        foreach ($basedoc->detaildata as $item) {
                            $item = new Item($item);
                            //находим  последнюю партию по  первому складу
                            $options = $this->docform->store->getOptionList();
                            $keys = array_keys($options);
                            $stock = Stock::getFirst("closed <> 1 and group_id={$item->group_id} and item_id={$item->item_id} and store_id=" . $keys[0], 'stock_id desc');
                            if ($stock instanceof Stock) {
                                $stock->quantity = $item->quantity;
                                $stock->pricends = $item->pricends;
                                $stock->price = $item->price;
                                $stock->type = 381;

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
        $row->add(new Label('quantity', $item->quantity / 1000));
        $row->add(new Label('price', H::fm($item->price)));
        $row->add(new Label('pricends', H::fm($item->pricends)));
        $row->add(new Label('amount', H::fm($item->pricends * ($item->quantity / 1000))));
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

        $this->editdetail->editquantity->setText($stock->quantity / 1000);
        $this->editdetail->editprice->setText(H::fm($stock->price));
        $this->editdetail->editpricends->setText(H::fm($stock->pricends));

        $this->editdetail->edittovar->setKey($stock->stock_id);
        $this->editdetail->edittovar->setText($stock->itemname);
        $this->editdetail->edittype->setValue($stock->type);

        $this->editdetail->qtystock->setText(Stock::getQuantity($stock->stock_id, $this->docform->document_date->getDate(), $stock->type) / 1000 . ' ' . $stock->measure_name);

        $this->_rowid = $stock->stock_id;
    }

    public function saverowOnClick($sender)
    {
        $id = $this->editdetail->edittovar->getKey();
        if ($id == 0) {
            $this->setError("Не выбран товар");
            return;
        }

        $stock = Stock::load($id);
        $stock->quantity = 1000 * $this->editdetail->editquantity->getText();
        $stock->partion = $stock->price;
        $stock->type = $this->editdetail->edittype->getValue();
        $stock->price = $this->editdetail->editprice->getText() * 100;
        $stock->pricends = $this->editdetail->editpricends->getText() * 100;

        unset($this->_tovarlist[$this->_rowid]);
        $this->_tovarlist[$stock->stock_id] = $stock;
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
        $this->docform->detail->Reload();

        //очищаем  форму
        $this->editdetail->edittovar->setKey(0);
        $this->editdetail->edittovar->setText('');
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
            'customer' => $this->docform->customer->getKey(),
            'customername' => $this->docform->customer->getText(),
            'store' => $this->docform->store->getValue(),
            'contract' => $this->docform->contract->getKey(),
            'contractnumber' => $this->docform->contract->getText(),
            'isnds' => $this->docform->isnds->isChecked(),
            'cash' => $this->docform->cash->isChecked(),
            'plan' => $this->docform->plan->isChecked(),
            'prepayment' => $this->docform->prepayment->isChecked(),
            'totalnds' => $this->docform->totalnds->getText() * 100,
            'total' => $this->docform->total->getText() * 100
        );
        $this->_doc->detaildata = array();
        foreach ($this->_tovarlist as $tovar) {
            $this->_doc->detaildata[] = $tovar->getData();
        }

        $this->_doc->amount = 100 * $this->docform->total->getText();
        $this->_doc->document_number = $this->docform->document_number->getText();
        $this->_doc->document_date = strtotime($this->docform->document_date->getText());
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
            if ($this->_basedocid > 0) {
                $this->_doc->AddConnectedDoc($this->_basedocid);
                $this->_basedocid = 0;
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

        $total = 0;
        $totalnds = 0;
        foreach ($this->_tovarlist as $item) {
            $item->amount = $item->pricends * ($item->quantity / 1000);
            $item->nds = $item->amount - $item->price * ($item->quantity / 1000);
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
        }
        if ($this->docform->customer->getKey() == 0) {
            $this->setError("Не введен   покупатель");
        }
        if ($this->docform->cash->isChecked() && $this->docform->prepayment->isChecked()) {
            $this->setError("Должно  быть либо  предоплата либо  оплата  наличными");
        }
        return !$this->isError();
    }

    public function beforeRender()
    {
        parent::beforeRender();
        $this->docform->totalnds->setVisible($this->docform->isnds->isChecked());

        $this->calcTotal();
        if ($this->docform->isnds->isChecked())
            App::$app->getResponse()->addJavaScript("var _nds = " . H::nds() . ";var nds_ = " . H::nds(true) . ";");
        else
            App::$app->getResponse()->addJavaScript("var _nds = 0;var nds_ = 0;");
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

    public function OnAutoContragent($sender)
    {
        $text = $sender->getValue();
        return Customer::findArray('customer_name', "customer_name like '%{$text}%' and ( cust_type=" . Customer::TYPE_BUYER . " or cust_type= " . Customer::TYPE_BUYER_SELLER . " )");
    }

    public function OnAutoContract($sender)
    {
        $text = $sender->getValue();
        return Document::findArray('document_number', "document_number like '%{$text}%' and ( meta_name='Contract' or meta_name='SupplierOrder' )");
    }

    public function OnAutoItem($sender)
    {
        $text = $sender->getValue();
        $store_id = $this->docform->store->getValue();

        return Stock::findArrayEx("store_id={$store_id} and closed <> 1 and  itemname  like '%{$text}%' ");
    }

    public function OnChangeItem($sender)
    {
        $id = $sender->getKey();
        $stock = Stock::load($id);
        $this->editdetail->qtystock->setText(Stock::getQuantity($id, $this->docform->document_date->getDate(), $this->editdetail->edittype->getValue()) / 1000 . ' ' . $stock->measure_name);

        $item = Item::load($stock->item_id);
        $this->editdetail->editprice->setText(H::fm($item->priceopt));
        $nds = 0;
        if ($this->docform->isnds->isChecked()) {
            $nds = H::nds();
        }

        $this->editdetail->editpricends->setText(H::fm($item->priceopt + $item->priceopt * $nds));

        $this->updateAjax(array('editprice', 'editpricends', 'qtystock'));
    }

    public function OnItemType($sender)
    {
        $stock_id = $this->editdetail->edittovar->getKey();
        $stock = Stock::load($stock_id);
        $this->editdetail->qtystock->setText(Stock::getQuantity($stock_id, $this->docform->document_date->getDate(), $this->editdetail->edittype->getValue()) / 1000 . ' ' . $stock->measure_name);
    }

}
