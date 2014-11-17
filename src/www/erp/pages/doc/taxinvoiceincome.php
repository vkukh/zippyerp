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
use ZippyERP\ERP\Entity\Stock;
use ZippyERP\ERP\Helper as H;

/**
 * Страница  ввода входящей налоговой  накладной
 */
class TaxInvoiceIncome extends \ZippyERP\ERP\Pages\Base
{

    public $_tovarlist = array();
    private $_doc;
    private $_basedocid = 0;

    public function __construct($docid = 0, $basedocid = 0)
    {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));

        $this->docform->add(new Date('created'))->setDate(time());

        $this->docform->add(new DropDownChoice('customer', Customer::getSellers()));

        $this->docform->add(new TextInput('based'));

        $this->docform->add(new SubmitLink('addrow'))->setClickHandler($this, 'addrowOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->setClickHandler($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->setClickHandler($this, 'savedocOnClick');
        $this->docform->add(new Button('backtolist'))->setClickHandler($this, 'backtolistOnClick');

        $this->docform->add(new Label('nds'));
        $this->docform->add(new Label('total'));
        $this->add(new Form('editdetail'))->setVisible(false);
        $this->editdetail->add(new DropDownChoice('edittovar'));
        $this->editdetail->add(new TextInput('editquantity'))->setText("1");
        $this->editdetail->add(new TextInput('editprice'));



        $this->editdetail->add(new Button('cancelrow'))->setClickHandler($this, 'cancelrowOnClick');
        $this->editdetail->add(new SubmitButton('submitrow'))->setClickHandler($this, 'saverowOnClick');

        if ($docid > 0) {    //загружаем   содержимок  документа настраницу
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);

            $this->docform->nds->setText($this->_doc->headerdata['nds'] / 100);
            $this->docform->created->setDate($this->_doc->document_date);

            $this->docform->based->setText($this->_doc->headerdata['based']);
            $this->docform->customer->setValue($this->_doc->headerdata['customer']);

            foreach ($this->_doc->detaildata as $item) {
                $item = new Item($item);
                $this->_tovarlist[$item->item_id] = $item;
            }
        } else {
            $this->_doc = Document::create('TaxInvoiceIncome');
            $this->docform->document_number->setText($this->_doc->nextNumber());

            if ($basedocid > 0) {  //создание на  основании
                $basedoc = Document::load($basedocid);
                if ($basedoc instanceof Document) {
                    $this->_basedocid = $basedocid;
                    $this->docform->based->setText($basedoc->meta_desc . " №" . $basedoc->document_number);


                    if ($basedoc->meta_name == 'GoodsReceipt') {
                        //  $this->docform->nds->setText($basedoc->headerdata['nds'] / 100);
                        $this->docform->customer->setValue($basedoc->headerdata['customer']);

                        foreach ($basedoc->detaildata as $item) {
                            $item = new Item($item);
                            $this->_tovarlist[$stock->item_id] = $item;
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
        $row->add(new Label('amount', H::fm($item->quantity * $item->price)));
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
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);
        $this->editdetail->edittovar->setOptionList(Stock::findArrayEx());
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
        // $stock->partion = $stock->price;
        $stock->price = $this->editdetail->editprice->getText() * 100;


        $this->_tovarlist[$stock->item_id] = $stock;
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
        $this->docform->detail->Reload();

        //очищаем  форму
        $this->editdetail->edittovar->setValue(0);
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
            'based' => $this->docform->based->getText(),
            'nds' => $this->docform->nds->getText() * 100
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
            $this->_doc->updateStatus(Document::STATE_CLOSED);
        } else {
            $this->_doc->updateStatus($isEdited ? Document::STATE_EDITED : Document::STATE_NEW);
        }
        if ($this->_basedocid > 0) {
            $this->_doc->AddConnectedDoc($this->_basedocid);
            $this->_basedocid = 0;
        }
        App::$app->getResponse()->toBack();
    }

    /**
     * Расчет  итого
     * 
     */
    private function calcTotal()
    {
        $total = 0;
        foreach ($this->_tovarlist as $tovar) {
            $total = $total + $tovar->price * $tovar->quantity;
        }
        $nds = H::nds() * $total;
        $this->docform->nds->setText(H::fm($nds));
        $this->docform->total->setText(H::fm($total + $nds));
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

        $this->calcTotal();
    }

    public function backtolistOnClick($sender)
    {
        App::$app->getResponse()->toBack();
    }

}
