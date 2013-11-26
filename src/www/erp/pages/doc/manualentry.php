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
use ZippyERP\ERP\Entity\Doc\Document;
use ZippyERP\ERP\Entity\Entry;
use ZippyERP\ERP\Entity\Account;

/**
 * Ручная хоз. операция  
 */
class ManualEntry extends \ZippyERP\ERP\Pages\Base
{

        public $_entrylist = array();
        private $_doc;

        public function __construct($docid = 0)
        {
                parent::__construct();
                $this->add(new Form('docform'));
                $this->docform->add(new Date('created', time()));
                $this->docform->add(new TextInput('description'));
                $this->docform->add(new SubmitLink('addrow'))->setClickHandler($this, 'addrowOnClick');
                $this->docform->add(new SubmitButton('savedoc'))->setClickHandler($this, 'savedocOnClick');
                $this->docform->add(new Button('backtolist'))->setClickHandler($this, 'backtolistOnClick');
                $this->docform->add(new Label('total'));
                $this->add(new Form('editdetail'))->setVisible(false);
                $this->editdetail->add(new DropDownChoice('editdt', \ZippyERP\ERP\Entity\Account::findArray("acc_name", "acc_id not in (select acc_pid  from erp_account_plan)")));
                $this->editdetail->add(new DropDownChoice('editct', \ZippyERP\ERP\Entity\Account::findArray("acc_name", "acc_id not in (select acc_pid  from erp_account_plan)")));
                $this->editdetail->add(new TextInput('editamount'))->setText("1");
                $this->editdetail->add(new TextInput('editcomment'));
                $this->editdetail->setSubmitHandler($this, 'saverowOnClick');
                $this->editdetail->add(new Button('cancelrow'))->setClickHandler($this, 'cancelrowOnClick');

                if ($docid > 0) {    //загружаем   содержимок  документа настраницу
                        $this->_doc = Document::load($docid);
                        // $this->docform->document_number->setText($this->_doc->document_number);
                        $this->docform->description->setText($this->_doc->headerdata['description']);

                        $this->docform->created->setText(date('Y-m-d', $this->_doc->document_date));

                        foreach ($this->_doc->detaildata as $item) {
                                $entry = new Entry($item);
                                $this->_entrylist[$entry->entry_id] = $entry;
                        }
                } else {
                        $this->_doc = Document::create('ManualEntry');
                }

                $this->docform->add(new DataView('detail', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, '_entrylist')), $this, 'detailOnRow'))->Reload();
        }

        public function detailOnRow($row)
        {
                $item = $row->getDataItem();

                $row->add(new Label('dt', $item->acc_d_code));
                $row->add(new Label('ct', $item->acc_c_code));
                $row->add(new Label('amount', number_format($item->amount / 100, 2)));
                $row->add(new Label('comment', $item->comment));
                $row->add(new ClickLink('delete'))->setClickHandler($this, 'deleteOnClick');
        }

        public function deleteOnClick($sender)
        {
                $entry = $sender->owner->getDataItem();
                // unset($this->_entrylist[$tovar->tovar_id]);

                $this->_entrylist = array_diff_key($this->_entrylist, array($entry->entry_id => $this->_entrylist[$entry->entry_id]));
                $this->docform->detail->Reload();
        }

        public function addrowOnClick($sender)
        {
                $this->editdetail->setVisible(true);
                $this->docform->setVisible(false);
        }

        public function saverowOnClick($sender)
        {
                $dt = $this->editdetail->editdt->getValue();
                if ($dt == 0) {
                        $this->setError("Не выбран счет дебета");
                        return;
                }
                $ct = $this->editdetail->editct->getValue();
                if ($ct == 0) {
                        $this->setError("Не выбран счет кредита");
                        return;
                }

                $entry = new Entry();
                $entry->acc_c = $ct;
                $acc = Account::load($ct);
                $entry->acc_c_code = $acc->acc_code;  // код  счета  по  дебету
                $entry->acc_d = $dt;
                $acc = Account::load($dt);
                $entry->acc_d_code = $acc->acc_code; // код  счета  по  кредиту

                $entry->amount = $this->editdetail->editamount->getText() * 100;
                $entry->comment = $this->editdetail->editcomment->getText();
                $entry->entry_id = time();
                $this->_entrylist[$entry->entry_id] = $entry;
                $this->editdetail->setVisible(false);
                $this->docform->setVisible(true);
                $this->docform->detail->Reload();

                //очищаем  форму
                $this->editdetail->editdt->setValue(0);
                $this->editdetail->editct->setValue(0);
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

                $this->calcTotal();

                $this->_doc->headerdata = array(
                    'description' => $this->docform->description->getValue()
                );
                $this->_doc->detaildata = array();
                foreach ($this->_entrylist as $entry) {
                        $this->_doc->detaildata[] = $entry->getData();
                }

                $this->_doc->amount = 100 * $this->docform->total->getText();
                $this->_doc->document_date = strtotime($this->docform->created->getText());

                $this->_doc->save();
                $this->_doc->document_number = $this->_doc->document_id;
                $this->_doc->save();
                App::Redirect('\ZippyERP\ERP\Pages\Register\DocList');
        }

        /**
         * Расчет  итого
         * 
         */
        private function calcTotal()
        {
                $total = 0;
                foreach ($this->_entrylist as $entry) {
                        $total = $total + $entry->amount / 100;
                }
                $this->docform->total->setText(number_format($total, 2));
        }

        /**
         * Валидация   формы
         * 
         */
        private function checkForm()
        {

                if (count($this->_entrylist) == 0) {
                        $this->setError("Не введена ни одна проводка");
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
                App::Redirect("\\ZippyERP\\ERP\\Pages\\Register\\DocList");
        }

}
