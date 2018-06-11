<?php

namespace ZippyERP\ERP\Pages\Doc;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\CheckBox;
use Zippy\Html\Form\Date;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use \Zippy\Html\Form\AutocompleteTextInput;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Link\SubmitLink;
use ZippyERP\ERP\Entity\Doc\Document;
use ZippyERP\ERP\Entity\Item;
use ZippyERP\ERP\Entity\Stock;
use ZippyERP\ERP\Entity\Store;
use ZippyERP\ERP\Helper as H;
use Zippy\WebApplication as App;

/**
 * Страница  ввода перемещения товаров
 */
class MoveItem extends \ZippyERP\ERP\Pages\Base
{

    public $_itemlist = array();
    private $_doc;
    private $_rowid = 0;

    public function __construct($docid = 0) {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new Date('document_date', time()));
        $this->docform->add(new CheckBox('plan'));
        $this->docform->add(new DropDownChoice('storefrom', Store::findArray("storename", "store_type=" . Store::STORE_TYPE_OPT), Store::getBased()))->onChange($this, 'OnChangeStore');
        $this->docform->add(new DropDownChoice('storeto', Store::findArray("storename", "store_type=" . Store::STORE_TYPE_OPT), Store::getBased()))->onChange($this, 'OnChangeStore');


        $this->docform->add(new SubmitLink('addrow'))->onClick($this, 'addrowOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new Button('backtolist'))->onClick($this, 'backtolistOnClick');

        $this->add(new Form('editdetail'))->setVisible(false);
        $this->editdetail->add(new DropDownChoice('edittype'))->onChange($this, "OnItemType");
        $this->editdetail->add(new AutocompleteTextInput('edititem'))->onText($this, 'OnAutocompleteItem');
        $this->editdetail->edititem->onChange($this, 'OnChangeItem', false);

        $this->editdetail->add(new TextInput('editquantity'))->setText("1");
        $this->editdetail->add(new TextInput('editprice'))->setVisible(false);


        $this->editdetail->add(new Label('qtystock'));
        $this->editdetail->add(new SubmitButton('saverow'))->onClick($this, 'saverowOnClick');
        $this->editdetail->add(new Button('cancelrow'))->onClick($this, 'cancelrowOnClick');

        if ($docid > 0) {    //загружаем   содержимок  документа на страницу
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);
            $this->docform->document_date->setDate($this->_doc->document_date);
            $this->docform->storefrom->setValue($this->_doc->headerdata['storefrom']);
            $this->docform->storeto->setValue($this->_doc->headerdata['storeto']);
            $this->docform->plan->setChecked($this->_doc->headerdata['plan']);


            foreach ($this->_doc->detaildata as $item) {
                $stock = new Stock($item);
                $this->_itemlist[$stock->stock_id] = $stock;
            }
        } else {
            $this->_doc = Document::create('MoveItem');
        }

