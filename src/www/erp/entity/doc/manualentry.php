<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\ERP\Helper as H;
use \ZippyERP\ERP\Entity\Item;
use \ZippyERP\ERP\Entity\Entry;
use \ZippyERP\ERP\Entity\SubConto;
use \ZippyERP\ERP\Entity\Stock;
use \ZippyERP\ERP\Entity\Store;
use \ZippyERP\ERP\Entity\Employee;
use \ZippyERP\ERP\Entity\Customer;
use \ZippyERP\ERP\Entity\MoneyFund;

/**
 * Класс-сущность  документ для ручных  операций
 * и  ввода начальных остатков
 *
 */
class ManualEntry extends Document
{

    protected function init()
    {
        parent::init();
    }

    public function Execute()
    {
        $accarr = unserialize(base64_decode($this->headerdata['entry']));
        foreach ($accarr as $entry) {


            Entry::AddEntry($entry->acc_d, $entry->acc_c, $entry->amount, $this->document_id, $this->document_date);
        }

        //ТМЦ
        $itemarr = unserialize(base64_decode($this->headerdata['item']));
        if (is_array($itemarr)) {

            foreach ($itemarr as $item) {
                $stock = Stock::getStock($item->store_id, $item->item_id, $item->price, true);
                $acc = explode('_', $item->op);
                $sc = new SubConto($this, $acc[0], $acc[1] == 'd' ? ($item->qty) / 1000 * $stock->price : 0 - ($item->qty) / 1000 * $stock->price);
                $sc->setStock($stock->stock_id);
                $sc->setQuantity($acc[1] == 'd' ? $item->qty : 0 - $item->qty);
                $sc->save();
            }
        }
        //сотрудники (лицевые  счета)
        $emparr = unserialize(base64_decode($this->headerdata['emp']));
        if (count($emparr) > 0) {

            foreach ($emparr as $emp) {

                $val = $emp->val;
                $acc = explode('_', $emp->op);

                $sc = new SubConto($this, $acc[0], $acc[1] == 'd' ? $val : 0 - $val);
                $sc->setEmployee($emp->employee_id);
                $sc->save();
            }
        }
        //контрагенты (взаиморасчеты)
        $carr = unserialize(base64_decode($this->headerdata['c']));
        if (count($carr) > 0) {

            foreach ($carr as $c) {
                $val = $c->val;
                $acc = explode('_', $c->op);

                $sc = new SubConto($this, $acc[0], $acc[1] == 'd' ? $val : 0 - $val);
                $sc->setCustomer($c->customer_id);
                $sc->save();
            }
        }
        //денежные  счета
        $farr = unserialize(base64_decode($this->headerdata['f']));
        if (count($farr) > 0) {

            foreach ($farr as $f) {
                $val = $f->val;
                $acc = explode('_', $f->op);

                $sc = new SubConto($this, $acc[0], $acc[1] == 'd' ? $val : 0 - $val);
                $sc->setMoneyfund($f->id);
                $sc->save();
            }
        }
    }

    public function generateReport()
    {

        $header = array(
            'date' => date('d.m.Y', $this->document_date),
            "description" => $this->headerdata["description"],
            "document_number" => $this->document_number
        );
        $detail = array();
        $i = 1;
        $arr = array();
        $accarr = unserialize(base64_decode($this->headerdata['entry']));
        foreach ($accarr as $entry) {
            $arr[] = array("no" => $i++,
                "acc_d" => $entry->acc_d,
                "acc_c" => $entry->acc_c,
                "amount" => H::fm($entry->amount));
        }
        $detail['entry'] = $arr;

        //ТМЦ
        $arr = array();
        $itemarr = unserialize(base64_decode($this->headerdata['item']));
        foreach ($itemarr as $item) {
            $op = str_replace('_d', ' Дебет', $item->op);
            $op = str_replace('_c', ' Кредит', $op);
            $arr[] = array("no" => $i++,
                "opname" => $op,
                "store_name" => $item->store_name,
                "item_name" => $item->itemname,
                "qty" => $item->qty / 1000,
                "price" => H::fm($item->price),
                "amount" => H::fm($item->price * ($item->qty / 1000)));
        }
        $detail['item'] = $arr;
        //Сотрудники
        $arr = array();
        $itemarr = unserialize(base64_decode($this->headerdata['emp']));
        foreach ($itemarr as $item) {

            $op = str_replace('_d', ' Дебет', $item->op);
            $op = str_replace('_c', ' Кредит', $op);
            $arr[] = array("no" => $i++,
                "opname" => $op,
                "name" => $item->fullname,
                "amount" => H::fm($item->val));
        }
        $detail['emp'] = $arr;

        //Контрагенты
        $arr = array();
        $itemarr = unserialize(base64_decode($this->headerdata['c']));
        foreach ($itemarr as $item) {
            $op = str_replace('_d', ' Дебет', $item->op);
            $op = str_replace('_c', ' Кредит', $op);
            $arr[] = array("no" => $i++,
                "opname" => $op,
                "optype" => $item->type == 1 ? "Покупатель" : "Поставщик",
                "name" => $item->customer_name,
                "amount" => H::fm($item->val));
        }
        $detail['c'] = $arr;

        //Денежные  счета
        $arr = array();
        $itemarr = unserialize(base64_decode($this->headerdata['f']));
        foreach ($itemarr as $item) {
            $op = str_replace('_d', ' Дебет', $item->op);
            $op = str_replace('_c', ' Кредит', $op);

            $arr[] = array("no" => $i++,
                "opname" => $op,
                "name" => $item->title,
                "amount" => H::fm($item->val));
        }
        $detail['f'] = $arr;


        $report = new \ZippyERP\ERP\Report('manualentry.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

}
