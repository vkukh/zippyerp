<?php

namespace ZippyERP\Shop\Entity;

//класс-сущность  строки  детализации  заказа
class OrderDetail extends \ZCL\DB\Entity
{

    protected function init() {
        $this->order_id = 0;
        $this->orderdetail_id = 0;
    }

    protected static function getMetadata() {
        return array('table' => 'shop_orderdetails', 'view' => 'shop_orderdetails_view', 'keyfield' => 'orderdetail_id');
    }

}
