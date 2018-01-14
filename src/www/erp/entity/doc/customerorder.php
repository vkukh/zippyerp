<?php

namespace ZippyERP\ERP\Entity\Doc;

use ZippyERP\ERP\Helper as H;

/**
 * документ - заказ  клиента
 */
class CustomerOrder extends Document
{

    protected function init()
    {
        parent::init();
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

        //$customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata["customer"]);
        $header = array('date' => date('d.m.Y', $this->document_date),
            "customername" => $this->headerdata["customername"],
            "document_number" => $this->document_number,
            "base" => $this->base,
            "total" => H::fm($total));

        $report = new \ZippyERP\ERP\Report('customerorder.tpl');

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
        $list[Document::STATE_NEW] = 'Новий';
        $list[Document::STATE_WA] = 'Очікує підтвердження';
        $list[Document::STATE_APPROVED] = 'Затврджений';
        $list[Document::STATE_WORK] = 'Виконується';
        $list[Document::STATE_CLOSED] = 'Закритий';
        $list[Document::STATE_WORK] = 'В процесі виробництва';
        $list[Document::STATE_WP] = 'Очікує оплату оплату';
        $list[Document::STATE_INSHIPMENT] = 'Відгрудений';

        return $list;
    }

    public function getRelationBased()
    {
        $list = array();
        $list['Invoice'] = 'Рахунок-фактура';
        return $list;
    }

}
