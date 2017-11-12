<?php

namespace ZippyERP\Shop\Entity;

//класс-сущность  товара
/**
 * @keyfield=product_id
 * @table=shop_products
 * @view=shop_products_view
 */
class Product extends \ZCL\DB\Entity
{

    public $attributevalues;

    protected function init() {
        $this->product_id = 0;
        $this->image_id = 0;
        $this->group_id = 0;
        $this->price = 0;
        $this->old_price = 0;
        $this->novelty = 0;
        $this->topsaled = 0;
        $this->attributevalues = array();
        $this->created = time();
    }

    protected function afterLoad() {

        $this->rated = round($this->rated);
        $this->created = strtotime($this->created);
        $this->novelty = $this->created > strtotime("-1 month") ? 1 : 0;
    }

    protected function afterSave($update) {
        $conn = \ZCL\DB\DB::getConnect();
        $conn->Execute("delete from shop_attributevalues where  product_id=" . $this->product_id);
        foreach ($this->attributevalues as $key => $value) {
            if ($value != null) {
                $conn->Execute("insert  into shop_attributevalues (attribute_id,product_id,attributevalue) values ({$key},{$this->product_id}," . $conn->qstr($value) . ")");
            }
        }
    }

    protected function beforeDelete() {
        $conn = \ZCL\DB\DB::getConnect();
        $conn->Execute("delete from shop_attributevalues where  product_id=" . $this->product_id);
        Image::delete($this->image_id);
    }

    /**
     * Возвращает список аттрибутов  со значениями
     * 
     */
    public function getAttrList() {
        $conn = \ZCL\DB\DB::getConnect();

        $attrlist = ProductAttribute::find("group_id=" . $this->group_id);
        $ret = array();
        $attrvalues = array();
        //выбираем значения атриутов продукта
        $rows = $conn->Execute("select attribute_id,attributevalue from shop_attributevalues where  product_id=" . $this->product_id);
        foreach ($rows as $row) {
            $attrvalues[$row['attribute_id']] = $row['attributevalue'];
        }

        foreach ($attrlist as $attr) {
            $attr->value = @$attrvalues[$attr->attribute_id];
            $ret[] = $attr;
        }

        return $ret;
    }

    /**
     * Возвращает  ЧПУ  строку.  Если  не  задана,   возвращвет id
     * 
     */
    public function getSEF() {
        return strlen($this->sef) > 0 ? $this->sef : $this->product_id;
    }

    /**
     * Загружает товар   по  ЧПУ коду
     * 
     */
    public static function loadSEF($sef) {
        $list = self::find("product_id={$sef} or sef='{$sef}'");
        if (count($list) > 0) {
            return array_pop($list);
        } else {
            return null;
        }
    }

}
