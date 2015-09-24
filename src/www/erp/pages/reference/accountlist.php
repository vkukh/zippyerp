<?php

namespace ZippyERP\ERP\Pages\Reference;

use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Label;
use \Zippy\Html\DataList\ArrayDataSource;
use \ZippyERP\ERP\Entity\Account;
use \ZippyERP\ERP\Helper as H;

/**
 * Класс страницы плана счетов
 */
class AccountList extends \ZippyERP\ERP\Pages\Base
{

    public function __construct()
    {
        parent::__construct();
        $this->add(new DataView('list', new ArrayDataSource(Account::find("", "cast(acc_code as char)")), $this, 'listOnRow'))->Reload();
    }

    public function listOnRow($row)
    {
        $item = $row->getDataItem();
        $row->add(new Label('code', $item->acc_code));
        $row->add(new Label('name', $item->acc_name));
        $saldo = $item->getSaldo();
        $row->add(new Label('dt', $saldo > 0 ? H::fm($saldo) : ""));
        $row->add(new Label('ct', $saldo < 0 ? H::fm(0 - $saldo) : ""));
    }

}
