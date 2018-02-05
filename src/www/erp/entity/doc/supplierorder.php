<?php

namespace ZippyERP\ERP\Entity\Doc;

use ZippyERP\ERP\Helper as H;

/**
 * Документ - заказ  поставщику
 */
class SupplierOrder extends Document
{

    protected function init() {
        parent::init();
        $this->datatag = 0; //поставщик
    }

    public function generateReport() {
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
            "empname" => $this->headerdata["empname"],
            "timeline" => date('d.m.Y', $this->headerdata['timeline']),
            "total" => H::fm($total));

        $report = new \ZippyERP\ERP\Report('supplierorder.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute() {
        return true;
    }

    //список состояний  для   выпадающих списков
    public static function getStatesList() {
        $list = array();
        $list[Document::STATE_NEW] = 'Новий';
        $list[Document::STATE_WA] = 'Чекає затвердження';
        $list[Document::STATE_APPROVED] = 'Затверджений';
        $list[Document::STATE_WORK] = 'Виконується';
        $list[Document::STATE_CLOSED] = 'Закритий';

        return $list;
    }

    public function getRelationBased() {
        $list = array();
        $list['PurchaseInvoice'] = 'Вхідний рахунок';
        //  $list['GoodsReceipt'] = 'Прибуткова накладна';
        return $list;
    }

}
