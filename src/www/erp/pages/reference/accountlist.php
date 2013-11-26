<?php

namespace ZippyERP\ERP\Pages\Reference;

use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Label;
use \Zippy\Html\DataList\ArrayDataSource;
use \ZippyERP\ERP\Entity\Account;

/**
 * Класс страницы плана счетов
 */
class AccountList extends \ZippyERP\ERP\Pages\Base
{

        public function __construct()
        {
                parent::__construct();
                $this->add(new DataView('list', new ArrayDataSource(Account::find("", "acc_code")), $this, 'listOnRow'))->Reload();
        }

        public function listOnRow($row)
        {
                $item = $row->getDataItem();
                $row->add(new Label('code', $item->acc_code));
                $row->add(new Label('name', $item->acc_name));
                $saldo = $item->getSaldo();
                $row->add(new Label('dt', $saldo > 0 ? number_format($saldo / 100.0, 2) : ""));
                $row->add(new Label('ct', $saldo < 0 ? number_format(0 - $saldo / 100.0, 2) : ""));
        }

}
