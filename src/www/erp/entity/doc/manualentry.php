<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\ERP\Helper as H;
use \ZippyERP\ERP\Entity\Item;
use \ZippyERP\ERP\Entity\Entry;
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


           Entry::AddEntry($entry->acc_d, $entry->acc_c, $entry->amount, $this->document_id);
        }

        //ТМЦ
        $itemarr = unserialize(base64_decode($this->headerdata['item']));
        if(count($itemarr) > 0){
            $a20d = $a22d = $a26d = $a28d = 0; 
            $a20c = $a22c = $a26c = $a28c = 0; 
            foreach ($itemarr as $item) {
                $stock = Stock::getStock($item->store_id, $item->item_id, $item->price, true);
                $stock->updateStock($item->op ==1 ? $item->qty : 0 - $item->qty, $this->document_id, array());
                // группируем  по  бух. счетам
                $amount = $item->qty * $item->price;
                if($item->item_type == Item::ITEM_TYPE_STUFF){
                   if($item->op == 1)  $a20d +=  $amount; 
                   else  $a20c +=  $amount; 
                }
                if($item->item_type == Item::ITEM_TYPE_MBP){
                   if($item->op == 1)  $a22d +=  $amount; 
                   else  $a22c +=  $amount; 
                }
                if($item->item_type == Item::ITEM_TYPE_GOODS){
                   if($item->op == 1)  $a28d +=  $amount; 
                   else  $a28c +=  $amount; 
                }
                if($item->item_type == Item::ITEM_TYPE_PRODUCTION){
                   if($item->op == 1)  $a26d +=  $amount; 
                   else  $a26c +=  $amount; 
                }
                if($a20d > 0) Entry::AddEntry("201", -1, $a20d, $this->document_id);
                if($a20c > 0) Entry::AddEntry(-1, "201", $a20c, $this->document_id);
                if($a22d > 0) Entry::AddEntry("22", -1, $a22d, $this->document_id);
                if($a22c > 0) Entry::AddEntry(-1, "22", $a22c, $this->document_id);
                if($a26d > 0) Entry::AddEntry("26", -1, $a26d, $this->document_id);
                if($a26c > 0) Entry::AddEntry(-1, "26", $a26c, $this->document_id);
                if($a28d > 0) Entry::AddEntry("281", -1, $a28d, $this->document_id);
                if($a28c > 0) Entry::AddEntry(-1, "281", $a28c, $this->document_id);
            }
        }
        //сотрудники (лицевые  счета)
        $emparr = unserialize(base64_decode($this->headerdata['emp']));
        if(count($emparr) > 0){
              $a372d = $a372c = 0;        
              $a661d = $a661c = 0;        
              foreach ($emparr as $emp) {
                  $tax = 0;
                  $val = $emp->val;
                  if($emp->op == 2 || $emp->op == 4) $val = 0 - $val;  //расход
                  if($emp->op == 3 || $emp->op == 4) $tax = Employee::TAX_ACCOUNTABLE;  //подотчет
                  
                  Employee::AddActivity($emp->employee_id, $val, $this->document_id,$tax);
                  // группируем  по  бух. счетам
                  if($emp->op == 1) $a661c +=  $emp->val;
                  if($emp->op == 2) $a661d +=  $emp->val;
                  if($emp->op == 3) $a372d +=  $emp->val;
                  if($emp->op == 4) $a372c +=  $emp->val;
                  if($a661c > 0) Entry::AddEntry(-1, "661", $a661c, $this->document_id);
                  if($a661d > 0) Entry::AddEntry("661", -1 , $a661d, $this->document_id);
                  if($a372d > 0) Entry::AddEntry("372", -1 , $a372d, $this->document_id);
                  if($a372c > 0) Entry::AddEntry(-1, "372", $a372c, $this->document_id);
                 
              }
        }
        //контрагенты (взаиморасчеты)
        $carr = unserialize(base64_decode($this->headerdata['c']));
        if(count($carr) > 0){
            $a36c =  $a36d = $a63c = $a63d= 0;
            foreach ($carr as $c) {
               // Customer::AddActivity($c->customer_id, $c->op ==1 ? $c->val : 0 - $c->val, $this->document_id);
                if($c->op ==2 && $c->type ==1) Entry::AddEntry(-1, "36", $c->val, $this->document_id,0,$c->customer_id);
                if($c->op ==1 && $c->type ==1) Entry::AddEntry( "36",-1, $c->val, $this->document_id,$c->customer_id,0);;
                if($c->op ==1 && $c->type ==2) Entry::AddEntry( "63",-1, $c->val, $this->document_id,$c->customer_id,0);
                if($c->op ==2 && $c->type ==2) Entry::AddEntry( -1,"63", $c->val, $this->document_id,0,$c->customer_id);
            }
                 
        }
        //денежные  счета
        $farr = unserialize(base64_decode($this->headerdata['f']));
        if(count($farr) > 0){
             $a30d = $a30c = $a31d = $a31c = 0;
             foreach ($farr as $f) {
               // MoneyFund::AddActivity($f->id, $f->op ==1 ? $f->val : $f->val, $this->document_id);
                if($f->ftype ==0 && $f->op == 1)  Entry::AddEntry("30", -1, $f->val, $this->document_id,$f->id,0);
                if($f->ftype ==0 && $f->op == 2)  Entry::AddEntry(-1, "30", $f->val, $this->document_id,0,$f->id);
                if($f->ftype > 0 && $f->op == 1)  Entry::AddEntry("31", -1, $f->val, $this->document_id,$f->id,0);
                if($f->ftype > 0 && $f->op == 2)  Entry::AddEntry(-1, "31", $f->val, $this->document_id,0,$f->id);
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
            $arr[] = array("no" => $i++,
                "opname" => $item->op ==1 ? '+':'-',
                "code" => $item->code,
                "store_name" => $item->store_name,
                "item_name" => $item->itemname,
                "qty" => $item->qty,
                "price" => H::fm($item->price),
                "amount" => H::fm($item->price * $item->qty));
        }
        $detail['item'] = $arr;
        //Сотрудники
        $arr = array();
        $itemarr = unserialize(base64_decode($this->headerdata['emp']));
        foreach ($itemarr as $item) {
        
        $op = $item->op == 1 || $item->op == 3 ? "+":"-" ;
        $op = ($op . ' ') . ($item->op == 1 || $item->op == 2 ? "зарплата":"подотчет") ;
         
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
        
          $arr[] = array("no" => $i++,
                "opname" => $item->op == 1 ? "Долг нам":"Долг наш",
                "optype" => $item->type == 1 ? "Покупатель":"Поставщик",
                "name" => $item->customer_name,
                "amount" => H::fm($item->val));
        }
        $detail['c'] = $arr;
       
        //Денежные  счета
        $arr = array();
        $itemarr = unserialize(base64_decode($this->headerdata['f']));
        foreach ($itemarr as $item) {
          
            $arr[] = array("no" => $i++,
                "opname" => $item->op == 1 ? "+":"-",
                "name" => $item->title,
                "amount" => H::fm($item->val));
        }
        $detail['f'] = $arr;    
        
            
        $report = new \ZippyERP\ERP\Report('manualentry.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

}
