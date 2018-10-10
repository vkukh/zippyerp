<?php

namespace ZippyERP\ERP\Pages\Doc;

use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Form\Button;
use \Zippy\Html\Form\CheckBox;
use \Zippy\Html\Form\Date;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\SubmitButton;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\AutocompleteTextInput;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Link\SubmitLink;
use \ZippyERP\ERP\Entity\Customer;
use \ZippyERP\ERP\Entity\Doc\Document;
use \ZippyERP\ERP\Entity\Item;
use \ZippyERP\ERP\Entity\Stock;
use \ZippyERP\ERP\Entity\Employee;
use \ZippyERP\ERP\Entity\Store;
use \ZippyERP\ERP\Helper as H;
use \Zippy\WebApplication as App;

/**
 * Страница  ввода  розничной  накладной
 */
class RetailIssue extends \ZippyERP\ERP\Pages\Base
{

    public $_tovarlist = array();
    private $_doc;
    private $_rowid = 0;
    private $_discount = 0;
    private $_opt = 0;

    public function __construct($docid = 0, $basedocid = 0) {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new Date('document_date'))->setDate(time());
        $this->docform->add(new CheckBox('plan'));
        $this->docform->add(new CheckBox('ccard'));

        $this->docform->add(new DropDownChoice('emp', Employee::findArray("shortname", "", 'shortname')));
        $this->docform->add(new DropDownChoice('store', Store::findArray("storename", "store_type = " . Store::STORE_TYPE_RET . " or store_type = " . Store::STORE_TYPE_OPT, "store_type  ")))->onChange($this, 'OnChangeStore');
        $this->docform->store->selectFirst();

        $this->docform->add(new AutocompleteTextInput('customer'))->onText($this, 'OnAutoCustomer');
        $this->docform->customer->onChange($this, 'OnChangeCustomer');

        $this->docform->add(new SubmitLink('addrow'))->onClick($this, 'addrowOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new Button('backtolist'))->onClick($this, 'backtolistOnClick');

        $this->docform->add(new Label('totalnds'));
        $this->docform->add(new Label('total'));

        $this->add(new Form('editdetail'))->setVisible(false);
        $this->editdetail->add(new TextInput('editquantity'))->setText("1");
        $this->editdetail->add(new TextInput('editprice'));

        $this->editdetail->add(new AutocompleteTextInput('edittovar'))->onText($this, 'OnAutoItem');
        $this->editdetail->edittovar->onChange($this, 'OnChangeItem', true);

        $this->editdetail->add(new Label('qtystock'));

        $this->editdetail->add(new Button('cancelrow'))->onClick($this, 'cancelrowOnClick');
        $this->editdetail->add(new SubmitButton('submitrow'))->onClick($this, 'saverowOnClick');

        $this->add(new Form('mform'));
        $this->mform->add(new TextInput('mcustname'));


        $this->mform->add(new TextInput('mphone'));

        $this->mform->add(new SubmitButton('madd'))->onClick($this, 'mformOnClick');


        if ($docid > 0) {    //загружаем   содержимок  документа настраницу
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);

            $this->docform->totalnds->setText(H::fm($this->_doc->headerdata['totalnds']));
            $this->docform->document_date->setDate($this->_doc->document_date);
            $this->docform->plan->setChecked($this->_doc->headerdata['plan']);
            $this->docform->ccard->setChecked($this->_doc->headerdata['ccard']);

            $this->docform->store->setValue($this->_doc->headerdata['store']);
            $this->docform->emp->setValue($this->_doc->headerdata['emp']);
            $this->docform->customer->setKey($this->_doc->headerdata['customer']);
            $this->docform->customer->setText($this->_doc->headerdata['customername']);

            foreach ($this->_doc->detaildata as $item) {
                $item = new Item($item);
                $this->_tovarlist[$item->item_id] = $item;
            }
        } else {
            $this->_doc = Document::create('RetailIssue');
            $this->docform->document_number->setText($this->_doc->nextNumber());
            if ($basedocid > 0) {  //создание на  основании
                $basedoc = Document::load($basedocid);
                if ($basedoc instanceof Document) {
                    $this->_basedocid = $basedocid;
                    //$this->docform->base->setText($basedoc->meta_desc . " №" . $basedoc->document_number);


                    if ($basedoc->meta_name == 'CustomerOrder') {
                        $nds = H::nds(true);  // если  в  заказе   цена  с  НДС  получаем  базовую  цену

                        $this->docform->customer->setKey($basedoc->headerdata['customer']);
                        $this->docform->customer->setText($basedoc->headerdata['customername']);
                        $this->docform->emp->setValue($basedoc->headerdata['emp']);



                        foreach ($basedoc->detaildata as $item) {
                            $item = new Item($item);
                            //находим  последнюю партию по  первому складу
                            $options = $this->docform->store->getOptionList();
                            $keys = array_keys($options);
                            $stock = Stock::getFirst("closed <> 1 and  item_id={$item->item_id} and store_id=" . $keys[0], 'stock_id desc');
                            if ($stock instanceof Stock) {
                                $stock->quantity = $item->quantity;
                                // $stock->pricends = $item->pricends;
                                $stock->price = $item->price;


                                $this->_tovarlist[$stock->stock_id] = $stock;
                            } else {
                                $this->setError('Не знайдений на складі  товар ' . $item->itemname);
                            }
                        }
                    }
                }
            }
        }

