<?php

namespace ZippyERP\ERP\Entity;

/**
 * Клас-сущность  метаданные
 *
 * @table=erp_metadata
 * @keyfield=meta_id
 */
class MetaData extends \ZCL\DB\Entity
{

    protected function beforeDelete()
    {
        return true;
    }

}
