<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use \ZippyERP\ERP\Util;
use \ZippyERP\ERP\Entity\Entry;
use \ZippyERP\ERP\Entity\MoneyFund;
use \ZippyERP\ERP\Helper as H;

/**
 * Класс-сущность  документ инвентаризация
 * 
 */
class Inventory extends Document
{

    public function getRelationBased()
    {
        $list = array();
        $list['InventoryLoss'] = 'Списание ТМЦ';
        $list['InventoryGain'] = 'Оприходование  излищков';

        return $list;
    }

}
