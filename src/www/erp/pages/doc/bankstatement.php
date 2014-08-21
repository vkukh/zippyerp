<?php

namespace ZippyERP\ERP\Pages\Doc;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Form\AutocompleteTextInput;
use Zippy\Html\Form\CheckBox;
use Zippy\Html\Form\Date;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Link\SubmitLink;
use Zippy\Html\Panel;
use ZippyERP\System\Application as App;
use ZippyERP\ERP\Entity\Doc\Document;
use ZippyERP\ERP\Entity\Account;
use ZippyERP\ERP\Entity\Entry;

/**
 * Банковская   выписка 
 */
class BankStatement extends \ZippyERP\ERP\Pages\Base
{

    public $_list = array();
    private $_doc;

    public function __construct($docid = 0, $basedon = 0)
    {
        parent::__construct();
        $this->add(new Form('docform'));
        $this->docform->add(new Date('created'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new TextInput('basedoc'));
        $this->docform->add(new DropDownChoice('bankaccount', \ZippyERP\ERP\Entity\MoneyFund::findArray('title', "bank > 0")));

        $this->docform->add(new SubmitLink('addrow'))->setClickHandler($this, 'addrowOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->setClickHandler($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->setClickHandler($this, 'savedocOnClick');
        $this->docform->add(new Button('backtolist'))->setClickHandler($this, 'backtolistOnClick');

        $this->add(new Form('editdetail'))->setVisible(false);
        $this->editdetail->add(new DropDownChoice('edittype'));
        $this->editdetail->add(new DropDownChoice('editaccount', Account::findArray("acc_name", "acc_id not in (select acc_pid  from erp_account_plan)")));
        $this->editdetail->add(new TextInput('editamount'))->setText("1");
        $this->editdetail->add(new TextInput('editcomment'));
        $this->editdetail->setSubmitHandler($this, 'saverowOnClick');
        $this->editdetail->add(new Button('cancelrow'))->setClickHandler($this, 'cancelrowOnClick');

        if ($docid > 0) {    //загружаем   содержимок  документа на страницу
            $this->_doc = Document::load($docid);
        } else {
            $this->_doc = Document::create('BankStatement');
        }
        if ($docid == 0 && $basedon > 0) {
            $this->_doc->loadBasedOn($basedon);
        }
        $this->docform->add(new DataView('detail', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, '_list')), $this, 'detailOnRow'));
        $this->fillPage();
        $this->docform->detail->Reload() ;
        
    }

    private function fillPage()
    {
        $this->docform->document_number->setText($this->_doc->document_number);
        $this->docform->bankaccount->setValue($this->_doc->headerdata['bankaccount']);
        $this->docform->created->setText(date('Y-m-d', $this->_doc->document_date));

        /*
        if ($this->_doc->headerdata['basedoc'] > 0) {  // документ-основание
            $bdoc = Document::load($this->_doc->headerdata['basedoc']);
            if ($bdoc != null) {
                $this->docform->basedoc->setValue($bdoc->document_number);
                $this->docform->basedoc->setKey($bdoc->document_id);
            }
        }
        
        */
        foreach ($this->_doc->detaildata as $item) {
            $entry = new Entry($item);
            $this->_list[$entry->entry_id] = $entry;
        }
        $this->docform->created->setText(date('Y-m-d', $this->_doc->document_date));
        $this->docform->detail->Reload();
    }

    public function detailOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('type', $item->type == 1 ? "Приход" : "Расход"));
        $row->add(new Label('account', $item->acc_code));
        $row->add(new Label('amount', number_format($item->amount / 100, 2)));
        $row->add(new Label('comment', $item->comment));
        $row->add(new ClickLink('delete'))->setClickHandler($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender)
    {
        $entry = $sender->owner->getDataItem();
        // unset($this->_entrylist[$tovar->tovar_id]);

        $this->_list = array_diff_key($this->_list, array($entry->entry_id => $this->_list[$entry->entry_id]));
        $this->docform->detail->Reload();
    }

    public function addrowOnClick($sender)
    {
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);
    }

    public function saverowOnClick($sender)
    {
        $acc = $this->editdetail->editaccount->getValue();
        if ($acc == 0) {
            $this->setError("Не выбран счет");
            return;
        }


        $entry = new Entry();   //используем   класс  проводки  для   строки
        $entry->acc = $acc;
        $acc = Account::load($acc);
        $entry->acc_code = $acc->acc_code;  // код  счета  по  дебету
        $entry->type = $this->editdetail->edittype->getValue();
        ;
        $entry->amount = $this->editdetail->editamount->getText() * 100;
        $entry->comment = $this->editdetail->editcomment->getText();
        $entry->entry_id = time();
        $this->_list[$entry->entry_id] = $entry;
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
        $this->docform->detail->Reload();

        //очищаем  форму
        $this->editdetail->editaccount->setValue(0);
        //  $this->editdetail->editct->setValue(0);
        $this->editdetail->editamount->setText("0");
        $this->editdetail->editcomment->setText("");
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



        //   $this->_doc->headerdata = array(
        //       'description' => $this->docform->description->getValue()
        //  );
        $this->_doc->detaildata = array();
        $total = 0;
        foreach ($this->_list as $entry) {
            $this->_doc->detaildata[] = $entry->getData();
            $total += $entry->amount;
        }

        $this->_doc->amount = $total;
        $this->_doc->document_date = strtotime($this->docform->created->getText());
        $this->_doc->document_number = $this->docform->document_number->getText();
        $this->_doc->headerdata['bankaccount'] = $this->docform->bankaccount->getValue();
        $this->_doc->headerdata['basedoc'] = $this->docform->basedoc->getValue();
        $isEdited = $this->_doc->document_id > 0;

        $this->_doc->save();


       // if ($this->_doc->headerdata['basedoc'] > 0) {
       //     $this->_doc->AddConnectedDoc($this->_doc->headerdata['basedoc']);
      //  }

        if($sender->id == 'execdoc'){
            $this->_doc->updateStatus(Document::STATE_EXECUTED);
        }else {
            $this->_doc->updateStatus( $isEdited ? Document::STATE_EDITED : Document::STATE_NEW);   
        }       
        
        App::Redirect('\ZippyERP\ERP\Pages\Register\DocList', $this->_doc->document_id);
    }

    /**
     * Валидация   формы
     * 
     */
    private function checkForm()
    {

        if (count($this->_list) == 0) {
            $this->setError("Не введена ни одна строка");
            return false;
        }
      /*  if (strlen($this->docform->basedoc->getValue()) > 0) {
            if ($this->docform->basedoc->getKey() > 0) {
                
            } else {
                $this->setError("Неверно введен документ-основание");
                return false;
            }
        } */
        return true;
    }

    public function beforeRender()
    {
        parent::beforeRender();
    }

    public function backtolistOnClick($sender)
    {
        App::Redirect("\\ZippyERP\\ERP\\Pages\\Register\\DocList");
    }

}
