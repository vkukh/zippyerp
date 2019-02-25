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
use App\Entity\Doc\Document;
use App\Entity\Item;
use App\Entity\Stock;
use App\Entity\Store;
use App\Helper as H;
use App\Application as App;

/**
 * Страница  оприходование излишков
 */
class InventoryGain extends \App\Pages\Base
{

    public $_itemlist = array();
    private $_doc;
    private $_rowid = 0;
    private $_gains = array(71 => "Прочие операционные доходы");

    public function __construct($docid = 0, $basedocid = 0) {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new Date('document_date'))->setDate(time());

        $this->docform->add(new DropDownChoice('store', Store::findArray("storename", "")))->onChange($this, 'OnChangeStore');
        $this->docform->add(new DropDownChoice('gains', $this->_gains));

        $this->docform->add(new SubmitLink('addrow'))->onClick($this, 'addrowOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new Button('backtolist'))->onClick($this, 'backtolistOnClick');

        $this->add(new Form('editdetail'))->setVisible(false);
        $this->editdetail->add(new TextInput('editquantity'))->setText("1");
        $this->editdetail->add(new TextInput('editprice'));
        $this->editdetail->add(new AutocompleteTextInput('edititem'))->onText($this, 'OnAutoItem');
        $this->editdetail->edititem->onChange($this, 'OnChangeItem', true);

        $itt = array('281' => 'Товары', '201' => 'Сырье и материалы', '22' => 'МБП', '203' => 'Топливо', '204' => 'Тара', '207' => 'Запчасти',);

        $this->editdetail->add(new DropDownChoice('edittype', $itt, 281))->onChange($this, "OnItemType");

        $this->editdetail->add(new Button('cancelrow'))->onClick($this, 'cancelrowOnClick');
        $this->editdetail->add(new SubmitButton('submitrow'))->onClick($this, 'saverowOnClick');

        if ($docid > 0) {    //загружаем   содержимок  документа на страницу
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);

            $this->docform->document_date->setDate($this->_doc->document_date);


            $this->docform->store->setValue($this->_doc->headerdata['store']);
            $this->docform->gains->setValue($this->_doc->headerdata['gains']);

            foreach ($this->_doc->detaildata as $_item) {
                $stock = new Stock($_item);
                $this->_itemlist[$stock->$stock_id] = $stock;
            }
        } else {
            $this->_doc = Document::create('InventoryGain');
            $this->docform->document_number->setText($this->_doc->nextNumber());
            if ($basedocid > 0) {  //создание на  основании
                $basedoc = Document::load($basedocid);
                if ($basedoc instanceof Document) {
                    $this->_basedocid = $basedocid;
                }
            }
        }

        $this->docform->add(new DataView('detail', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, '_itemlist')), $this, 'detailOnRow'))->Reload();
        $this->OnItemType($this->editdetail->edititem);
    }

    public function detailOnRow($row) {
        $item = $row->getDataItem();

        $row->add(new Label('item', $item->itemname));
        $row->add(new Label('measure', $item->msr));
        $row->add(new Label('quantity', H::fqty($item->quantity)));
        $row->add(new Label('price', H::famt($item->price)));
        $row->add(new Label('amount', H::famt(($item->quantity ) * $item->price)));
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

        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);
        $this->_rowid = 0;
        //очищаем  форму
        $this->editdetail->edititem->setKey(0);
        $this->editdetail->edititem->setText('');

        $this->editdetail->editquantity->setText("1");

        $this->editdetail->editprice->setText("");
    }

    public function editOnClick($sender) {

        $stock = $sender->getOwner()->getDataItem();

        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);

        $this->editdetail->editquantity->setText(H::fqty($stock->quantity));
        $this->editdetail->editprice->setText(H::famt($stock->price));


        //  $list = Stock::findArrayEx("closed  <> 1   and store_id={$stock->store_id}");
        $this->editdetail->edititem->setKey($stock->stock_id);
        $this->editdetail->edititem->setText($stock->itemname);

        $this->editdetail->edittype->setValue($stock->type);


        $this->_rowid = $stock->stock_id;
    }

    public function saverowOnClick($sender) {
        $id = $this->editdetail->edititem->getKey();
        if ($id == 0) {
            $this->setError("Не вибраний ТМЦ");
            return;
        }


        $stock = Stock::load($id);


        $stock->quantity = $this->editdetail->editquantity->getText();

        $stock->price = $this->editdetail->editprice->getText();

        $stock->type = $this->editdetail->edittype->getValue();

        unset($this->_itemlist[$this->_rowid]);
        $this->_itemlist[$stock->stock_id] = $stock;
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
            'storename' => $this->docform->store->getValueName(),
            'gains' => $this->docform->gains->getValue(),
            'gainsname' => $this->_gains[$this->docform->gains->getValue()]
        );
        $this->_doc->detaildata = array();
        foreach ($this->_itemlist as $item) {
            $this->_doc->detaildata[] = $item->getData();
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
            $this->setError("Ошибка записи. Детализация в  логе");

            $logger->error($ee);
            return;
        }
    }

    public function OnItemType($sender) {
        $this->editdetail->edititem->setKey(0);
        $this->editdetail->edititem->setText('');

        $this->editdetail->editquantity->setText("1");
        $this->editdetail->editprice->setText(" ");
    }

    /**
     * Валидация   формы
     *
     */
    private function checkForm() {

        if (count($this->_itemlist) == 0) {
            $this->setError("Не введений ні один  ТМЦ");
        }

        return !$this->isError();
    }

    public function backtolistOnClick($sender) {
        App::RedirectBack();
    }

    public function OnChangeStore($sender) {
        //очистка  списка  товаров
        $this->_itemlist = array();
        $this->docform->detail->Reload();
    }

    public function OnChangeItem($sender) {

        $id = $sender->getKey();
        $stock = Stock::load($id);
        //   $item = Item::load($stock->item_id);
        $this->editdetail->editprice->setText(H::famt($stock->partion));


        $this->updateAjax(array('editprice'));
    }

    public function OnAutoItem($sender) {
        $r = array();
        $store_id = $this->docform->store->getValue();

        $text = $sender->getText();
        $list = Stock::findArrayEx("store_id={$store_id}  and (itemname like " . Stock::qstr('%' . $text . '%') . " or item_code like " . Stock::qstr('%' . $text . '%') . "  )");
        foreach ($list as $k => $v) {
            $r[$k] = $v;
        }
        return $r;
    }

}
