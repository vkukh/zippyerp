<?php

namespace ZippyERP\ERP\Pages\Doc;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Form\AutocompleteTextInput;
use Zippy\Html\Form\Date;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Link\SubmitLink;
use Zippy\Html\Panel;
use ZippyERP\System\Application as App;
use ZippyERP\System\System;
use ZippyERP\ERP\Entity\Doc\Document;
use ZippyERP\ERP\Entity\Item;
use ZippyERP\ERP\Entity\CapitalAsset;
use \ZippyERP\ERP\Helper as H;

/**
 * Страница  ввода  ОС в  эксплуатацию
 */
class NAInMaintenance extends \ZippyERP\ERP\Pages\Base
{

    public $_tovarlist = array();
    private $_doc;
    private $_rowid = 0;

    public function __construct($docid = 0)
    {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new Date('document_date'))->setDate(time());



        $this->docform->add(new SubmitLink('addrow'))->setClickHandler($this, 'addrowOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->setClickHandler($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->setClickHandler($this, 'savedocOnClick');
        $this->docform->add(new Button('backtolist'))->setClickHandler($this, 'backtolistOnClick');

        $this->add(new Form('editdetail'))->setVisible(false);
        //   $this->editdetail->add(new DropDownChoice('editinventory')) ;
        $this->editdetail->add(new TextInput('editprice'));

        $this->editdetail->add(new AutocompleteTextInput('edittovar'))->setAutocompleteHandler($this, "OnAutoItem");
        //  $this->editdetail->edittovar->setChangeHandler($this, 'OnChangeItem');



        $this->editdetail->add(new Button('cancelrow'))->setClickHandler($this, 'cancelrowOnClick');
        $this->editdetail->add(new SubmitButton('submitrow'))->setClickHandler($this, 'saverowOnClick');

        if ($docid > 0) {    //загружаем   содержимок  документа на страницу
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);

            $this->docform->document_date->setDate($this->_doc->document_date);




            foreach ($this->_doc->detaildata as $item) {
                $item = new Item($item);
                $this->_tovarlist[$item->item_id] = $item;
            }
        } else {
            $this->_doc = Document::create('NAInMaintenance');
            $this->docform->document_number->setText($this->_doc->nextNumber());
        }

        $this->docform->add(new DataView('detail', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, '_tovarlist')), $this, 'detailOnRow'))->Reload();
    }

    public function detailOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('tovar', $item->itemname));
        $row->add(new Label('inventory', $item->inventory));
        //       $row->add(new Label('price', H::fm($item->price)));
        $row->add(new ClickLink('edit'))->setClickHandler($this, 'editOnClick');
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
        $this->_rowid = 0;
    }

    public function editOnClick($sender)
    {
        $os = $sender->getOwner()->getDataItem();
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);

        // $this->editdetail->editinventory->setText($stock->inventory  );
        $this->editdetail->editprice->setText(H::fm($os->value));


        $this->editdetail->edittovar->setKey($os->item_id);
        $this->editdetail->edittovar->setText($os->inventory . ', ' . $os->itemname);


        $this->_rowid = $os->item_id;
    }

    public function saverowOnClick($sender)
    {
        $id = $this->editdetail->edittovar->getKey();
        if ($id == 0) {
            $this->setError("Не выбрано ОС");
            return;
        }
        $ca = CapitalAsset::load($id);
        //$ca->inventory =  $this->editdetail->editinventory->getValue();
        if (strlen($ca->inventory) == 0) {
            //  $this->setError("Не выбран инвентарный номер");
            //   return;
        }
        // $stock->partion = $stock->price;
        $ca->value = $this->editdetail->editprice->getText() * 100;
        $ca->quantity = 1000;

        unset($this->_tovarlist[$this->_rowid]);
        $this->_tovarlist[$ca->item_id] = $ca;
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
        $this->docform->detail->Reload();

        //очищаем  форму
        $this->editdetail->edittovar->setKey(0);
        $this->editdetail->edittovar->setText('');
        //   $this->editdetail->editinventory->setOptionList(array());

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


        $this->_doc->detaildata = array();
        foreach ($this->_tovarlist as $ca) {
            $this->_doc->detaildata[] = $ca->getData();
        }


        $this->_doc->document_number = $this->docform->document_number->getText();
        $this->_doc->document_date = $this->docform->document_date->getDate();
        $isEdited = $this->_doc->document_id > 0;


        $conn = \ZDB\DB\DB::getConnect();
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
            $this->setError($ee->message);
        } catch (\Exception $ee) {
            $conn->RollbackTrans();
            throw new \Exception($ee->message);
        }
    }

    /**
     * Расчет  итого
     *
     */
    private function calcTotal()
    {
        
    }

    /**
     * Валидация   формы
     *
     */
    private function checkForm()
    {

        if (count($this->_tovarlist) == 0) {
            $this->setError("Не введена ни  одна позиция");
        }

        return !$this->isError();
    }

    public function beforeRender()
    {
        parent::beforeRender();

        $this->calcTotal();
    }

    public function backtolistOnClick($sender)
    {
        App::RedirectBack();
    }

    public function OnAutoItem($sender)
    {
        $text = $sender->getValue();
        $list_ = CapitalAsset::find("item_type= " . Item::ITEM_TYPE_OS . " and ( itemname  like '%{$text}%' or detail  like '%<inventory>{$text}</inventory>%' ) ", "itemname");
        $list = array();
        foreach ($list_ as $id => $os) {
            if ($os->typeos == 11)
                continue; //идет как  малоценка
            $list[$id] = strlen($os->inventory) > 0 ? $os->inventory . ', ' . $os->itemname : $os->itemname;
        }
        return $list;
    }

    public function OnChangeItem($sender)
    {
        $text = $sender->getText();

        //выбираем  инвентарные номера
        $list = CapitalAsset::findArray("itemname", "item_type= " . Item::ITEM_TYPE_OS . " and itemname  = '{$text}'", "itemname");

        $inv = array();
        foreach ($list as $ca) {

            if (strlen($ca->inventory) > 0) {
                $inv[$ca->inventory] = $ca->inventory;
            }
        }

        $this->editdetail->editinventory->setOptionList($inv);

        $this->updateAjax(array('editinventory'));
    }

}
