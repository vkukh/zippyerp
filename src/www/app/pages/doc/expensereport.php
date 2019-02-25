<?php

namespace App\Pages\Doc;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\CheckBox;
use Zippy\Html\Form\Date;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Form\AutocompleteTextInput;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Link\SubmitLink;
use App\Entity\Doc\Document;
use App\Entity\Employee;
use App\Entity\Item;
use App\Entity\Store;
use App\Helper as H;
use App\Application as App;

/**
 * Страница  ввода  авансового отчета
 */
class ExpenseReport extends \App\Pages\Base
{

    public $_itemlist = array();
    private $_doc;
    private $_rowid = 0;

    public function __construct($docid = 0, $basedocid = 0) {
        parent::__construct();

        $this->add(new Form('docform'));
        $this->docform->add(new TextInput('document_number'));
        $this->docform->add(new Date('document_date'))->setDate(time());
        $this->docform->add(new DropDownChoice('employee', Employee::findArray("emp_name", "detail like '%<fired>0</fired>%'  ", "emp_name")));
        $this->docform->add(new DropDownChoice('store', Store::findArray("storename", "")));
        $this->docform->add(new DropDownChoice('expensetype', array(0 => 'Не выбрано', 23 => 'Прямые производственные затраты', 91 => 'Общепроизводственые затраты', 92 => 'Административные затраты', 93 => 'Затраты на сбыт')));
        $itt = array('281' => 'Товары', '201' => 'Сырье и материалы', '22' => 'МБП', '203' => 'Топливо', '204' => 'Тара', '207' => 'Запчасти', '15' => 'ОС и НМА');
        $this->docform->add(new DropDownChoice('storetype', $itt));


        $this->docform->add(new TextInput('comment'));
        $this->docform->add(new TextInput('expenseamount'));
        $this->docform->add(new CheckBox('isnds', H::usends()))->onChange($this, 'onIsnds');
        $this->docform->isnds->setVisible(H::usends());



        $this->docform->add(new SubmitLink('addrow'))->onClick($this, 'addrowOnClick');
        $this->docform->add(new Button('backtolist'))->onClick($this, 'backtolistOnClick');
        $this->docform->add(new SubmitButton('savedoc'))->onClick($this, 'savedocOnClick');
        $this->docform->add(new SubmitButton('execdoc'))->onClick($this, 'savedocOnClick');

        $this->docform->add(new Label('totalnds'));
        $this->docform->add(new Label('total'));
        $this->add(new Form('editdetail'))->setVisible(false);


        $this->editdetail->add(new AutocompleteTextInput('edititem'))->onText($this, 'OnAutoItem');

        $this->editdetail->add(new TextInput('editquantity'))->setText("1");
        $this->editdetail->add(new TextInput('editprice'));
        $this->editdetail->add(new TextInput('editpricends'));

        $this->editdetail->add(new Button('cancelrow'))->onClick($this, 'cancelrowOnClick');
        $this->editdetail->add(new SubmitButton('saverow'))->onClick($this, 'saverowOnClick');

        if ($docid > 0) {    //загружаем   содержимок  документа настраницу
            $this->_doc = Document::load($docid);
            $this->docform->document_number->setText($this->_doc->document_number);

            $this->docform->expenseamount->setText(H::famt($this->_doc->headerdata['expenseamount']));
            $this->docform->comment->setText($this->_doc->headerdata['comment']);
            $this->docform->isnds->setChecked($this->_doc->headerdata['isnds']);


            $this->docform->document_date->setDate($this->_doc->document_date);
            $this->docform->employee->setValue($this->_doc->headerdata['employee']);
            $this->docform->store->setValue($this->_doc->headerdata['store']);
            $this->docform->expensetype->setValue($this->_doc->headerdata['expensetype']);
            $this->docform->storetype->setValue($this->_doc->headerdata['storetype']);

            foreach ($this->_doc->detaildata as $item) {
                $item = new Item($item);
                $this->_itemlist[$item->item_id] = $item;
            }
            /*
              $val = $this->_doc->headerdata['expensetype'];
              if ($val == 201 || $val == 22 || $val == 281) {
              $this->docform->expenseamount->setVisible(false);
              $this->docform->store->setVisible(true);
              $this->docform->addrow->setVisible(true);
              } else {
              $this->docform->expenseamount->setVisible(true);
              $this->docform->store->setVisible(false);
              $this->docform->addrow->setVisible(false);
              } */
        } else {
            $this->_doc = Document::create('ExpenseReport');
            /* if ($basedocid > 0) {  //создание на  основании
              $basedoc = Document::load($basedocid);
              if ($basedoc instanceof Document) {
              $this->_basedocid = $basedocid;


              /*
              if ($basedoc->meta_name == 'PurchaseInvoice') {

              $this->docform->employee->setValue($basedoc->headerdata['employee']);

              foreach ($basedoc->detaildata as $_item) {
              $item = new Item($_item);
              $this->_itemlist[$item->item_id] = $item;
              }
              }
              }
              } */
        }
        $this->calcTotal();
        $this->docform->add(new DataView('detail', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, '_itemlist')), $this, 'detailOnRow'))->Reload();
    }

    public function detailOnRow($row) {
        $item = $row->getDataItem();

        $row->add(new Label('item', $item->itemname));
        $row->add(new Label('measure', $item->measure_name));
        $row->add(new Label('quantity', H::fqty($item->quantity)));
        $row->add(new Label('price', H::famt($item->price)));
        $row->add(new Label('pricends', H::famt($item->pricends)));
        $row->add(new Label('amount', H::famt($item->amount)));
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');

        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function editOnClick($sender) {
        $item = $sender->getOwner()->getDataItem();
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);

        $this->editdetail->editquantity->setText(H::fqty($item->quantity));
        $this->editdetail->editprice->setText(H::famt($item->price));
        $this->editdetail->editpricends->setText(H::famt($item->pricends));
        $this->editdetail->edititem->setKey($item->item_id);
        $this->editdetail->edititem->setText($item->itemname);
        $this->_rowid = $item->item_id;
    }

    public function deleteOnClick($sender) {
        $item = $sender->owner->getDataItem();
        // unset($this->_itemlist[$item->item_id]);

        $this->_itemlist = array_diff_key($this->_itemlist, array($item->item_id => $this->_itemlist[$item->item_id]));
        $this->calcTotal();
        $this->docform->detail->Reload();
    }

    public function addrowOnClick($sender) {
        $this->editdetail->setVisible(true);
        $this->docform->setVisible(false);
        $this->_rowid = 0;
    }

    public function saverowOnClick($sender) {


        $id = $this->editdetail->edititem->getKey();
        if ($id == 0) {
            $this->setError("Не вибраний ТМЦ");
            return;
        }
        $item = Item::load($id);
        $item->quantity = $this->editdetail->editquantity->getText();
        $item->price = $this->editdetail->editprice->getText();
        $item->pricends = $this->editdetail->editpricends->getText();


        unset($this->_itemlist[$this->_rowid]);
        $this->_itemlist[$item->item_id] = $item;
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
        $this->calcTotal();
        $this->docform->detail->Reload();

        //очищаем  форму
        $this->editdetail->edititem->setKey(0);
        $this->editdetail->edititem->setText('');


        $this->editdetail->editprice->setText("");
        $this->editdetail->editpricends->setText("");
    }

    public function cancelrowOnClick($sender) {
        $this->editdetail->setVisible(false);
        $this->docform->setVisible(true);
    }

    public function savedocOnClick($sender) {
        if ($this->checkForm() == false) {
            return;
        }

        $this->calcTotal();
        $expenseamount = $this->docform->expenseamount->getText();
        $this->_doc->headerdata = array(
            'employee' => $this->docform->employee->getValue(),
            'store' => $this->docform->store->getValue(),
            'isnds' => $this->docform->isnds->isChecked() ? 1 : 0,
            'expenseamount' => $this->docform->expenseamount->getText(),
            'comment' => $this->docform->comment->getText(),
            'expensetype' => $this->docform->expensetype->getValue(),
            'storetype' => $this->docform->storetype->getValue(),
            'totalnds' => $this->docform->totalnds->getText(),
            'total' => $this->docform->total->getText()
        );
        $this->_doc->detaildata = array();
        foreach ($this->_itemlist as $item) {
            $this->_doc->detaildata[] = $item->getData();
        }

        $this->_doc->amount = $this->docform->total->getText();
        if ($expenseamount > 0)
            $this->_doc->amount += $expenseamount;
        $this->_doc->document_number = $this->docform->document_number->getText();
        $this->_doc->document_date = $this->docform->document_date->getDate();
        $isEdited = $this->_doc->document_id > 0;
        $this->_doc->datatag = $this->docform->employee->getValue();
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
     * Расчет  итого
     *
     */
    private function calcTotal() {

        $total = 0;
        $totalnds = 0;
        foreach ($this->_itemlist as $item) {
            $item->amount = $item->price * ($item->quantity );
            if ($this->docform->isnds->isChecked()) {
                $item->amount = $item->pricends * $item->quantity;
                $totalnds = $totalnds + ($item->pricends - $item->price) * $item->quantity;
            }
            $total = $total + $item->amount;
        }
        $this->docform->total->setText(H::famt($total));
        $this->docform->totalnds->setText(H::famt($totalnds));
    }

    /**
     * Валидация   формы
     *
     */
    private function checkForm() {
        if (strlen($this->_doc->document_number) == 0) {
            $this->setError('Введите номер документа');
        }


        if ($this->docform->employee->getValue() == 0) {
            $this->setError("Не вибраний  співробітник");
        }

        if ($this->docform->expenseamount->getText() > 0 && $this->docform->expensetype->getValue() == 0) {
            $this->setError("Не введений тип витрат");
        }
        if (count($this->_itemlist) > 0 && $this->docform->storetype->getValue() == 0) {
            $this->setError("Не введений тип оприходування");
        }
        return !$this->isError();
    }

    public function backtolistOnClick($sender) {
        App::RedirectBack();
    }

    // событие  после  создания  нового элемента справочника номенклатуры
    public function OnItem($cancel = false) {
        $this->editdetail->setVisible(true);
        if ($cancel == true)
            return;

        $item = $this->itemdetail->getData();

        $this->editdetail->edititem->setKey($item->item_id);
        $this->editdetail->edititem->setText($item->itemname);
    }

    public function OnAutoItem($sender) {

        $text = Item::qstr('%' . $sender->getText() . '%');
        return Item::findArray('itemname', "disabled <> 1 and (itemname like {$text} or item_code like {$text})");
    }

    public function onIsnds($sender) {
        $this->calcTotal();
        $this->_tvars["usends"] = $this->docform->isnds->isChecked();
        $this->docform->detail->Reload();
    }

}
