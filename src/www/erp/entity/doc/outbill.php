<?php

namespace ZippyCMS\Store\Entity;

use \ZippyERP\System\System;

/**
 * Класс-сущность  локумент приходная  накладая
 * 
 */
class OutBill extends Document
{

        public function generateReport()
        {
                $i = 1;
                $detail = array();
                $total = 0;
                foreach ($this->detaildata as $value) {
                        $detail[] = array("no" => $i++,
                            "tovar_name" => $value['tovarname'],
                            "measure" => $value['measure_name'],
                            "serial_number" => $value['serial_number'],
                            "quantity" => $value['quantity'],
                            "price" => number_format($value['price'], 2),
                            "amount" => number_format($value['quantity'] * $value['price'], 2)
                        );
                        $total += $value['quantity'] * $value['price'];
                }

                $header = array('date' => date('d.m.Y', $this->document_date),
                    "document_number" => $this->document_number,
                    "nds" => number_format($this->headerdata["nds"], 2),
                    "total" => number_format($total, 2)
                );

                $reportgen = new \ZCL\RepGen\RepGen(_ROOT . 'themes/' . \ZippyERP\System\System::getTheme() . '/modules/store/templates/outbill.html', $header);




                $html = $reportgen->generateSimple($detail);

                return $html;
        }

        public function Execute()
        {
                foreach ($this->detaildata as $value) {
                        $stock = Stock::getStock($this->headerdata['store'], $value['tovar_id'], $value['partion'], true);
                        $stock->updateStock(0 - $value['quantity'], $this->document_id, strlen($value['serial_number']) > 0 ? array($value['serial_number']) : array());
                }
                return true;
        }

}

