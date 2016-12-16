<?php

namespace ZippyERP\ERP\Entity\Doc;

use Carbon\Carbon;
use ZippyERP\ERP\Entity\Entry;
use ZippyERP\ERP\Entity\SubConto;
use ZippyERP\ERP\Helper as H;

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
        $conn = \ZDB\DB::getConnect();

        $store_id = $this->headerdata['store_id'];
        $item = \ZippyERP\ERP\Entity\Item::getSumItem();

        //$stock = \ZippyERP\ERP\Entity\Stock::getStock($store_id, $item->item_id, 1, true);

        $discont = 0; //скидки  704  счет
        $date = new Carbon();
        $date->setTimestamp($this->document_date);
        $end = $date->endOfMonth()->getTimestamp();
        $begin = $date->startOfMonth()->getTimestamp();


        $sql = " select coalesce(abs(sum(amount)),0) from erp_account_subconto where amount < 0  and account_id=285 and  extcode  = {$store_id} and  document_date  <= " . $conn->DBDate($end);
        $tm = $conn->GetOne($sql);  //торговая  наценка на  конец периода
        //остатки на  конец периода
        $sql = " select coalesce(sum(quantity),0) AS quantity,coalesce(price,0) as price  from erp_account_subconto sc join erp_store_stock  st on sc.stock_id = st.stock_id where    store_id = {$store_id} and date(document_date) <= " . $conn->DBDate($end);
        $row = $conn->GetRow($sql);
        $ost = $row['quantity'] / 1000;
        $ost = $ost * $row['price'];

        if ($ost == 0)
            return null;

        //выручка
        $sql = " select coalesce(abs(sum(amount)),0) from erp_account_subconto where amount < 0  and account_id=702 and  extcode  = {$store_id} and date(document_date) <= " . $conn->DBDate($end) . " and date(document_date) >= " . $conn->DBDate($begin);
        $saled = $conn->GetOne($sql);  //выручка сданная в кассу

        $k = ($tm - $discont) / ($ost - $discont);
        $sb = (1 - $k) * $saled;
        ; //себестоимость
        // списываем  наценку
        Entry::AddEntry("285", "282", $saled - $sb, $this->document_id, $this->document_date);
        // себестоимость реализации
        Entry::AddEntry("902", "282", $sb, $this->document_id, $this->document_date);

        $item = \ZippyERP\ERP\Entity\Item::getSumItem();

        $stockto = \ZippyERP\ERP\Entity\Stock::getStock($store_id, $item->item_id, 1, true);
        $sc = new SubConto($this, 282, 0 - $saled);
        $sc->setStock($stockto->stock_id);
        $sc->setQuantity(0 - $saled * 1000); //цена  единицы  товара - 1 копейка.

        $sc->save();


        // НДС
        $nds = H::nds(true);
        Entry::AddEntry("702", "641", $saled * $nds, $this->document_id, $this->document_date);


        return true;
    }

}
