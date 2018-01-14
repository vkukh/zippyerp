<?php

namespace ZippyERP\ERP\Pages\Reference;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Panel;
use ZippyERP\ERP\Entity\Bank;

class BankList extends \ZippyERP\ERP\Pages\Base
{

    private $_bank;

    public function __construct()
    {
        parent::__construct();

        $this->add(new Panel('banktable'))->setVisible(true);
        $this->banktable->add(new DataView('banklist', new \ZCL\DB\EntityDataSource('\ZippyERP\ERP\Entity\Bank'), $this, 'banklistOnRow'))->Reload();
        $this->banktable->add(new ClickLink('addnew'))->onClick($this, 'addOnClick');
        $this->add(new Form('bankdetail'))->setVisible(false);
        $this->bankdetail->add(new TextInput('editbankname'));
        $this->bankdetail->add(new TextInput('editmfo'));
        $this->bankdetail->add(new SubmitButton('save'))->onClick($this, 'saveOnClick');
        $this->bankdetail->add(new Button('cancel'))->onClick($this, 'cancelOnClick');
    }

    public function banklistOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('bankname', $item->bank_name));
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function editOnClick($sender)
    {
        $this->_bank = $sender->owner->getDataItem();
        $this->banktable->setVisible(false);
        $this->bankdetail->setVisible(true);
        $this->bankdetail->editbankname->setText($this->_bank->bank_name);
        $this->bankdetail->editmfo->setText($this->_bank->mfo);
    }

    public function deleteOnClick($sender)
    {
        Bank::delete($sender->owner->getDataItem()->bank_id);
        $this->banktable->banklist->Reload();
    }

    public function addOnClick($sender)
    {
        $this->banktable->setVisible(false);
        $this->bankdetail->setVisible(true);
        $this->bankdetail->editbankname->setText('');
        $this->bankdetail->editmfo->setText('');
        $this->_bank = new Bank();
    }

    public function saveOnClick($sender)
    {

        $this->_bank->bank_name = $this->bankdetail->editbankname->getText();
        $this->_bank->mfo = $this->bankdetail->editmfo->getText();
        if ($this->_bank->bank_name == '') {
            $this->setError("Введіть найменування");
            return;
        }

        $this->_bank->Save();
        $this->bankdetail->setVisible(false);
        $this->banktable->setVisible(true);
        $this->banktable->banklist->Reload();
    }

    public function cancelOnClick($sender)
    {
        $this->banktable->setVisible(true);
        $this->bankdetail->setVisible(false);
    }

}
