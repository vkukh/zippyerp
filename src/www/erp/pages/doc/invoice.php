<?php

//todofirst

namespace ZippyERP\ERP\Pages\Doc;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\AutocompleteTextInput;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\CheckBox;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Date;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Link\SubmitLink;
use ZippyERP\ERP\Entity\Customer;
use ZippyERP\ERP\Entity\Doc\Document;
use ZippyERP\ERP\Entity\Stock;
use ZippyERP\ERP\Entity\Item;
use ZippyERP\ERP\Helper as H;
use Zippy\WebApplication as App;

/**
 * Страница  ввода  счета-фактуры
 */
class Invoice extends \ZippyERP\ERP\Pages\Base
{

    public $_tovarlist = array();
    private $_doc;
    private $_basedocid = 0;
    private $_rowid = 0;
    private $_discount;

    public function __construct($docid = 0, $basedocid = 0) {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new Date('document_date'))->setDate(time());
        $this->docform->add(new Date('paydate'))->setDate(strtotime("+7 day", time()));
        $this->docform->add(new CheckBox('isnds'))->onChange($this, 'onIsnds');
        $this->docform->isnds->setChecked(H::usends());
        $this->docform->add(new AutocompleteTextInput('customer'))->onText($this, 'OnAutoCustomer');
        $this->docform->customer->onChange($this, 'OnChangeCustomer');


        $this->docform->add(new AutocompleteTextInput('contract'))->onText($this, "OnAutoContract");

