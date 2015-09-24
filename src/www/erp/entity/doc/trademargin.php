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
        $item = \ZippyERP\ERP\Entity\Item::getSumItem();

        $stock = \ZippyERP\ERP\Entity\Stock::getStock($store_id, $item->item_id, 1, true);

        $discont = 0; //скидки  704  счет
        $date = new Carbon();
        $date->setTimestamp($this->document_date);
        $end = $date->endOfMonth()->getTimestamp();
        $begin = $date->startOfMonth()->getTimestamp();






        //$a285 = Account::load(285);
       // $os285 = $a285->getSaldoAndOb($begin, $end, 0, $store_id);

       // $k = ($os285['endct'] - $discont) / ($tov - $discont);

        //$a702 = Account::load(702); //выручка (списано  с  магазина)
        // реализация
        //$saled = $a702->getSaldoAndOb($begin, $end, 0, $store_id);
        //себетоимость
        //$sb = (1 - $k) * $saled['obct'];
        //$sb = (int) $sb;





        //$sql = " select coalesce(sum(amount),0) from erp_account_subconto where amount > 0  and account_id=282 and  stock_id = {$stock} and date(document_date) <= " . $conn->DBDate($end). " and date(document_date) >= " . $conn->DBDate($begin);
       // $sb = $conn->GetOne($sql);  //себестоимость


        $sql = " select coalesce(abs(sum(amount)),0) from erp_account_subconto where amount < 0  and account_id=285 and  extcode  = {$store_id} and date(document_date) <= " . $conn->DBDate($end);
        $tm = $conn->GetOne($sql);  //торговая  наценка на  конец периода

        //остатки на  конец периода
        $sql = " select coalesce(sum(quantity),0) AS quantity,coalesce(price,0) as price  from erp_account_subconto sc join erp_store_stock  st on sc.stock_id = st.stock_id where    store_id = {$store_id} and date(document_date) <= " . $conn->DBDate($end);
        $row = $conn->GetRow($sql);
        $ost= $row['quantity'] / 1000;
        $ost = $ost * $row['price'];

        if($ost ==0) return;

        //выручка
        $sql = " select coalesce(abs(sum(amount)),0) from erp_account_subconto where amount < 0  and account_id=702 and  extcode  = {$store_id} and date(document_date) <= " . $conn->DBDate($end). " and date(document_date) >= " . $conn->DBDate($begin);
        $saled = $conn->GetOne($sql);  //выручка сданная в кассу

        $k = ($tm - $discont) / ($ost - $discont);
        $sb = (1- $k)*$saled;; //себестоимость

        // списываем  наценку
        Entry::AddEntry("285", "282", $saled - $sb, $this->document_id,$this->document_date);
        // себестоимость реализации
        Entry::AddEntry("902", "282", $sb, $this->document_id,$this->document_date);

        // НДС
        $nds = H::nds(true);
        Entry::AddEntry("702", "641",$saled * $nds, $this->document_id,$this->document_date);




        return true;
    }

}
