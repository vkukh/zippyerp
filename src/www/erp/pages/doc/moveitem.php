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
use ZippyERP\ERP\Entity\Doc\Document;
use ZippyERP\ERP\Entity\Item;
use ZippyERP\ERP\Entity\Stock;
use ZippyERP\ERP\Entity\Store;

/**
 * Страница  ввода перемещения товаров
 */
class MoveItem extends \ZippyERP\ERP\Pages\Base
{

    public $_itemlist = array();
    private $_doc;

    public function __construct($docid = 0)
    {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new Date('created', time()));
        $this->docform->add(new DropDownChoice('storefrom'))->setChangeHandler($this, 'OnChangeStore');
        $this->docform->add(new DropDownChoice('storeto'))->setChangeHandler($this, 'OnChangeStore');
        $this->docform->storefrom->setOptionList(Store::findArray("storename", "store_type=" . Store::STORE_TYPE_OPT));
        $this->docform->storeto->setOptionList(Store::findArray("storename", ''));

        $this->docform->add(new SubmitLink('addrow'))->setClickHandler($this, 'addrowOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->setClickHandler($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->setClickHandler($this, 'savedocOnClick');
        $this->docform->add(new Button('backtolist'))->setClickHandler($this, 'backtolistOnClick');


        $this->add(new Form('editdetail'))->setVisible(false);
        $this->editdetail->add(new DropDownChoice('edititem'))->setChangeHandler($this, 'OnChangeItem');
        $this->editdetail->add(new TextInput('editquantity'))->setText("1");
        $this->editdetail->add(new TextInput('editprice'))->setText("0");

        $this->editdetail->add(new Label('qtystock'));
        $this->editdetail->add(new SubmitButton('saverow'))->setClickHandler($this, 'saverowOnClick');
        $this->editdetail->add(new Button('cancelrow'))->setClickHandler($this, 'cancelrowOnClick');

        if ($docid > 0) {    //загружаем   содержимок  документа на страницу
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);
            $this->docform->created->setDate($this->_doc->document_date);
            $this->docform->storefrom->setValue($this->_doc->headerdata['storefrom']);
            $this->docform->storeto->setValue($this->_doc->headerdata['storeto']);


            foreach ($this->_doc->detaildata as $item) {
                $stock = new Stock($item);
                $this->_itemlist[$stock->stock_id] = $stock;
            }
        } else {
            $this->_doc = Document::create('MoveItem');
        }

        $this->docform->add(new DataView('detail', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, '_itemlist')), $this, 'detailOnRow'))->Reload();
    }

    public function detailOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('item', $item->itemname));

        $row->add(new Label('measure', $item->measure_name));
        $row->add(new Label('quantity', $item->quantity));
        $row->add(new Label('price', number_format($item->price / 100, 2, '.', '')));
        $row->add(new ClickLink('delete'))->setClickHandler($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender)
    {
        $item = $sender->owner->getDataItem();
        // unset($this->_itemlist[$item->item_id]);

        $this->_itemlist = array_diff_key($this->_itemlist, array($item->stock_id => $this->_itemlist[$item->stock_id]));
        $this->docform->detail->Reload();
    }

    public function addrowOnClick($sender)
    {
        if ($this->docform->storefrom->getValue() == 0) {
            $this->setError("Выберите склад-источник");
            return;
        }
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);
        $this->editdetail->edititem->setOptionList(Stock::findArrayEx(" store_id=" . $this->docform->storefrom->getValue()));
    }

    public function saverowOnClick($sender)
    {
        $id = $this->editdetail->edititem->getValue();
        if ($id == 0) {
            $this->setError("Не выбран ТМЦ");
            return;
        }


        $stock = Stock::load($id);
        $stock->quantity = $this->editdetail->editquantity->getText();

        $store = Store::load($this->docform->storeto->getValue());
        if ($store->store_type == Store::STORE_TYPE_OPT) {
            $stock->price = $stock->partion;  //перемещение на  оптовый  склад
        } else {
            $stock->price = $this->editdetail->editprice->getText() * 100;
        }
        $this->_itemlist[$stock->stock_id] = $stock;
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
        $this->docform->detail->Reload();

        //очищаем  форму
        $this->editdetail->edititem->setValue(0);
        $this->editdetail->editquantity->setText("1");
        $this->editdetail->editprice->setText("1");
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



        $this->_doc->headerdata = array(
            'storefrom' => $this->docform->storefrom->getValue(),
            'storeto' => $this->docform->storeto->getValue()
        );
        $this->_doc->detaildata = array();
        foreach ($this->_itemlist as $item) {
            $this->_doc->detaildata[] = $item->getData();
        }


        $this->_doc->document_number = $this->docform->document_number->getText();
        $this->_doc->document_date = strtotime($this->docform->created->getText());
        $isEdited = $this->_doc->document_id > 0;

        $this->_doc->save();
        if ($sender->id == 'execdoc') {
            $this->_doc->updateStatus(Document::STATE_EXECUTED);
        } else {
            $this->_doc->updateStatus($isEdited ? Document::STATE_EDITED : Document::STATE_NEW);
        }
        App::Redirect('\ZippyERP\ERP\Pages\Register\DocList');
    }

    /**
     * Валидация   формы
     * 
     */
    private function checkForm()
    {

        if (count($this->_itemlist) == 0) {
            $this->setError("Не введен ни один  товар");
            return false;
        }
        if ($this->docform->storeto->getValue() == $this->docform->storefrom->getValue()) {
            $this->setError("Выбран  тот  же  склад для  получения");
            return false;
        }


        return true;
    }

    public function backtolistOnClick($sender)
    {
        App::Redirect("\\ZippyERP\\ERP\\Pages\\Register\\DocList");
    }

    public function OnChangeItem($sender)
    {
        $stock_id = $sender->getValue();
        $stock = Stock::load($stock_id);
        $this->editdetail->qtystock->setText(Stock::getQuantity($stock_id, $this->docform->created->getDate()) . ' ' . $stock->measure_name);
        $store = Store::load($this->docform->storeto->getValue());
        if ($store->store_type == Store::STORE_TYPE_OPT) {
            $this->editdetail->editprice->setText(number_format($stock->partion / 100, 2, '.', ''));
        } else {
            $item = Item::load($stock->item_id);
            $this->editdetail->editprice->setText(number_format($item->price / 100, 2, '.', ''));
        }
    }

    public function OnChangeStore($sender)
    {
        if ($sender->id == 'storefrom') {
            //очистка  списка  товаров
            $this->_itemlist = array();
            $this->docform->detail->Reload();
        }
        if ($sender->id == 'storeto') {

            $store = Store::load($sender->getValue());
            if ($store->store_type == Store::STORE_TYPE_OPT) {
                $this->editdetail->editprice->setVisible(false);
            } else {
                $this->editdetail->editprice->setVisible(true);
            }
        }
    }

}
