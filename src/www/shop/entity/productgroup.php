<?php

namespace ZippyERP\Shop\Entity;

/**
 * класс-сущность  группы товаров
 * @table=shop_productgroups
 * @view=shop_productgroups_view
 * @keyfield=group_id
 * @parentfield=parent_id
 * @pathfield=mpath  
 */
class ProductGroup extends \ZCL\DB\TreeEntity
{

    protected function init() {
        $this->group_id = 0;
        $this->parent_id = 0;
        $this->mpath = '';
    }

}
