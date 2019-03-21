<?php

namespace App\Pages\Doc;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\Date;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Link\SubmitLink;
use App\Entity\CAsset;
use App\Entity\Doc\Document;
use App\Helper as H;
use App\Application as App;

/**
 * Страница  ввода  ОС в  эксплуатацию
 */
class NAInMaintenance extends \App\Pages\Base
{

    public $_tovarlist = array();
    private $_doc;
    private $_rowid = 0;

    public function __construct($docid = 0) {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new Date('document_date'))->setDate(time());


        $this->docform->add(new SubmitLink('addrow'))->onClick($this, 'addrowOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new Button('backtolist'))->onClick($this, 'backtolistOnClick');

        $this->add(new Form('editdetail'))->setVisible(false);
        //   $this->editdetail->add(new DropDownChoice('editinventory')) ;


        $list_ = CAsset::find(" ", "ca_name");
        $list = array();
        foreach ($list_ as $id => $os) {
            if ($os->acc_code == 112)
                continue; //идет как  малоценка
            $list[$id] = strlen($os->code) > 0 ? $os->code . ', ' . $os->ca_name : $os->ca_name;
        }
        $this->editdetail->add(new DropDownChoice('edittovar', $list));

        $this->editdetail->add(new Button('cancelrow'))->onClick($this, 'cancelrowOnClick');
        $this->editdetail->add(new SubmitButton('submitrow'))->onClick($this, 'saverowOnClick');

        if ($docid > 0) {    //загружаем   содержимок  документа на страницу
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);

            $this->docform->document_date->setDate($this->_doc->document_date);


            foreach ($this->_doc->detaildata as $item) {
                $ca = new CAsset($item);
                $this->_tovarlist[$ca->ca_id] = $ca;
            }
        } else {
            $this->_doc = Document::create('NAInMaintenance');
            $this->docform->document_number->setText($this->_doc->nextNumber());
        }

        $this->docform->add(new DataView('detail', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, '_tovarlist')), $this, 'detailOnRow'))->Reload();
    }

    public function detailOnRow($row) {
        $item = $row->getDataItem();

        $row->add(new Label('tovar', $item->ca_name));
        $row->add(new Label('inventory', $item->code));
        //       $row->add(new Label('price', H::famt($item->price)));
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender) {
        $tovar = $sender->owner->getDataItem();
        // unset($this->_tovarlist[$tovar->tovar_id]);

        $this->_tovarlist = array_diff_key($this->_tovarlist, array($tovar->ca_id => $this->_tovarlist[$tovar->ca_id]));
        $this->docform->detail->Reload();
    }

    public function addrowOnClick($sender) {
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);
    }

    public function editOnClick($sender) {
        $os = $sender->getOwner()->getDataItem();
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);

        // $this->editdetail->editinventory->setText($stock->inventory  );

        $this->editdetail->edittovar->setValue($os->ca_id);

        $this->_rowid = $os->item_id;
    }

    public function saverowOnClick($sender) {
        $id = $this->editdetail->edittovar->getValue();
        if ($id == 0) {
            $this->setError("Не вибраний ОC");
            return;
        }
        $ca = CAsset::load($id);

        // $stock->partion = $stock->price;
        //$ca->value = $this->editdetail->editprice->getText() ;
        //$ca->quantity = 1 ;


        $this->_tovarlist[$ca->ca_id] = $ca;
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
        $this->docform->detail->Reload();

        //очищаем  форму
        $this->editdetail->edittovar->setValue(0);

        //   $this->editdetail->editinventory->setOptionList(array());
    }

    public function cancelrowOnClick($sender) {
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
    }

    public function savedocOnClick($sender) {
        if ($this->checkForm() == false) {
            return;
        }



        $this->_doc->detaildata = array();
        foreach ($this->_tovarlist as $ca) {
            $this->_doc->detaildata[] = $ca->getData();
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
            $this->setError("Ошибка записи. Детально  в  логе ");

            $logger->error($ee);
            return;
        }
    }

    /**
     * Валидация   формы
     *
     */
    private function checkForm() {

        if (count($this->_tovarlist) == 0) {
            $this->setError("Не введены позиции");
        }

        return !$this->isError();
    }

    public function backtolistOnClick($sender) {
        App::RedirectBack();
    }

}
