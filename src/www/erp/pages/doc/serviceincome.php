<?php

namespace ZippyERP\ERP\Pages\Doc;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\CheckBox;
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
use Zippy\Html\Form\AutocompleteTextInput;
use ZippyERP\ERP\Helper as H;

/**
 * Страница  ввода  акта  о  выполненных работах
 * сторонней организацией
 */
class ServiceIncome extends \ZippyERP\ERP\Pages\Base
{

    public $_itemlist = array();
    private $_doc;

    public function __construct($docid = 0, $basedocid = 0)
    {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new Date('created'))->setDate(time());
        $this->docform->add(new DropDownChoice('customer', Customer::getSellers()));
        $this->docform->add(new TextInput('reference'));
        $this->docform->add(new CheckBox('isnds'))->setChangeHandler($this, 'onIsnds');
        $this->docform->add(new CheckBox('cash'));
        $this->docform->add(new SubmitLink('addrow'))->setClickHandler($this, 'addrowOnClick');
        $this->docform->add(new Button('backtolist'))->setClickHandler($this, 'backtolistOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->setClickHandler($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->setClickHandler($this, 'savedocOnClick');

        $this->docform->add(new Label('nds'));
        $this->docform->add(new Label('total'));
        $this->add(new Form('editdetail'))->setVisible(false);
        $this->editdetail->add(new AutocompleteTextInput('edititem'))->setAutocompleteHandler($this, 'OnAutocomplete');
        $this->editdetail->add(new TextInput('editquantity'))->setText("1");
        $this->editdetail->add(new TextInput('editprice'));

        $this->editdetail->add(new Button('cancelrow'))->setClickHandler($this, 'cancelrowOnClick');
        $this->editdetail->add(new SubmitButton('saverow'))->setClickHandler($this, 'saverowOnClick');
        $this->editdetail->add(new SubmitLink('additem'))->setClickHandler($this, 'addItemOnClick');

        if ($docid > 0) {    //загружаем   содержимок  документа настраницу
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);
            $this->docform->reference->setText($this->_doc->headerdata['reference']);
            $this->docform->nds->setText($this->_doc->headerdata['nds'] / 100);
            $this->docform->isnds->setChecked($this->_doc->headerdata['isnds']);
            $this->docform->cash->setChecked($this->_doc->headerdata['cash']);
            $this->docform->created->setDate($this->_doc->document_date);
            $this->docform->customer->setValue($this->_doc->headerdata['customer']);

            foreach ($this->_doc->detaildata as $item) {
                $item = new Item($item);
                $this->_itemlist[$item->item_id] = $item;
            }
        } else {
            $this->_doc = Document::create('ServiceIncome');
            if ($basedocid > 0) {  //создание на  основании
                $basedoc = Document::load($basedocid);
                if ($basedoc instanceof Document) {
                    $this->_basedocid = $basedocid;
                    $this->docform->reference->setText($basedoc->meta_desc . " №" . $basedoc->document_number);


                    if ($basedoc->meta_name == 'PurchaseInvoice') {
                        $this->docform->isnds->setChecked($basedoc->headerdata['isnds']);
                        $this->docform->customer->setValue($basedoc->headerdata['customer']);

                        foreach ($basedoc->detaildata as $item) {
                            $item = new Item($item);
                            $this->_itemlist[$item->item_id] = $item;
                        }
                    }
                }
            }
        }

