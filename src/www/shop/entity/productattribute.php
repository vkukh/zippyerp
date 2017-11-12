<?php

namespace ZippyERP\Shop\Entity;

/**
 * класс-сущность  атрибута продукта
 * @table=shop_attributes
 * @keyfield=attribute_id
 */
class ProductAttribute extends \ZCL\DB\Entity
{

    public $searchvalue;

    protected function init() {
        $this->attribute_id = 0;
        $this->showinlist = 0;
        $this->value = '';
    }

}
