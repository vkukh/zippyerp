<?php

namespace ZippyERP\Shop\Entity;

/**
 * класс-сущность  атрибута производитель
 * @table=shop_manufacturers
 * @keyfield=manufacturer_id
 */
class Manufacturer extends \ZCL\DB\Entity
{

    protected function init() {
        $this->manufacturer_id = 0;
    }

    public static function getNames() {
        $names = array();
        $list = Manufacturer::find('', 'manufacturername');
        foreach ($list as $item) {
            $names[$item->manufacturer_id] = $item->manufacturername;
        }
        return $names;
    }

}
