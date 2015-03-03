<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\ERP\Helper as H;

/**
 * Документ - заказ  поставщику
 */
class SupplierOrder extends Document
{

    protected function init()
    {
        parent::init();
        $this->intattr1 = 0; //поставщик
        $this->intattr2 = 0; // оплата
    }

    public function generateReport()
    {
        $i = 1;
        $detail = array();
        $total = 0;
        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "tovar_name" => $value['itemname'],
                "measure" => $value['measure_name'],
                "quantity" => $value['quantity'],
                "price" => H::fm($value['price']),
                "amount" => H::fm($value['quantity'] * $value['price'])
            );
            $total += $value['quantity'] * $value['price'];
        }

        $customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata["supplier"]);
        $header = array('date' => date('d.m.Y', $this->document_date),
            "customername" => $customer->customer_name,
            "document_number" => $this->document_number,
            "base" => $this->base,
            "total" => H::fm($total));

        $report = new \ZippyERP\ERP\Report('supplierorder.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {


        return true;
        ;
    }

    //список состояний  для   выпадающих списков
    public static function getStatesList()
    {
        $list = array();
        $list[Document::STATE_NEW] = 'Новый';
        $list[Document::STATE_WA] = 'Ждет утверждения';
        $list[Document::STATE_APPROVED] = 'Утвержден';
        $list[Document::STATE_WORK] = 'В работе';
        $list[Document::STATE_CLOSED] = 'Закрыт';

        return $list;
    }

    public function getRelationBased()
    {
        $list = array();
        $list['PurchaseInvoice'] = 'Счет входящий';
        //  $list['GoodsReceipt'] = 'Приходная накладная';
        return $list;
    }

}
