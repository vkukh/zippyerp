<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use \ZippyERP\ERP\Util;
use \ZippyERP\ERP\Helper as H;
use \ZippyERP\ERP\Entity\Entry;
use \ZippyERP\ERP\Entity\Account;
use Carbon\Carbon;

/**
 * Класс-сущность  документ списание  торговой наценки
 * 
 */
class TradeMargin extends Document
{
    public function generateReport()
    {



        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "storename" => $this->headerdata['storename']
            
        );

        $report = new \ZippyERP\ERP\Report('trademargin.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute()
    {
         $conn = \ZCL\DB\DB::getConnect();
        
         $store_id = $this->headerdata['store_id'];
         $discont = 0; //скидки  704  счет
         $date = new Carbon();
         $date->setTimestamp($this->document_date);
         $end = $date->endOfMonth()->getTimestamp();
         $begin = $date->startOfMonth()->getTimestamp();

//        Entry::AddEntry(79, 902, $s902, $this->document_id);


        //остаток товаров  на начало учетного периода;
        $sql = " select coalesce(sum(quantity*price),0) AS quantity  from erp_stock_activity_view  where    store_id = {$store_id} and date(document_date) <= " . $conn->DBDate($end);
        $tov =  $conn->GetOne($sql);
                   

        //$sql = " select coalesce(sum(quantity*price),0) AS quantity  from erp_stock_activity_view  where    store_id = {$store_id} and date(document_date) >= " . $conn->DBDate($begin) . " and date(document_date) <= " . $conn->DBDate($end);
        //$ob =  $conn->GetOne($sql);

        $a285 = Account::load(285);
        $os285 = $a285->getSaldoAndOb($begin,$end,0,$store_id);
        
        $k = ($os285['endct'] - $discont)/($tov - $discont) ;
        
        $a702 = Account::load(702); //выручка (списано  с  магазина)
        
        // реализация
        $saled = $a702->getSaldoAndOb($begin,$end,0,$store_id);
        //себетоимость
        $sb = (1 - $k)* $saled['obct'];  
        $sb  = (int)$sb;  
       // списываем  наценку
        Entry::AddEntry("285", "282", $saled['obct']- $sb, $this->document_id);
        // себестоимость реализации
        Entry::AddEntry("902", "282", $saled['obct'] , $this->document_id);
        
        return true;
    }    
}
