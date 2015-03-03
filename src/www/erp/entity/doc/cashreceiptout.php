<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\ERP\Entity\MoneyFund;
use \ZippyERP\ERP\Entity\Entry;
use \ZippyERP\ERP\Entity\Customer;


/**
 * Класс-сущность  документ  расходный кассовый  ордер
 * 
 */
class CashReceiptOut extends Document
{

    const TYPEOP_CUSTOMER = 1;   // Оплата заказа
    const TYPEOP_BANK = 2;   // Перечисление на счет
    const TYPEOP_CASH = 3;   // В  подотчет

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
            $header['optype']  =  "Оплата поставщику";
        }
        if ($optype == self::TYPEOP_CASH) {
            $emp = \ZippyERP\ERP\Entity\Employee::load($this->headerdata["opdetail"]);
            $header['opdetail']  =  $emp->shortname;
            $header['optype']  =  "В  подотчет";
        }
        if ($optype == self::TYPEOP_BANK) {
            $mf = \ZippyERP\ERP\Entity\MoneyFund::load($this->headerdata["opdetail"]);
            $header['opdetail']  =  $mf->title;
            $header['optype']  =  "Перечисление на счет";
          
        }
        
        $report = new \ZippyERP\ERP\Report('cashreceiptout.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute()
    {
        $mf = MoneyFund::getCash();
        $optype = $this->header['optype'];
        if ($optype == self::TYPEOP_CUSTOMER) {
           // Customer::AddActivity($this->headerdata['opdetail'],0-$this->headerdata['amount'], $this->document_id);
            $ret = Entry::AddEntry(63, 30, $this->headerdata['amount'], $this->document_id,$this->headerdata['opdetail'],$mf->id);
           
        }
        if ($optype == self::TYPEOP_CASH) {
              $ret = Entry::AddEntry(372, 30, $this->headerdata['amount'], $this->document_id,$this->headerdata['opdetail'],$mf->id);
            
        }
        if ($optype == self::TYPEOP_BANK) {
              $ret = Entry::AddEntry(31, 30, $this->headerdata['amount'], $this->document_id,$this->headerdata['opdetail'],$mf->id);
          
        }

        return true;
    }

    // Список  типов операций
    public static function getTypes()
    {
        $list = array();
        $list[self::TYPEOP_CUSTOMER] = "Оплата поставщику";
        $list[self::TYPEOP_BANK] = "Пополнение  счета";
        $list[self::TYPEOP_CASH] = "Расход на подотчета";
        return $list;
    }

}
