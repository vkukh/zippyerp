<?php

namespace ZippyERP\ERP\Pages\Doc;

use Zippy\Html\Form\Button;
use Zippy\Html\Form\Date;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Label;
use ZippyERP\ERP\Entity\Doc\Document;
use ZippyERP\ERP\Entity\Stock;
use ZippyERP\ERP\Entity\Store;
use ZippyERP\ERP\Helper as H;
use Zippy\WebApplication as App;

/**
 * Страница  документа переоценка  в  суммовом  учете
 *
 */
class RevaluationRetSum extends \ZippyERP\ERP\Pages\Base
{

    public function __construct($docid = 0)
    {
        parent::__construct();
        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new Date('document_date'))->setDate(time());
        $this->docform->add(new SubmitButton('execdoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new Button('backtolist'))->onClick($this, 'backtolistOnClick');

        $this->docform->add(new DropDownChoice('store', Store::findArray("storename", "store_type = " . Store::STORE_TYPE_RET_SUM)))->onChange($this, 'ajaxUpdateActual', true);
        ;
        $this->docform->add(new DropDownChoice('type'));
        $this->docform->add(new TextInput('summa'));
        $this->docform->add(new Label('actual'));

        if ($docid > 0) {    //загружаем   содержимок  документа настраницу
            $this->_doc = Document::load($docid);
            if ($this->_doc == null)
                App::RedirectError('Докумен не найден');

            $this->docform->document_number->setText($this->_doc->document_number);
            $this->docform->document_date->setDate($this->_doc->document_date);
            $this->docform->store->setValue($this->_doc->headerdata['store_id']);
            $this->docform->type->setValue($this->_doc->headerdata['type']);
            $this->docform->summa->setText(H::fm($this->_doc->headerdata['summa']));
            $this->updateActual();
        } else {
            $this->_doc = Document::create('RevaluationRetSum');
            $this->docform->document_number->setText($this->_doc->nextNumber());
        }
    }

    public function savedocOnClick($sender)
    {
        $store = Store::load($this->docform->store->getValue());

        if ($store == false) {
            $this->setError("Не  выбран  магазин");
            return;
        }
        if ($this->docform->summa->getText() == 0) {
            $this->setError("Не введена сумма");
            return;
        }
        if ($this->docform->summa->getText() == $this->docform->actual->getText()) {
            $this->setError("Не изменена сумма");
            return;
        }
        $this->_doc->document_number = $this->docform->document_number->getText();
        $this->_doc->document_date = strtotime($this->docform->document_date->getText());
        $this->_doc->headerdata['store_id'] = $store->store_id;
        $this->_doc->headerdata['type'] = $this->docform->type->getValue();
        $this->_doc->headerdata['storename'] = $store->storename;
        $this->_doc->headerdata['summa'] = 100 * $this->docform->summa->getText();
        $this->_doc->headerdata['actual'] = 100 * $this->docform->actual->getText();
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

    public function backtolistOnClick($sender)
    {
        App::RedirectBack();
    }

    // обновляем  сумму  на  магазине
    public function updateActual()
    {
        $store_id = $this->docform->store->getValue();
        if ($store_id > 0) {
            $list = Stock::find("store_id = " . $store_id);  //ишем  сток  со  спецтоваром
            foreach ($list as $stock) {
                $amount = Stock::getQuantity($stock->stock_id, $this->docform->document_date->getDate());
                $this->docform->actual->setText(H::fm($amount));
                $this->_doc->headerdata['stock_id'] = $stock->stock_id;
                return;
            }
        }
    }

    public function ajaxUpdateActual()
    {
        $this->updateActual();
        $this->updateAjax(array('actual'));
    }

}