        $this->docform->add(new DataView('detail', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, '_itemlist')), $this, 'detailOnRow'))->Reload();

        $this->add(new \ZippyERP\ERP\Blocks\Item('itemdetail', $this, 'OnItem'))->setVisible(false);
    }

    public function detailOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('item', $item->itemname));
        $row->add(new Label('measure', $item->measure_name));
        $row->add(new Label('quantity', $item->quantity));
        $row->add(new Label('price', H::fm($item->price)));
        $row->add(new Label('amount', H::fm($item->quantity * $item->price)));
        $row->add(new ClickLink('delete'))->setClickHandler($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender)
    {
        $item = $sender->owner->getDataItem();
        // unset($this->_itemlist[$item->item_id]);

        $this->_itemlist = array_diff_key($this->_itemlist, array($item->item_id => $this->_itemlist[$item->item_id]));
        $this->docform->detail->Reload();
    }

    public function addrowOnClick($sender)
    {
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);
    }

    public function saverowOnClick($sender)
    {
        $id = $this->editdetail->edititem->getKey();
        if ($id == 0) {
            $this->setError("Не выбран товар");
            return;
        }
        $item = Item::load($id);
        $item->quantity = $this->editdetail->editquantity->getText();
        $item->price = $this->editdetail->editprice->getText() * 100;


        $this->_itemlist[$item->item_id] = $item;
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
        $this->docform->detail->Reload();

        //очищаем  форму
        $this->editdetail->edititem->setText('');
        $this->editdetail->edititem->setKey(0);
        $this->editdetail->editquantity->setText("1");

        $this->editdetail->editprice->setText("");
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
            'reference' => $this->docform->reference->getValue(),
            'nds' => $this->docform->nds->getText() * 100,
            'isnds' => $this->docform->isnds->isChecked(),
            'cash' => $this->docform->cash->isChecked(),
            'total' => $this->docform->total->getText() * 100
        );
        $this->_doc->detaildata = array();
        foreach ($this->_itemlist as $item) {
            $this->_doc->detaildata[] = $item->getData();
        }

        $this->_doc->amount = 100 * $this->docform->total->getText();
        $this->_doc->document_number = $this->docform->document_number->getText();
        $this->_doc->document_date = $this->docform->created->getDate();
        $isEdited = $this->_doc->document_id > 0;
        $this->_doc->intattr1 = $this->docform->customer->getValue();
        $this->_doc->save();

        if ($sender->id == 'execdoc') {
            $this->_doc->updateStatus(Document::STATE_EXECUTED);
        } else {
            $this->_doc->updateStatus($isEdited ? Document::STATE_EDITED : Document::STATE_NEW);
        }
        App::$app->getResponse()->toBack();
    }

    /**
     * Расчет  итого
     * 
     */
    private function calcTotal()
    {
        $nds = H::nds();
        if ($this->docform->isnds->isChecked() == false) {
            $nds = 0;
        }
        $total = 0;
        foreach ($this->_itemlist as $item) {
            $total = $total + $item->price * $item->quantity;
        }
        $this->docform->total->setText(H::fm($total + $total * $nds));
        $this->docform->nds->setText(H::fm($total * $nds));
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
        if ($this->docform->customer->getValue() == 0) {
            $this->setError("Не выбран  поставщик");
            return false;
        }
        return true;
    }

    public function beforeRender()
    {
        parent::beforeRender();
        $this->docform->nds->setVisible($this->docform->isnds->isChecked());
        $this->calcTotal();
    }

    public function onIsnds($sender)
    {
        
    }

    public function backtolistOnClick($sender)
    {
        App::$app->getResponse()->toBack();
    }

    public function addItemOnClick($sender)
    {
        $this->editdetail->setVisible(false);
        $this->itemdetail->open();
    }

    public function OnItem($cancel = false)
    {
        $this->editdetail->setVisible(true);
        if ($cancel == true)
            return;

        $item = $this->itemdetail->getData();

        $this->editdetail->edititem->setText($item->itemname);
        $this->editdetail->edititem->setKey($item->item_id);
    }

    // автолоад списка  товаров
    public function OnAutocomplete($sender)
    {
        $text = $sender->getValue();
        $answer = array();
        $conn = \ZCL\DB\DB::getConnect();
        $sql = "select item_id,itemname from erp_item where itemname  like '%{$text}%' and item_type in( " . Item::ITEM_TYPE_GOODS . "," . Item::ITEM_TYPE_STUFF . "," . Item::ITEM_TYPE_MBP . "   ) order  by itemname   limit 0,20";
        $rs = $conn->Execute($sql);
        foreach ($rs as $row) {
            $answer[$row['item_id']] = $row['itemname'];
        }
        return $answer;
    }

    /*
      События  жизненного  цикла  страницы, раскоментировать нужное
      public function beforeRequest(){
      parent::beforeRequest();

      }
      public function afterRequest(){
      parent::afterRequest();

      }
      public function beforeRender(){
      parent::beforeRender();

      }
      public function afterRender(){
      parent::afterRender();

      }
     */
}
