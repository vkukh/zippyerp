<?php

namespace ZippyERP\ERP\Entity;

/**
 * Клас-сущность  склад
 * 
 * @table=erp_store
 * @keyfield=store_id
 */
class Store extends \ZCL\DB\Entity
{

    const STORE_TYPE_OPT = 1; //  Оптовый  склад
    const STORE_TYPE_RET = 2; //  Розничный  склад
    const STORE_TYPE_RET_SUM = 3; //  Магазин  с  суммовым  учетом

    protected function beforeDelete()
    {
        return false;
    }

}
