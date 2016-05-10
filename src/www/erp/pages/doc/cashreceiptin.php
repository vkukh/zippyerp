<?php

namespace ZippyERP\ERP\Pages\Doc;

use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\TextArea;
use \Zippy\Html\Form\Date;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Label;
use \Zippy\Html\Form\AutocompleteTextInput;
use \Zippy\Html\Form\Button;
use \Zippy\Html\Form\SubmitButton;
use \ZippyERP\ERP\Consts;
use \ZippyERP\System\System;
use \ZippyERP\System\Application as App;
use \ZippyERP\ERP\Entity\Doc\Document;
use \ZippyERP\ERP\Entity\Customer;
use \ZippyERP\ERP\Entity\MoneyFund;
use \ZippyERP\ERP\Entity\Employee;
use \ZippyERP\ERP\Entity\Store;
use \ZippyERP\ERP\Entity\Doc\CashReceiptIn as CRIN;

/**
 * Страница документа Приходный кассовый  ордер
 */
class CashReceiptIn extends \ZippyERP\ERP\Pages\Base
{

    private $_doc;

    public function __construct($docid = 0)
    {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new Date('document_date', time()));
        $this->docform->add(new DropDownChoice('optype', CRIN::getTypes(), 1))->setChangeHandler($this, 'optypeOnChange');
        $this->docform->add(new Label('lblopdetail'));
        $this->docform->add(new AutocompleteTextInput('opdetail'))->setAutocompleteHandler($this, 'opdetailOnAutocomplete');
        ;
        $this->docform->add(new TextInput('amount'));
        $this->docform->add(new TextInput('nds'));
        $this->docform->add(new AutocompleteTextInput('basedoc'))->setAutocompleteHandler($this, 'basedocOnAutocomplete');
        $this->docform->add(new TextArea('notes'));
        $this->docform->add(new Button('backtolist'))->setClickHandler($this, 'backtolistOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->setClickHandler($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->setClickHandler($this, 'savedocOnClick');
        $this->optypeOnChange(null);
        if ($docid > 0) {    //загружаем   содержимое  документа на страницу
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);
            $this->docform->document_date->setDate($this->_doc->document_date);
            $this->docform->amount->setText($this->_doc->amount / 100);
            $this->docform->optype->setValue($this->_doc->headerdata['optype']);
            $this->optypeOnChange(null);
            $this->docform->opdetail->setKey($this->_doc->headerdata['opdetail']);
            $this->docform->opdetail->setText($this->_doc->headerdata['opdetailname']);
            $this->docform->nds->setText($this->_doc->headerdata['nds'] / 100);
            $this->docform->notes->setText($this->_doc->headerdata['notes']);
            $basedocid = $this->_doc->headerdata['basedoc'];
            if ($basedocid > 0) {
                $base = Document::load($basedocid);
                $this->docform->basedoc->setKey($basedocid);
                $this->docform->basedoc->setValue($base->document_number);
            }
        } else {
            $this->_doc = Document::create('CashReceiptIn');
        }
    }

    public function optypeOnChange($sender)
    {
        $optype = $this->docform->optype->getValue();
        if ($optype == Consts::TYPEOP_CUSTOMER_IN) {
            $this->docform->lblopdetail->setText('Покупатель');
        }
        if ($optype == Consts::TYPEOP_CUSTOMER_IN_BACK) {
            $this->docform->lblopdetail->setText('Поставщик');
        }

        if ($optype == Consts::TYPEOP_CASH_IN) {
            $this->docform->lblopdetail->setText('Сотрудник');
        }
        if ($optype == Consts::TYPEOP_RET_IN) {
            $this->docform->lblopdetail->setText('Магазины');
        }
        $this->docform->nds->setVisible($optype == Consts::TYPEOP_CUSTOMER_IN);
        $this->docform->opdetail->setKey(0);
        $this->docform->opdetail->setText('');
        if ($optype == Consts::TYPEOP_BANK_IN) {
            $this->docform->lblopdetail->setText('Р/счет');
            $acc = MoneyFund::getFirst('ftype=' . MoneyFund::MF_BANK);
            $this->docform->opdetail->setKey($acc->id);
            $this->docform->opdetail->setText($acc->title);
        }
    }

    public function opdetailOnAutocomplete($sender)
    {
        $text = $sender->getValue();
        $optype = $this->docform->optype->getValue();
        if ($optype == Consts::TYPEOP_CUSTOMER_IN) {
            return Customer::findArray('customer_name', "customer_name like '%{$text}%' and ( cust_type=" . Customer::TYPE_BUYER . " or cust_type= " . Customer::TYPE_BUYER_SELLER . " )");
        }
        if ($optype == Consts::TYPEOP_CUSTOMER_IN_BACK) {
            return Customer::findArray('customer_name', "customer_name like '%{$text}%' and ( cust_type=" . Customer::TYPE_SELLER . " or cust_type= " . Customer::TYPE_BUYER_SELLER . " )");
        }
        if ($optype == Consts::TYPEOP_BANK_IN) {
            return MoneyFund::findArray('title', "title like '%{$text}%' ");
        }
        if ($optype == Consts::TYPEOP_CASH_IN) {
            return Employee::findArray('fullname', "fullname like '%{$text}%' ");
        }
        if ($optype == Consts::TYPEOP_RET_IN) {
            return Store::findArray('storename', "storename like '%{$text}%' and (store_type = " . Store::STORE_TYPE_RET . ' or store_type=' . Store::STORE_TYPE_RET_SUM . ") ");
        }
    }

    public function basedocOnAutocomplete($sender)
    {
        $text = $sender->getValue();
        $answer = array();
        $conn = \ZDB\DB\DB::getConnect();
        $sql = "select document_id,document_number from erp_document where document_number  like '%{$text}%' and document_id <> {$this->_doc->document_id} order  by document_id desc  limit 0,20";
        $rs = $conn->Execute($sql);
        foreach ($rs as $row) {
            $answer[$row['document_id']] = $row['document_number'];
        }
        return $answer;
    }

    public function backtolistOnClick($sender)
    {
        App::RedirectBack();
    }

    public function savedocOnClick($sender)
    {



        $basedocid = $this->docform->basedoc->getKey();
        $this->_doc->headerdata = array(
            'optype' => $this->docform->optype->getValue(),
            'opdetail' => $this->docform->opdetail->getKey(),
            'opdetailname' => $this->docform->opdetail->getText(),
            'amount' => $this->docform->amount->getValue() * 100,
            'nds' => $this->docform->nds->getValue() * 100,
            'basedoc' => $basedocid,
            'notes' => $this->docform->notes->getText()
        );
        $this->_doc->amount = 100 * $this->docform->amount->getText();
        $this->_doc->document_number = $this->docform->document_number->getText();
        $this->_doc->document_date = $this->docform->document_date->getDate();
        $this->_doc->datatag = $this->_doc->headerdata['opdetail'];

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

            if ($basedocid > 0) {
                $this->_doc->AddConnectedDoc($basedocid);
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

}
