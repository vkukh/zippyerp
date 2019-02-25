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
use Zippy\Html\Form\AutocompleteTextInput;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Link\SubmitLink;
use App\Entity\Doc\Document;
use App\Entity\Item;
use App\Application as App;
use App\Helper as H;

/**
 * Страница  списания  МЦ  с  эксплуатации
 */
class MZOutMaintenance extends \App\Pages\Base
{

    public $_tovarlist = array();
    private $_doc;
    private $_rowid = 0;
    private $_os = false;
    private $_mz = array();

    public function __construct($docid = 0) {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new Date('document_date'))->setDate(time());


        $this->docform->add(new SubmitLink('addrow'))->onClick($this, 'addrowOnClick');
        $this->docform->add(new SubmitLink('addrowos'))->onClick($this, 'addrowOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new Button('backtolist'))->onClick($this, 'backtolistOnClick');

        $this->add(new Form('editdetail'))->setVisible(false);
        $this->editdetail->add(new TextInput('editquantity'))->setText("1");
        $this->editdetail->add(new TextInput('editprice'))->setText("0");
        $this->editdetail->add(new DropDownChoice('edittovar'))->onChange($this, 'OnChangeItem');




        $this->editdetail->add(new Button('cancelrow'))->onClick($this, 'cancelrowOnClick');
        $this->editdetail->add(new SubmitButton('submitrow'))->onClick($this, 'saverowOnClick');

        if ($docid > 0) {    //загружаем   содержимок  документа на страницу
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);

            $this->docform->document_date->setDate($this->_doc->document_date);

            $i = 1;
            foreach ($this->_doc->detaildata as $_item) {
                $item = new \App\DataItem($_item);
                $item->id = $i++;
                $this->_tovarlist[$item->id] = $item;
            }
        } else {
            $this->_doc = Document::create('MZOutMaintenance');
            $this->docform->document_number->setText($this->_doc->nextNumber());
        }

        $this->docform->add(new DataView('detail', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, '_tovarlist')), $this, 'detailOnRow'))->Reload();
    }

    public function detailOnRow($row) {
        $item = $row->getDataItem();

        $row->add(new Label('tovar', $item->itemname));
        $row->add(new Label('quantity', H::fqty($item->qty)));
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender) {
        $tovar = $sender->owner->getDataItem();
        // unset($this->_tovarlist[$tovar->tovar_id]);

        $this->_tovarlist = array_diff_key($this->_tovarlist, array($tovar->id => $this->_tovarlist[$tovar->id]));
        $this->docform->detail->Reload();
    }

    public function addrowOnClick($sender) {
        $this->_os = $sender->id == "addrowos";

        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);
        $this->_mz = array();
        $list = array();

        $conn = \ZDB\DB::getConnect();


        if ($this->_os) {
            $sql = " SELECT  
                     ca.`ca_id`  as item_id  ,
                     ca.`ca_name` as itemname  ,
                       sum( sc.`quantity`) as qty,
                        sc.`amount`
                      FROM entrylist_view sc
                          JOIN `cassetlist` ca
                          ON sc.ca_id = ca.ca_id
                          where sc.acc_code = '112' and  sc.`quantity` > 0
                          group by ca.`ca_id`,
                     ca.`ca_name`,
                     sc.`amount`";
        } else {
            $sql = "  SELECT   
                     it.item_id,
                     it.`itemname`,
                       sum( sc.`quantity`) as qty,
                        sc.`amount`
                      FROM entrylist_view sc
                          JOIN items it
                          ON sc.extcode = it.item_id
                          where sc.acc_code = 'МЦ' and  sc.`quantity` > 0
                          group by sc.`item_id`,
                      it.`itemname`,
                      sc.`amount`";
        }

        $rs = $conn->Execute($sql);
        foreach ($rs as $v) {
            if ($v['qty'] > 0) {

                $it = new \App\DataItem();
                $it->id = time();
                $it->os = $this->_os;
                $it->item_id = $v['item_id'];
                $it->itemname = $v['itemname'];
                $it->qty = H::fqty($v['qty']);
                $it->price = H::famt($v['amount'] / $v['qty']);
                $this->_mz[$it->id] = $it;

                $list[$it->id] = $it->itemname;
            }
        }

        $this->editdetail->edittovar->setOptionList($list);
        $this->editdetail->edittovar->setValue(0);
    }

    public function saverowOnClick($sender) {
        $id = $this->editdetail->edittovar->getValue();
        if ($id == 0) {
            $this->setError("Не выбран  МЦ");
            return;
        }
        $it = $this->_mz[$id];

        $it->qty = $this->editdetail->editquantity->getText();


        // unset($this->_tovarlist[$this->_rowid]);
        $this->_tovarlist[$it->id] = $it;
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
        $this->docform->detail->Reload();

        //очищаем  форму
        $this->editdetail->edittovar->setValue(0);

        $this->editdetail->editquantity->setText("1");
        $this->editdetail->editprice->setText("0");
    }

    public function cancelrowOnClick($sender) {
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
    }

    public function savedocOnClick($sender) {
        if ($this->checkForm() == false) {
            return;
        }


        $this->_doc->headerdata = array();
        $this->_doc->detaildata = array();
        foreach ($this->_tovarlist as $tovar) {
            $this->_doc->detaildata[] = $tovar->getData();
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
            $this->setError("Помилка запису документу. Деталізація в лог файлі  ");

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
            $this->setError("Не введений ні один  товар");
        }

        return !$this->isError();
    }

    public function backtolistOnClick($sender) {
        App::RedirectBack();
    }

    public function OnChangeStore($sender) {
        //очистка  списка  товаров
        $this->_tovarlist = array();
        $this->docform->detail->Reload();
    }

    public function OnChangeItem($sender) {
        $item_id = $sender->getValue();
        $it = $this->_mz[$item_id];
        $this->editdetail->editquantity->setText($it->qty);
        $this->editdetail->editprice->setText($it->price);

        $this->updateAjax(array('editquantity', 'editprice'));
    }

}
