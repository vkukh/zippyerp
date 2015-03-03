<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\ERP\Entity\MoneyFund;
use \ZippyERP\ERP\Entity\Entry;
use \ZippyERP\ERP\Entity\Customer;
use \ZippyERP\ERP\Entity\Employee;

/**
 * Класс-сущность  документ  приходный кассовый  ордер
 * 
 */
class CashReceiptIn extends Document
{

    const TYPEOP_CUSTOMER = 1;   // Оплата заказа
    const TYPEOP_BANK = 2;   // Снятие  со  счета
    const TYPEOP_CASH = 3;   // Из  подотчета
    const TYPEOP_RET = 4;   // Из  магазина

    public function generateReport()
    {
        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "notes" => $this->headerdata['notes'],
            "amount" => \ZippyERP\ERP\Helper::fm($this->headerdata["amount"])
        );
         $optype = $this->headerdata['optype'];
         
        if ($optype == self::TYPEOP_CUSTOMER) {
            $customer = \ZippyERP\ERP\Entity\Customer::load($this->headerdata["opdetail"]);
            $header['opdetail']  =  $customer->customer_name;
            $header['optype']  =  "Оплата от покупателя";
        }
        if ($optype == self::TYPEOP_CASH) {
            $emp = \ZippyERP\ERP\Entity\Employee::load($this->headerdata["opdetail"]);
            $header['opdetail']  =  $emp->shortname;
            $header['optype']  =  "Возврат из подотчета";
        }
        if ($optype == self::TYPEOP_BANK) {
            $mf = \ZippyERP\ERP\Entity\MoneyFund::load($this->headerdata["opdetail"]);
            $header['opdetail']  =  $mf->title;
            $header['optype']  =  "Снятие с банковского счета";
          
        }
        if ($optype == self::TYPEOP_RET) {
             $store = \ZippyERP\ERP\Entity\Store::load($this->headerdata["opdetail"]);
            $header['opdetail']  =  $store->store_name;
            $header['optype']  =  "Выручка   с розницы";
           
        }         
        $report = new \ZippyERP\ERP\Report('cashreceiptin.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute()
    {

        $mf = MoneyFund::getCash();
        //MoneyFund::AddActivity($mf->id, $this->headerdata['amount'], $this->document_id);
        $ret = "";
        $optype = $this->headerdata['optype'];
        if ($optype == self::TYPEOP_CUSTOMER) {
            
            //Customer::AddActivity($this->headerdata['opdetail'],$this->headerdata['amount'], $this->document_id);
            $ret = Entry::AddEntry(30, 36, $this->headerdata['amount'], $this->document_id,$mf->id,$this->headerdata['opdetail']);
        }
        if ($optype == self::TYPEOP_CASH) {
          $ret = Entry::AddEntry(30, 372, $this->headerdata['amount'], $this->document_id,$mf->id,$this->headerdata['opdetail']);
        }
        if ($optype == self::TYPEOP_BANK) {
            $ret = Entry::AddEntry(30, 31, $this->headerdata['amount'], $this->document_id,$mf->id,$this->headerdata['opdetail']);
        }
        if ($optype == self::TYPEOP_RET) {
            $store_id = $this->headerdata['opdetail']; // магазин
            $ret = Entry::AddEntry(30, 702, $this->headerdata['amount'], $this->document_id,$mf->id,$store_id);
        }
        if (strlen($ret) > 0)
            throw new \Exception($ret);
        return true;
    }

    // Список  типов операций
    public static function getTypes()
    {
        $list = array();
        $list[self::TYPEOP_CUSTOMER] = "Оплата покупателя";
        $list[self::TYPEOP_BANK] = "Снятие  со  счета";
        $list[self::TYPEOP_CASH] = "Приход  с  подотчета";
        $list[self::TYPEOP_RET] = "Приход с розницы";
        return $list;
    }

}
