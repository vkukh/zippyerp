<?php

namespace ZippyERP\ERP\Pages\Doc;

use Zippy\Html\Form\Button;
use Zippy\Html\Form\Date;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use ZippyERP\ERP\Entity\Doc\Document;
use ZippyERP\ERP\Entity\Store;
use Zippy\WebApplication as App;

/**
 * Страница  документа списание  торговой наценки
 *
 */
class TradeMargin extends \ZippyERP\System\Pages\Base
{

    public function __construct($docid = 0)
    {
        parent::__construct();
        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new Date('document_date'))->setDate(time());
        $this->docform->add(new SubmitButton('execdoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new Button('backtolist'))->onClick($this, 'backtolistOnClick');

        $this->docform->add(new DropDownChoice('store', Store::findArray("storename", "store_type = " . Store::STORE_TYPE_RET_SUM)));

        if ($docid > 0) {    //загружаем   содержимок  документа настраницу
            $this->_doc = Document::load($docid);
            if ($this->_doc == null)
                App::RedirectError('Докумен не найден');

            $this->docform->document_number->setText($this->_doc->document_number);
            $this->docform->document_date->setDate($this->_doc->document_date);
            $this->docform->store->setValue($this->_doc->headerdata['store_id']);
        } else {
            $this->_doc = Document::create('TradeMargin');
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
        $this->_doc->document_number = $this->docform->document_number->getText();
        $this->_doc->document_date = $this->docform->document_date->getDate();
        $this->_doc->headerdata['store_id'] = $store->store_id;
        $this->_doc->headerdata['storename'] = $store->storename;
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

}
