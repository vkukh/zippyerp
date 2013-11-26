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

        protected function beforeDelete()
        {
                return false;
        }

}

