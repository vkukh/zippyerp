<?php

namespace ZippyERP\ERP\Entity\Doc;

use ZippyERP\ERP\Helper as H;

/**
 * Класс-сущность  документ инвентаризация
 *
 */
class Inventory extends Document
{

    public function generateReport()
    {


        $i = 1;
        $detail = array();

        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "tovar_name" => $value['itemname'],
                "measure" => $value['measure_name'],
                "quantity" => $value['quantity'] / 1000,
                "realquantity" => $value['realquantity'] / 1000,
                "price" => H::fm($value['price']),
                "amount" => H::fm(($value['quantity'] / 1000) * $value['price'])
            );
        }

        //$firm = \ZippyERP\System\System::getOptions("firmdetail");
        // $customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata["customer"]);
        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "storename" => $this->headerdata["storename"],
            "itemtypename" => $this->headerdata["itemtypename"]
        );

        $report = new \ZippyERP\ERP\Report('inventory.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function getRelationBased()
    {
        $list = array();
        $list['InventoryLost'] = 'Списание ТМЦ (потери)';
        $list['InventoryGain'] = 'Оприходование  излищков';

        return $list;
    }

}
