<?php

namespace App\Pages\Doc;

use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Form\AutocompleteTextInput;
use \Zippy\Html\Form\Button;
use \Zippy\Html\Form\CheckBox;
use \Zippy\Html\Form\Date;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\SubmitButton;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Link\SubmitLink;
use \App\Entity\Customer;
use \App\Entity\Doc\Document;
use \App\Entity\Item;
use \App\Entity\Stock;
use \App\Entity\Store;
use \App\Helper as H;
use \App\Application as App;

/**
 * Страница  ввода счета-фактуры
 */
class Invoice extends \App\Pages\Base
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
        $this->docform->add(new Date('paydate'))->setDate(time() + 3 * 24 * 3600);


        $this->docform->add(new DropDownChoice('store', Store::getList(), H::getDefStore()))->onChange($this, 'OnChangeStore');


        $this->docform->add(new AutocompleteTextInput('customer'))->onText($this, 'OnAutoCustomer');
        $this->docform->customer->onChange($this, 'OnChangeCustomer');
        $this->docform->add(new DropDownChoice('pricetype', Item::getPriceTypeList()))->onChange($this, 'OnChangePriceType');


        $this->docform->add(new TextInput('notes'));
        $this->docform->add(new TextInput('order'));

        $this->docform->add(new Label('discount'))->setVisible(false);

        $this->docform->add(new SubmitLink('addcust'))->onClick($this, 'addcustOnClick');

        $this->docform->add(new SubmitLink('addrow'))->onClick($this, 'addrowOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('forpay'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('payed'))->onClick($this, 'savedocOnClick');

        $this->docform->add(new Button('backtolist'))->onClick($this, 'backtolistOnClick');

        $this->docform->add(new Label('total'));
        $this->docform->add(new Label('totalnds'));

        $this->add(new Form('editdetail'))->setVisible(false);
        $this->editdetail->add(new TextInput('editquantity'))->setText("1");
        $this->editdetail->add(new TextInput('editprice'));
        $this->editdetail->add(new TextInput('editpricends'));
        $this->editdetail->add(new AutocompleteTextInput('edittovar'))->onText($this, 'OnAutoItem');
        $this->editdetail->edittovar->onChange($this, 'OnChangeItem', true);



        $this->editdetail->add(new Label('qtystock'));

        $this->editdetail->add(new Button('cancelrow'))->onClick($this, 'cancelrowOnClick');
        $this->editdetail->add(new SubmitButton('submitrow'))->onClick($this, 'saverowOnClick');

        //добавление нового контрагента
        $this->add(new Form('editcust'))->setVisible(false);
        $this->editcust->add(new TextInput('editcustname'));
        $this->editcust->add(new TextInput('editphone'));
        $this->editcust->add(new Button('cancelcust'))->onClick($this, 'cancelcustOnClick');
        $this->editcust->add(new SubmitButton('savecust'))->onClick($this, 'savecustOnClick');

        if ($docid > 0) {    //загружаем   содержимок  документа настраницу
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);
            $this->docform->pricetype->setValue($this->_doc->headerdata['pricetype']);

            $this->docform->document_date->setDate($this->_doc->document_date);
            $this->docform->paydate->setDate($this->_doc->headerdata['paydate']);

            $this->docform->store->setValue($this->_doc->headerdata['store']);
            $this->docform->customer->setKey($this->_doc->customer_id);
            $this->docform->customer->setText($this->_doc->customer_name);

            $this->docform->notes->setText($this->_doc->notes);
            $this->docform->order->setText($basedoc->headerdata['order']);

            foreach ($this->_doc->detaildata as $_item) {
                $item = new Item($_item);
                $this->_tovarlist[$item->item_id] = $item;
            }
        } else {
            $this->_doc = Document::create('Invoice');
            $this->docform->document_number->setText($this->_doc->nextNumber());

            if ($basedocid > 0) {  //создание на  основании
                $basedoc = Document::load($basedocid);
                if ($basedoc instanceof Document) {
                    $this->_basedocid = $basedocid;
                    if ($basedoc->meta_name == 'Order') {

                        $this->docform->customer->setKey($basedoc->customer_id);
                        $this->docform->customer->setText($basedoc->customer_name);
                        $this->docform->order->setText($basedoc->document_number);
                        $this->docform->pricetype->setValue($this->_doc->headerdata['pricetype']);
                        $this->docform->store->setValue($this->_doc->headerdata['store']);


                        foreach ($basedoc->detaildata as $_item) {
                            $item = new Item($_item);
                            if (H::usends()) {
                                $item->pricends = $item->price;
                                $item->price = H::famt($item->pricends - $item->pricends * H::nds(true));
                            }
                            $this->_tovarlist[$item->items_id] = $item;
                        }
                    }
                }
            }
        }
        $this->calcTotal();
        $this->docform->add(new DataView('detail', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, '_tovarlist')), $this, 'detailOnRow'))->Reload();
        if (false == \App\ACL::checkShowDoc($this->_doc))
            return;
    }

    public function detailOnRow($row) {
        $item = $row->getDataItem();

        $row->add(new Label('tovar', $item->itemname));

        $row->add(new Label('code', $item->item_code));

        $row->add(new Label('quantity', H::fqty($item->quantity)));
        $row->add(new Label('price', H::famt($item->price)));
        $row->add(new Label('pricends', H::famt($item->pricends)));

        $row->add(new Label('msr', $item->msr));

        $row->add(new Label('amount', H::famt($item->amount)));
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
    }

    public function deleteOnClick($sender) {
        if (false == \App\ACL::checkEditDoc($this->_doc))
            return;

        $tovar = $sender->owner->getDataItem();
        // unset($this->_tovarlist[$tovar->tovar_id]);

        $this->_tovarlist = array_diff_key($this->_tovarlist, array($tovar->item_id => $this->_tovarlist[$tovar->item_id]));
        $this->docform->detail->Reload();
        $this->calcTotal();
    }

    public function addrowOnClick($sender) {
        $this->editdetail->setVisible(true);
        $this->editdetail->editquantity->setText("1");
        $this->editdetail->editprice->setText("0");
        $this->docform->setVisible(false);
        $this->_rowid = 0;
    }

    public function editOnClick($sender) {
        $item = $sender->getOwner()->getDataItem();
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);

        $this->editdetail->editquantity->setText($item->quantity);
        $this->editdetail->editprice->setText($item->price);
        $this->editdetail->editpricends->setText($item->pricends);

        $this->editdetail->edittovar->setKey($item->item_id);
        $this->editdetail->edittovar->setText($item->itemname);


        $this->editdetail->qtystock->setText(H::fqty($item::getQuantity() ) );

        $this->_rowid = $item->item_id;
    }

    public function saverowOnClick($sender) {

        $id = $this->editdetail->edittovar->getKey();
        if ($id == 0) {
            $this->setError("Не выбран товар");
            return;
        }

        $item = Item::load($id);
        $item->quantity = $this->editdetail->editquantity->getText();


        $item->price = $this->editdetail->editprice->getText();
        $item->pricends = $this->editdetail->editpricends->getText();


        unset($this->_tovarlist[$this->_rowid]);
        $this->_tovarlist[$item->item_id] = $item;
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);

        $this->calcTotal();
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
        //очищаем  форму
        $this->editdetail->edittovar->setKey(0);
        $this->editdetail->edittovar->setText('');

        $this->editdetail->editquantity->setText("1");

        $this->editdetail->editprice->setText("");
    }

    public function savedocOnClick($sender) {
        if (false == \App\ACL::checkEditDoc($this->_doc))
            return;
        $this->_doc->document_number = $this->docform->document_number->getText();
        $this->_doc->document_date = strtotime($this->docform->document_date->getText());
        $this->_doc->notes = $this->docform->notes->getText();

        $this->_doc->customer_id = $this->docform->customer->getKey();
        if ($this->checkForm() == false) {
            return;
        }

        $this->calcTotal();
        $old = $this->_doc->cast();

        $this->_doc->headerdata = array(
            'store' => $this->docform->store->getValue(),
            'paydate' => $this->docform->paydate->getDate(),
            'pricetype' => $this->docform->pricetype->getValue(),
            'pricetypename' => $this->docform->pricetype->getValueName(),
            'order' => $this->docform->order->getText(),
            'totalnds' => $this->docform->totalnds->getText(),
            'total' => $this->docform->total->getText()
        );
        $this->_doc->detaildata = array();
        foreach ($this->_tovarlist as $tovar) {
            $this->_doc->detaildata[] = $tovar->getData();
        }

        $this->_doc->amount = $this->docform->total->getText();
        $this->_doc->datatag = $this->_doc->amount;
        $isEdited = $this->_doc->document_id > 0;



        $conn = \ZDB\DB::getConnect();
        $conn->BeginTrans();
        try {
            $this->_doc->save();
            if ($sender->id == 'forpay') {
                if (!$isEdited)
                    $this->_doc->updateStatus(Document::STATE_NEW);

                $this->_doc->updateStatus(Document::STATE_EXECUTED);
                $this->_doc->updateStatus(Document::STATE_WP);
            } else
            if ($sender->id == 'payed') {
                if (!$isEdited)
                    $this->_doc->updateStatus(Document::STATE_NEW);

                $this->_doc->updateStatus(Document::STATE_EXECUTED);
                $this->_doc->updateStatus(Document::STATE_PAYED);

                //проверяем  что есть ордер
                $list = $this->_doc->ConnectedDocList();
                foreach ($list as $d) {
                    if ($d->meta_name == 'Order') {
                        $d->updateStatus(Document::STATE_PAYED);
                    }
                }
            } else {
                $this->_doc->updateStatus($isEdited ? Document::STATE_EDITED : Document::STATE_NEW);
            }



            if ($this->_basedocid > 0) {
                $this->_doc->AddConnectedDoc($this->_basedocid);
                $this->_basedocid = 0;
            }
            $conn->CommitTrans();
            App::RedirectBack();
        } catch (\Exception $ee) {
            global $logger;
            $conn->RollbackTrans();
            $this->setError($ee->getMessage());

            $logger->error($ee->getMessage() . " Документ " . $this->_doc->meta_desc);
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
            $item->amount = $item->price * $item->quantity;
            if (H::usends()) {
                $item->amount = $item->pricends * $item->quantity;
            }
            $totalnds = $totalnds + ($item->pricends - $item->price) * $item->quantity;

            $total = $total + $item->amount;
        }
        $this->docform->total->setText(H::famt($total));
        $this->docform->totalnds->setText(H::famt($totalnds));
    }

    /**
     * Валидация   формы
     *
     */
    private function checkForm() {
        if (strlen($this->_doc->document_number) == 0) {
            $this->setError('Введите номер документа');
        }
        if (count($this->_tovarlist) == 0) {
            $this->setError("Не веден ни один  товар");
        }
        if ($this->docform->store->getValue() == 0) {
            $this->setError("Не выбран  склад");
        }
        if ($this->docform->customer->getKey() == 0 && trim($this->docform->customer->getText()) == '') {
            $this->setError("Неверно введен  покупатель");
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

    public function OnChangeItem($sender) {
        $id = $sender->getKey();
        $item = Item::load($id);
        $this->editdetail->qtystock->setText(H::fqty($item->qty));


        $price = $item->getPrice($this->docform->pricetype->getValue());
        $price = $price - $price / 100 * $this->_discount;


        $this->editdetail->editprice->setText(H::famt($price));
        if (H::usends()) {
            $this->editdetail->editpricends->setText(H::famt($price));
            $this->editdetail->editprice->setText(H::famt($price - $price * H::nds(true)));
        }











        $this->updateAjax(array('qtystock', 'editprice', 'editpricends'));
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
        if ($this->_discount > 0) {
            $this->docform->discount->setVisible(true);
            $this->docform->discount->setText('Скидка ' . $this->_discount . '%');
        } else {
            $this->docform->discount->setVisible(false);
        }
    }

    public function OnAutoItem($sender) {

        $text = Item::qstr('%' . $sender->getText() . '%');
        return Item::findArray('itemname', "disabled <> 1 and (itemname like {$text} or item_code like {$text})");
    }

    //добавление нового контрагента
    public function addcustOnClick($sender) {
        $this->editcust->setVisible(true);
        $this->docform->setVisible(false);

        $this->editcust->editcustname->setText('');
        $this->editcust->editphone->setText('');
    }

    public function savecustOnClick($sender) {
        $custname = trim($this->editcust->editcustname->getText());
        if (strlen($custname) == 0) {
            $this->setError("Не введено имя");
            return;
        }
        $cust = new Customer();
        $cust->customer_name = $custname;
        $cust->phone = $this->editcust->editcustname->getText();
        $cust->save();
        $this->docform->customer->setText($cust->customer_name);
        $this->docform->customer->setKey($cust->customer_id);

        $this->editcust->setVisible(false);
        $this->docform->setVisible(true);
        $this->docform->discount->setVisible(false);
        $this->_discount = 0;
    }

    public function cancelcustOnClick($sender) {
        $this->editcust->setVisible(false);
        $this->docform->setVisible(true);
    }

    public function OnChangePriceType($sender) {
        $this->_tovarlist = array();
        $this->calcTotal();
        $this->docform->detail->Reload();
    }

}