        $this->docform->add(new SubmitLink('addrow'))->onClick($this, 'addrowOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new Button('backtolist'))->onClick($this, 'backtolistOnClick');

        $this->docform->add(new Label('total'));
        $this->docform->add(new Label('totalnds'));
        $this->add(new Form('editdetail'))->setVisible(false);

        $this->editdetail->add(new TextInput('editquantity'))->setText("1");
        $this->editdetail->add(new TextInput('editprice'));
        $this->editdetail->add(new TextInput('editpricends'));
        $this->editdetail->add(new AutocompleteTextInput('edittovar'))->onText($this, 'OnAutoItem');
        $this->editdetail->edittovar->onChange($this, 'OnChangeItem', true);


        $this->editdetail->add(new Button('cancelrow'))->onClick($this, 'cancelrowOnClick');
        $this->editdetail->add(new SubmitButton('submitrow'))->onClick($this, 'saverowOnClick');

        if ($docid > 0) {    //загружаем   содержимок  документа настраницу
            $this->_doc = Document::load($docid);
            if ($this->_doc == null)
                App::RedirectError('Докумен не найден');
            $this->docform->document_number->setText($this->_doc->document_number);
            $this->docform->isnds->setChecked($this->_doc->headerdata['isnds']);

            $this->docform->document_date->setDate($this->_doc->document_date);
            $this->docform->paydate->setDate($this->_doc->headerdata['payment_date']);

            $this->docform->customer->setKey($this->_doc->headerdata['customer']);
            $this->docform->customer->setText($this->_doc->headerdata['customername']);
            $this->docform->contract->setKey($this->_doc->headerdata['contract']);
            $this->docform->contract->setText($this->_doc->headerdata['contractnumber']);

            foreach ($this->_doc->detaildata as $item) {
                $item = new Item($item);
                $this->_tovarlist[$item->item_id] = $item;
            }
            $this->calcTotal();
        } else {
            $this->_doc = Document::create('Invoice');
            $this->docform->document_number->setText($this->_doc->nextNumber());
            if ($basedocid > 0) {  //создание на  основании
                $basedoc = Document::load($basedocid);
                if ($basedoc instanceof Document) {
                    $this->_basedocid = $basedocid;
                    //  $this->docform->base->setText($basedoc->meta_desc . " №" . $basedoc->document_number);


                    if ($basedoc->meta_name == 'CustomerOrder') {
                        $nds = H::nds(true);  // если  в  заказе   цена  с  НДС  получаем  базовую  цену

                        $this->docform->customer->setKey($basedoc->headerdata['customer']);
                        $this->docform->customer->setText($basedoc->headerdata['customername']);

                        $this->docform->contract->setKey($basedoc->document_id);
                        $this->docform->contract->setText($basedoc->document_number);

                        foreach ($basedoc->detaildata as $item) {
                            $item = new Item($item);
                            $item->pricends = $item->price;
                            $item->price = $item->price - $item->price * $nds;
                            $this->_tovarlist[$item->item_id] = $item;
                        }
                    }
                    if ($basedoc->meta_name == 'ReturnGoodsReceipt') {


                        $this->docform->customer->setKey($basedoc->headerdata['customer']);
                        $this->docform->customer->setText($basedoc->headerdata['customername']);

                        $this->docform->contract->setKey($basedoc->headerdata['contract']);
                        $this->docform->contract->setText($basedoc->headerdata['contractnumber']);

                        $this->docform->isnds->setChecked($basedoc->headerdata['isnds']);

                        foreach ($basedoc->detaildata as $item) {
                            $item = new Item($item);
                            $this->_tovarlist[$item->item_id] = $item;
                        }
                    }
                }
            }
        }

        $this->docform->add(new DataView('detail', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, '_tovarlist')), $this, 'detailOnRow'))->Reload();
    }

    public function detailOnRow($row) {
        $item = $row->getDataItem();

        $row->add(new Label('tovar', $item->itemname));
        $row->add(new Label('measure', $item->measure_name));
        $row->add(new Label('quantity', $item->quantity / 1000));
        $row->add(new Label('price', H::fm($item->price)));
        $row->add(new Label('pricends', H::fm($item->pricends)));
        $row->add(new Label('amount', H::fm(($item->quantity / 1000) * $item->pricends)));
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender) {
        $tovar = $sender->owner->getDataItem();
        // unset($this->_tovarlist[$tovar->tovar_id]);

        $this->_tovarlist = array_diff_key($this->_tovarlist, array($tovar->item_id => $this->_tovarlist[$tovar->item_id]));
        $this->docform->detail->Reload();
    }

    public function addrowOnClick($sender) {
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);
        $this->_rowid = 0;
    }

    public function editOnClick($sender) {
        $item = $sender->getOwner()->getDataItem();
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);

        $this->editdetail->editquantity->setText($item->quantity / 1000);
        $this->editdetail->editprice->setText(H::fm($item->price));
        $this->editdetail->editpricends->setText(H::fm($item->pricends));

        $this->editdetail->edittovar->setKey($item->item_id);
        $this->editdetail->edittovar->setText($item->itemname);


        $this->_rowid = $item->item_id;
    }

    public function saverowOnClick($sender) {
        $id = $this->editdetail->edittovar->getKey();
        if ($id == 0) {
            $this->setError("Не вибраний товар");
            return;
        }
        $item = Item::load($id);
        $item->quantity = 1000 * $this->editdetail->editquantity->getText();
        $item->price = $this->editdetail->editprice->getText() * 100;
        $item->pricends = $this->editdetail->editpricends->getText() * 100;

        unset($this->_tovarlist[$this->_rowid]);
        $this->_tovarlist[$item->item_id] = $item;
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

    public function cancelrowOnClick($sender) {
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
    }

    public function savedocOnClick($sender) {
        if ($this->checkForm() == false) {
            return;
        }

        $this->calcTotal();

        $this->_doc->headerdata = array(
            'customer' => $this->docform->customer->getKey(),
            'customername' => $this->docform->customer->getText(),
            'isnds' => $this->docform->isnds->isChecked(),
            'payment_date' => strtotime($this->docform->paydate->getText()),
            'contract' => $this->docform->contract->getKey(),
            'contractnumber' => $this->docform->contract->getText(),
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
        $this->_doc->datatag = $this->docform->customer->getKey();

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
            if ($this->docform->contract->getKey() > 0) {
                $this->_doc->AddConnectedDoc($this->docform->contract->getKey());
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
     * Расчет  итого
     *
     */
    private function calcTotal() {
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
    private function checkForm() {

        if (count($this->_tovarlist) == 0) {
            $this->setError("Не введений ні один  товар");
        }
        if ($this->docform->customer->getKey() == 0) {
            $this->setError("Не вибраний покупатель");
        }
        return !$this->isError();
    }

    public function beforeRender() {
        parent::beforeRender();
        $this->docform->totalnds->setVisible($this->docform->isnds->isChecked());

        $this->calcTotal();

        if ($this->docform->isnds->isChecked())
            App::$app->getResponse()->addJavaScript("var _nds = " . H::nds() . ";var nds_ = " . H::nds(true) . ";");
        else
            App::$app->getResponse()->addJavaScript("var _nds = 0;var nds_ = 0;");
    }

    public function backtolistOnClick($sender) {
        App::RedirectBack();
    }

    public function onIsnds($sender) {
        foreach ($this->_tovarlist as $item) {
            if ($sender->isChecked() == false) {
                $item->price = $item->pricends;
            } else {
                $item->price = $item->pricends - $item->pricends * H::nds(true);
            }
        }
        $this->docform->detail->Reload();
    }

    public function OnAutoContract($sender) {
        $text = $sender->getValue();
        return Document::findArray('document_number', "document_number like '%{$text}%' and ( meta_name='Contract' or meta_name='SupplierOrder' )");
    }

    public function OnChangeItem($sender) {

        $item = Item::load($sender->getKey());
        $stock = Stock::getFirst("item_id={$item->item_id} and store_id in(select store_id from erp_store where  store_type=1)", "  store_id desc");
        $price = $item->getOptPrice($stock->price > 0 ? $stock->price : 0);
        $price = $price - $price / 100 * $this->_discount;
        $this->editdetail->editprice->setText(H::fm($price));
        $this->editdetail->editpricends->setText(H::fm($price));
        if ($this->docform->isnds->IsChecked()) {
            $nds = H::nds();
            $this->editdetail->editpricends->setText(H::fm($price + $price * $nds));
        }

        $this->updateAjax(array('editprice', 'editpricends'));
    }

    public function OnAutoCustomer($sender) {
        $text = Customer::qstr('%' . $sender->getText() . '%');
        return Customer::findArray("customer_name", "Customer_name like " . $text);
    }

    public function OnChangeCustomer($sender) {
        $this->_discount = 0;
        $customer_id = $this->docform->customer->getKey();
        if ($customer_id > 0) {
            $customer = Customer::load($customer_id);
            $this->_discount = $customer->discount;
        }
        $this->calcTotal();
    }

    public function OnAutoItem($sender) {

        $text = Item::qstr('%' . $sender->getText() . '%');
        return Item::findArrayEx("(itemname like {$text} or item_code like {$text}) and item_type <>" . Item::ITEM_TYPE_RETSUM);
    }

}
