<?php

namespace App\Pages\Register;

use Zippy\Html\DataList\ArrayDataSource;
use Zippy\Html\DataList\DataView;
use Zippy\Html\Panel;
use Zippy\Html\Label;
use App\Entity\Account;
use App\Entity\AccountEntry;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Button;
use Zippy\Html\Link\ClickLink;
use App\Helper as H;

/**
 * Класс страницы плана счетов
 */
class AccountList extends \App\Pages\Base
{

    public $_list = array();

    public function __construct() {
        parent::__construct();

        $this->add(new Panel('acctable'));
        $this->acctable->add(new DataView('list', new ArrayDataSource($this, '_list'), $this, 'listOnRow'));

        $this->acctable->add(new ClickLink('addnew'))->onClick($this, 'addOnClick');
        $this->add(new Form('accdetail'))->setVisible(false);
        $this->accdetail->add(new TextInput('editacc_code'));
        $this->accdetail->add(new TextInput('editacc_name'));
        $this->accdetail->add(new DropDownChoice('editacc_p'));
        $this->accdetail->add(new SubmitButton('save'))->onClick($this, 'saveOnClick');
        $this->accdetail->add(new Button('cancel'))->onClick($this, 'cancelOnClick');

        $this->updatelists();
    }

    public function listOnRow($row) {
        $item = $row->getDataItem();
        $row->add(new Label('code', $item->acc_code));
        $row->add(new Label('name', $item->acc_name));
        //   $saldo = $item->getSaldo();
        //  $row->add(new Label('dt', $saldo > 0 ? H::famt($saldo) : ""));
        //   $row->add(new Label('ct', $saldo < 0 ? H::famt(0 - $saldo) : ""));
        $row->add(new ClickLink('del'))->onClick($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender) {


        $acc_code = $sender->owner->getDataItem()->acc_code;
        $cnt = AccountEntry::findcnt("acc_d='{$acc_code}' or acc_c='{$acc_code}'");
        if ($cnt > 0) {
            $this->setError('Нельзя удалить счет  с  проводками');
            return;
        }
        $children = Account::find("pcode =" . Account::qstr($acc_code));
        if (count($children) > 0) {
            $this->setError('Нельзя удалить счет  с дочерними счетами');
            return;
        }


        Account::remove($acc_code);

        $this->updatelists();
    }

    public function addOnClick($sender) {
        $this->acctable->setVisible(false);
        $this->accdetail->setVisible(true);
        // Очищаем  форму
        $this->accdetail->clean();
    }

    public function saveOnClick($sender) {

        $acc = new Account();

        $pr = $this->accdetail->editacc_p->getValue();
        $acc_p = '';
        $acc_code = $this->accdetail->editacc_code->getText();
        $acc_name = $this->accdetail->editacc_name->getText();
        $ac = Account::load($acc_code);
        if ($ac instanceof Account) {
            $this->setError("Код уже  существует");
            return;
        }
        if ($pr > 0) {
            $cnt = AccountEntry::findcnt("acc_d='{$pr}' or acc_c='{$pr}'");
            if ($cnt > 0) {
                $this->setError("Родительский счет имеет проводки");
                return;
            }
            $acc_p = $pr;
        }


        Account::create($acc_code, $acc_name, $acc_p);
        $this->accdetail->setVisible(false);
        $this->acctable->setVisible(true);

        $this->updatelists();
    }

    private function updatelists() {
        $this->_list = Account::find("", "acc_code");
        $this->acctable->list->Reload();

        $list = Account::find('', 'acc_code');
        $p = array();
        foreach ($list as $a) {

            if (strlen($a->acc_code) > 3)
                continue;
            if ($a->acc_code > 0) {
                $p[$a->acc_code] = $a->acc_code . ' ' . $a->acc_name;
            }
        }
        $this->accdetail->editacc_p->setOptionList($p);
    }

    public function cancelOnClick($sender) {
        $this->acctable->setVisible(true);
        $this->accdetail->setVisible(false);
    }

}
