<?php

namespace ZippyERP\ERP\Pages\Doc;

use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\Date;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Form\TextArea;
use \Zippy\Html\Form\SubmitButton;
use \Zippy\Html\Form\Button;
use ZippyERP\ERP\Entity\Doc\Document;
use ZippyERP\ERP\Entity\Customer;
use ZippyERP\System\Application as App;

/**
 * Страница документа Договор
 */
class Contract extends \ZippyERP\ERP\Pages\Base
{

    private $_doc;

    public function __construct($docid = 0)
    {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new TextInput('amount'));
        $this->docform->add(new Date('created', time()));
        $this->docform->add(new DropDownChoice('customer', Customer::findArray("customer_name", "")));
        $this->docform->add(new Date('startdate', time()));
        $this->docform->add(new Date('enddate', time()));
        $this->docform->add(new TextArea('description'));
        $this->docform->add(new SubmitButton('submit'))->setClickHandler($this, 'submitOnClick');
        $this->docform->add(new Button('cancel'))->setClickHandler($this, 'cancelOnClick');
        if ($docid > 0) {    //загружаем   содержимок  документа настраницу
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);
            $this->docform->created->setDate($this->_doc->document_date);
            $this->docform->startdate->setDate($this->_doc->headerdata['startdate']);
            $this->docform->enddate->setDate($this->_doc->headerdata['enddate']);
            $this->docform->description->setText($this->_doc->headerdata['description']);
            $this->docform->amount->setText(number_format($this->_doc->headerdata['amount'], 2, '.', ' '));
            $this->docform->customer->setValue($this->_doc->headerdata['customer']);
        } else {
            $this->_doc = Document::create('Contract');
            $this->docform->document_number->setText($this->_doc->nextNumber());
        }
    }

    public function submitOnClick($sender)
    {

        $this->_doc->amount = 100 * $this->docform->amount->getText();
        $this->_doc->document_number = $this->docform->document_number->getText();
        $this->_doc->document_date = $this->docform->created->getDate();

        $this->_doc->headerdata = array(
            'customer' => $this->docform->customer->getValue(),
            'description' => $this->docform->description->getText(),
            'startdate' => $this->docform->startdate->getDate(),
            'enddate' => $this->docform->enddate->getDate()
        );
        $this->_doc->detaildata = array();
        $this->_doc->intattr1 = $this->docform->customer->getValue();


        $isEdited = $this->_doc->document_id > 0;
        $this->_doc->save();
        if ($sender->id == 'execdoc') {
            $this->_doc->updateStatus(Document::STATE_EXECUTED);
        } else {
            $this->_doc->updateStatus($isEdited ? Document::STATE_EDITED : Document::STATE_NEW);
        }
        App::$app->getResponse()->toBack();
    }

    public function cancelOnClick($sender)
    {
        App::$app->getResponse()->toBack();
    }

}