        $this->docform->add(new DataView('detail', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, '_tovarlist')), $this, 'detailOnRow'))->Reload();

        $this->OnChangeCustomer(null);

        $this->_opt = Store::load($this->docform->store->getValue())->store_type == Store::STORE_TYPE_OPT;
    }

    public function detailOnRow($row) {
        $item = $row->getDataItem();

        $row->add(new Label('code', $item->item_code));
        $row->add(new Label('tovar', $item->itemname));
        $row->add(new Label('measure', $item->measure_name));
        $row->add(new Label('quantity', $item->quantity / 1000));
        $row->add(new Label('price', H::fm($item->price)));
        $row->add(new Label('amount', H::fm(($item->quantity / 1000) * $item->price)));
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender) {
        $tovar = $sender->owner->getDataItem();
        // unset($this->_tovarlist[$tovar->tovar_id]);

        $this->_tovarlist = array_diff_key($this->_tovarlist, array($tovar->stock_id => $this->_tovarlist[$tovar->stock_id]));
        $this->docform->detail->Reload();
    }

    public function addrowOnClick($sender) {
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);
        $this->_rowid = 0;
        $this->editdetail->editquantity->setText('1');
        $this->editdetail->editprice->setText('0');
    }

    public function editOnClick($sender) {
        $stock = $sender->getOwner()->getDataItem();
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);

        $this->editdetail->editquantity->setText($stock->quantity / 1000);
        $this->editdetail->editprice->setText(H::fm($stock->price));


        //$list = Stock::findArrayEx("closed  <> 1 and   store_id={$stock->store_id}");
        $this->editdetail->edittovar->setKey($stock->stock_id);
        $this->editdetail->edittovar->setText($stock->itemname);


        $this->editdetail->qtystock->setText(Stock::getQuantity($stock->stock_id, $this->docform->document_date->getDate()) . ' ' . $stock->measure_name);

        $this->_rowid = $stock->stock_id;
    }

    public function saverowOnClick($sender) {
        $id = $this->editdetail->edittovar->getKey();
        if ($id == 0) {
            $this->setError("Не вибраний товар");
            return;
        }
        $stock = Stock::load($id);
        $stock->quantity = 1000 * $this->editdetail->editquantity->getText();
        $stock->partion = $stock->price;
        $stock->price = $this->editdetail->editprice->getText() * 100;
        if (!($stock->price > 0)) {
            $this->setError("Не введена  ціна");
            return;
        }
        if (!($stock->quantity > 0)) {
            $this->setError("Не введена  кількість");
            return;
        }


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
        $this->editdetail->qtystock->setText("");
    }

    public function cancelrowOnClick($sender) {
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
        //очищаем  форму
        $this->editdetail->edittovar->setKey(0);
        $this->editdetail->edittovar->setText('');

        $this->editdetail->editquantity->setText("1");

        $this->editdetail->editprice->setText("");
        $this->editdetail->qtystock->setText("");
    }

    public function savedocOnClick($sender) {
        $this->_doc->document_number = $this->docform->document_number->getText();
        $this->_doc->document_date = $this->docform->document_date->getDate();

        if ($this->checkForm() == false) {
            return;
        }

        $this->calcTotal();

        $this->_doc->headerdata = array(
            'customer' => $this->docform->customer->getKey(),
            'customername' => $this->docform->customer->getText(),
            'store' => $this->docform->store->getValue(),
            'emp' => $this->docform->emp->getValue(),
            'empname' => $this->docform->emp->getValueName(),
            'plan' => $this->docform->plan->isChecked(),
            'ccard' => $this->docform->ccard->isChecked(),
            'total' => $this->docform->total->getText() * 100,
            'totalnds' => $this->docform->totalnds->getText() * 100
        );
        $this->_doc->detaildata = array();
        foreach ($this->_tovarlist as $tovar) {
            $this->_doc->detaildata[] = $tovar->getData();
        }

        $this->_doc->amount = 100 * $this->docform->total->getText();
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
     * Расчет  итого
     *
     */
    private function calcTotal() {
        $total = 0;
        foreach ($this->_tovarlist as $tovar) {
            $total = $total + $tovar->price * ($tovar->quantity / 1000);
        }

        $nds = $total * H::nds(true);
        $this->docform->totalnds->setText(H::fm($nds));
        $this->docform->total->setText(H::fm($total));
    }

    /**
     * Валидация   формы
     *
     */
    private function checkForm() {

        if (count($this->_tovarlist) == 0) {
            $this->setError("Не введено жодного товару");
        }
        if ($this->docform->customer->getKey() == 0) {
            // $this->setError("Не введений   покупець");
        }
        return !$this->isError();
    }

    public function beforeRender() {
        parent::beforeRender();

        $this->calcTotal();
    }

    public function backtolistOnClick($sender) {
        App::RedirectBack();
    }

    public function OnChangeStore($sender) {
        //очистка  списка  товаров
        $this->_tovarlist = array();
        $this->docform->detail->Reload();
        $store_id = $this->docform->store->getValue();
        $this->calcTotal();
        $this->_opt = Store::load($store_id)->store_type == Store::STORE_TYPE_OPT;
    }

    public function OnAutoCustomer($sender) {
        $text = Customer::qstr('%' . $sender->getText() . '%');
        $list = Customer::findArray("customer_name", "cust_type in(1,3,5) and  (customer_name like {$text} or phone like {$text} )");
        return $list;
    }

    public function OnChangeCustomer($sender) {

        $customer_id = $this->docform->customer->getKey();
        if ($customer_id > 0) {
            $customer = Customer::load($customer_id);
            $this->_discount = $customer->discount;
        }
        $this->calcTotal();
    }

    public function OnAutoItem($sender) {
        $r = array();
        $store_id = $this->docform->store->getValue();

        $text = $sender->getText();
        $list = Stock::findArrayEx("store_id={$store_id} and closed <> 1 and (itemname like " . Stock::qstr('%' . $text . '%') . " or item_code like " . Stock::qstr('%' . $text . '%') . "  )");
        return $list;
    }

    public function OnChangeItem($sender) {
        $id = $sender->getKey();
        $stock = Stock::load($id);
        $price = 0;
        if ($this->_opt) {
            $item = Item::load($stock->item_id);
            $price = $item->getOptPrice($stock->price);
        } else {
            $price = $stock->price;
        }

        $price = $price - $price / 100 * $this->_discount;


        $this->editdetail->editprice->setText(H::fm($price));


        $this->editdetail->qtystock->setText(Stock::getQuantity($id, $this->docform->document_date->getDate()) / 1000 . ' ' . $stock->measure_name);

        $this->updateAjax(array('editprice', 'qtystock'));
    }

    //создаем нового клиента и всталяем  форму
    public function mformOnClick($sender) {
        $custname = $this->mform->mcustname->getText();

        $phone = $this->mform->mphone->getText();

        $customer = new Customer();
        $customer->customer_name = $custname;
        $customer->cust_type = Customer::TYPE_CLIENT;
        $customer->phone = $phone;
        $customer->save();


        $customer->save();


        $this->mform->clean();
        $this->docform->customer->setKey($customer->customer_id);
        $this->docform->customer->setText($customer->customer_name);
    }

}
