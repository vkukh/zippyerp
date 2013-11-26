<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;

/**
 * Класс-сущность  локумент приходная  накладая
 * 
 */
class PurchaseInvoice extends Document
{

        public function generateReport()
        {

                $customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata["customer"]);

                $i = 1;
                $total = 0;
                $detail = array();
                foreach ($this->detaildata as $value) {
                        $detail[] = array("no" => $i++,
                            "tovar_name" => $value['tovarname'],
                            "measure" => $value['measure_name'],
                            "serial_number" => $value['serial_number'],
                            "quantity" => $value['quantity'],
                            "price" => number_format($value['price'] / 100, 2),
                            "amount" => number_format($value['quantity'] * $value['price'] / 100, 2)
                        );
                        $total += $value['quantity'] * $value['price'] / 100;
                }

                $header = array('date' => date('d.m.Y', $this->document_date),
                    "customer" => $customer->customer_name,
                    "document_number" => $this->document_number,
                    "nds" => number_format($this->headerdata["nds"], 2),
                    "total" => number_format($total, 2)
                );


                $reportgen = new \ZCL\RepGen\RepGen(_ROOT . 'templates/erp/templates/purchaseinvoice.html', $header);



                $html = $reportgen->generateSimple($detail);
                return $html;
        }

        protected function ExecuteImpl()
        {
                foreach ($this->detaildata as $value) {
                        //поиск  записи  о  товаре   на складе

                        $stock = \ZippyERP\ERP\Entity\Stock::getStock($this->headerdata['store'], $value['item_id'], $value['price'], true);
                        $stock->updateStock($value['quantity'], $value['price'], $this->document_id, strlen($value['serial_number']) > 0 ? array($value['serial_number']) : array());
                }
                return true;
        }

}

