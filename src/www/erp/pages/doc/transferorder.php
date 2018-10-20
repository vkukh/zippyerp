<?php

//todofirst

namespace ZippyERP\ERP\Pages\Doc;

use Zippy\Html\Form\AutocompleteTextInput;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\CheckBox;
use Zippy\Html\Form\Date;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextArea;
use Zippy\Html\Form\TextInput;
use ZippyERP\ERP\Entity\Customer;
use ZippyERP\ERP\Entity\Doc\Document;
use Zippy\WebApplication as App;

/**
 * Страница документа Платежное поручение
 */
class TransferOrder extends \ZippyERP\ERP\Pages\Base
{

    private $_doc;
    private $_basedocid = 0;

    public function __construct($docid = 0, $basedocid = 0) {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new Date('document_date', time()));
        $this->docform->add(new DropDownChoice('bankaccount', \ZippyERP\ERP\Entity\MoneyFund::findArray('title', "ftype=1")));
        $this->docform->add(new CheckBox('tax'));
        $this->docform->add(new DropDownChoice('customer', Customer::findArray('customer_name', "", 'customer_name')));
        ;
        $this->docform->add(new TextInput('amount'));
        $this->docform->add(new TextInput('nds'));
        $this->docform->add(new AutocompleteTextInput('basedoc'))->onText($this, 'OnAutocomplete');

        $this->docform->add(new TextArea('notes'));
        $this->docform->add(new Button('backtolist'))->onClick($this, 'backtolistOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->onClick($this, 'savedocOnClick');


        if ($docid > 0) {    //загружаем   содержимок  документа настраницу
            $this->_doc = Document::load($docid);
            if ($this->_doc == null)
                App::RedirectError('Докумен не найден');
            $this->docform->amount->setText($this->_doc->amount / 100);
            $this->docform->document_number->setText($this->_doc->document_number);
            $this->docform->document_date->setDate($this->_doc->document_date);
            $this->docform->nds->setText($this->_doc->headerdata['nds'] / 100);
            $this->docform->notes->setText($this->_doc->headerdata['notes']);

            $this->docform->bankaccount->setValue($this->_doc->headerdata['bankaccount']);
            $this->docform->tax->setValue($this->_doc->headerdata['tax']);
            $this->docform->basedoc->setKey($this->_doc->headerdata['basedoc']);
            $this->docform->basedoc->setText($this->_doc->headerdata['basedocname']);


            $this->docform->customer->setValue($this->_doc->headerdata['customer']);
        } else {
            $this->_doc = Document::create('TransferOrder');
            $this->docform->document_number->setText($this->_doc->nextNumber());
            if ($basedocid > 0) {  //создание на  основании
                $basedoc = Document::load($basedocid);
                if ($basedoc instanceof Document) {
                    $this->_basedocid = $basedocid;
                }
            }
        }
    }

    public function backtolistOnClick($sender) {
        App::RedirectBack();
    }

    public function savedocOnClick($sender) {
        if ($this->docform->customer->getValue() <= 0) {
            $this->setError('Не вибраний  контрагент');
            return;
        }
        $this->_doc->headerdata = array(
            'customer' => $this->docform->customer->getValue(),
            'customername' => $this->docform->customer->getValueName(),
            'bankaccount' => $this->docform->bankaccount->getValue(),
            'tax' => $this->docform->tax->getValue(),
            'notes' => $this->docform->notes->getText(),
            'basedoc' => $this->docform->basedoc->getKey(),
            'basedocname' => $this->docform->basedoc->getText(),
            'nds' => $this->docform->nds->getValue() * 100,
            'amount' => $this->docform->amount->getValue() * 100
        );
        $this->_doc->amount = 100 * $this->docform->amount->getText();
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
            if ($this->_basedocid > 0) {
                $this->_doc->AddConnectedDoc($this->_basedocid);
                $this->_basedocid = 0;
            }
            $conn->CommitTrans();
            App::RedirectBack();
        } catch (\Exception $ee) {
            global $logger;
            $conn->RollbackTrans();
            $this->setError("Помилка запису документу. Деталізація в лог файлі  ");
    
            $logger->error($ee);
            return;
        }
    }

    // автолоад документов-оснований
    public function OnAutocomplete($sender) {
        $text = $sender->getValue();
        $answer = array();
        $conn = \ZDB\DB::getConnect();
        $sql = "select document_id,document_number from erp_document where document_number  like '%{$text}%' and document_id <> {$this->_doc->document_id} order  by document_id desc  limit 0,20";
        $rs = $conn->Execute($sql);
        foreach ($rs as $row) {
            $answer[$row['document_id']] = $row['document_number'];
        }
        return $answer;
    }

}