        $this->docform->add(new DataView('detail', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, '_itemlist')), $this, 'detailOnRow'))->Reload();
        $this->OnChangeStore($this->docform->storeto);
    }

    public function detailOnRow($row) {
        $item = $row->getDataItem();

        $row->add(new Label('item', $item->itemname));

        $row->add(new Label('measure', $item->measure_name));
        $row->add(new Label('quantity', $item->quantity / 1000));
        $row->add(new Label('price', H::fm($item->price)));
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender) {
        $item = $sender->owner->getDataItem();
        // unset($this->_itemlist[$item->item_id]);

        $this->_itemlist = array_diff_key($this->_itemlist, array($item->stock_id => $this->_itemlist[$item->stock_id]));
        $this->docform->detail->Reload();
    }

    public function addrowOnClick($sender) {
        if ($this->docform->storefrom->getValue() == 0) {
            $this->setError("Виберіть склад-джерело");
            return;
        }
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);
        $this->editdetail->edititem->setKey(0);
        $this->editdetail->edititem->setValue('');
        $this->editdetail->qtystock->setText('');
    }

    public function editOnClick($sender) {
        $stock = $sender->getOwner()->getDataItem();
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);

        $this->editdetail->editquantity->setText($stock->quantity / 1000);
        $this->editdetail->editprice->setText(H::fm($stock->price));

        $this->editdetail->edittype->setValue($stock->type);


        $this->editdetail->edititem->setKey($stock->stock_id);
        $this->editdetail->edititem->setValue($stock->itemname);
        $this->editdetail->qtystock->setText(Stock::getQuantity($stock->stock_id, $this->docform->document_date->getDate()) / 1000 . ' ' . $stock->measure_name);

        $this->_rowid = $stock->stock_id;
    }

    public function saverowOnClick($sender) {
        $id = $this->editdetail->edititem->getKey();
        if ($id == 0) {
            $this->setError("Не вибраний ТМЦ");
            return;
        }


        $stock = Stock::load($id);
        $stock->quantity = 1000 * $this->editdetail->editquantity->getText();
        $stock->type = $this->editdetail->edittype->getValue();

        $store = Store::load($this->docform->storeto->getValue());
        if ($store->store_type == Store::STORE_TYPE_OPT) {
            // $stock->price = $stock->partion;  //перемещение на  оптовый  склад
        } else {
            $stock->partion = $stock->price;
            $stock->price = $this->editdetail->editprice->getText() * 100;
        }
        $this->_itemlist[$stock->stock_id] = $stock;
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
        $this->docform->detail->Reload();

        //очищаем  форму
        $this->editdetail->edititem->setKey(0);
        $this->editdetail->edititem->setValue('');
        $this->editdetail->editquantity->setText("1");
        $this->editdetail->editprice->setText(" ");
    }

    public function cancelrowOnClick($sender) {
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
        $this->editdetail->edititem->setKey(0);
        $this->editdetail->edititem->setText('');

        $this->editdetail->editquantity->setText("1");
        $this->editdetail->editprice->setText(" ");
    }

    public function savedocOnClick($sender) {
        if ($this->checkForm() == false) {
            return;
        }


        $this->_doc->headerdata = array(
            'plan' => $this->docform->plan->isChecked(),
            'storefrom' => $this->docform->storefrom->getValue(),
            'storeto' => $this->docform->storeto->getValue()
        );
        $this->_doc->detaildata = array();
        foreach ($this->_itemlist as $item) {
            $this->_doc->detaildata[] = $item->getData();
        }


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
     * Валидация   формы
     *
     */
    private function checkForm() {

        if (strlen(trim($this->docform->document_number->getText())) == 0) {
            $this->setError("Не введений номер документу");
        }
        if (count($this->_itemlist) == 0) {
            $this->setError("Не введений ні один  товар");
        }
        if ($this->docform->storeto->getValue() == $this->docform->storefrom->getValue()) {
            $this->setError("Вибраний той самий склад-отримувач");
        }


        return !$this->isError();
    }

    public function backtolistOnClick($sender) {
        App::RedirectBack();
    }

    public function OnChangeItem($sender) {
        $stock_id = $sender->getKey();
        $stock = Stock::load($stock_id);
        $this->editdetail->qtystock->setText(Stock::getQuantity($stock_id, $this->docform->document_date->getDate(), $this->editdetail->edittype->getValue()) / 1000 . ' ' . $stock->measure_name);
        $store = Store::load($this->docform->storeto->getValue());
        if ($store->store_type == Store::STORE_TYPE_OPT) {
            // $this->editdetail->editprice->setText(H::fm($stock->price));
        } else {
            $item = Item::load($stock->item_id);
            $this->editdetail->editprice->setText(H::fm($item->getRetPrice($stock->price)));
        }
        if ($store->store_type == Store::STORE_TYPE_RET) {
            //если  уже   есть  товар  в  магазине  берем  цену  оттуда
            $stock = Stock::getFirst("store_id={$store->store_id} and item_id={$stock->item_id} and closed <> 1");
            if ($stock instanceof Stock) {
                $this->editdetail->editprice->setText(H::fm($stock->price));
            }
        }
    }

    public function OnChangeStore($sender) {
        if ($sender->id == 'storefrom') {
            //очистка  списка  товаров
            $this->_itemlist = array();
            $this->docform->detail->Reload();
        }
        $store = Store::load($this->docform->storeto->getValue());
        if ($store->store_type == Store::STORE_TYPE_OPT) {
            $this->editdetail->editprice->setVisible(false);
            $this->editdetail->edittype->setOptionList(array(281 => 'Товар', 201 => 'Материал', 22 => 'МПБ', 25 => 'Полуфабрикат', 26 => 'Готовая продукция'));
            $this->editdetail->edittype->setValue(281);
        } else {
            $this->editdetail->editprice->setVisible(true);
            $this->editdetail->edittype->setOptionList(array(281 => 'Товар', 26 => 'Готовая продукция'));
            $this->editdetail->edittype->setValue(281);
        }
    }

    public function OnItemType($sender) {
        $this->editdetail->edititem->setKey(0);
        $this->editdetail->edititem->setText('');

        $this->editdetail->editquantity->setText("1");
        $this->editdetail->editprice->setText(" ");
    }

    public function OnAutocompleteItem($sender) {
        $text = Store::qstr('%' . trim($sender->getText()) . '%');
        $store_id = $this->docform->storefrom->getValue();

        return Stock::findArrayEx("store_id={$store_id} and closed <> 1 and  (itemname  like {$text} or item_code  like {$text} ) and   stock_id in(select stock_id  from  erp_account_subconto  where  account_id= " . $this->editdetail->edittype->getValue() . ") ");
    }

    public function beforeRender() {
        parent::beforeRender();
    }

}
