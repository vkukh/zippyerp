<?php

namespace ZippyERP\ERP\Entity\Doc;

use ZippyERP\ERP\Helper as H;

/**
 * Документ - заказ  поставщику
 */
class SupplierOrder extends Document
{

    protected function init()
    {
        parent::init();
        $this->datatag = 0; //поставщик
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
                "quantity" => $value['quantity'] / 1000,
                "price" => H::fm($value['price']),
                "amount" => H::fm(($value['quantity'] / 1000) * $value['price'])
            );
            $total += ($value['quantity'] / 1000) * $value['price'];
        }


        $header = array('date' => date('d.m.Y', $this->document_date),
            "customername" => $this->headerdata['suppliername'],
            "document_number" => $this->document_number,
            "timeline" => date('d.m.Y', $this->headerdata['timeline']),
            "total" => H::fm($total));

        $report = new \ZippyERP\ERP\Report('supplierorder.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {
         return true;
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
