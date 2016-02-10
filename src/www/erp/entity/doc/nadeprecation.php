<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use \ZippyERP\ERP\Util;
use \ZippyERP\ERP\Helper as H;
use \ZippyERP\ERP\Entity\Entry;
use \ZippyERP\ERP\Entity\CapitalAsset;
use \ZippyERP\ERP\Entity\SubConto;
use \Carbon\Carbon;

/**
 * Класс-сущность  документ начсисление амортизации
 *
 */
class NADeprecation extends Document
{

    public function generateReport()
    {


        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "tax" => $this->headerdata['tax']
        );

        $report = new \ZippyERP\ERP\Report('nadeprecation.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute()
    {
        $date = new Carbon();
        $date->setTimestamp($this->document_date);
        $beginmonth = $date->startOfMonth()->getTimestamp();  //месяц начислниея

        $calist = CapitalAsset::find('item_type= ' . \ZippyERP\ERP\Entity\Item::ITEM_TYPE_OS . ' and item_id in (SELECT asset_id  FROM `erp_account_subconto` WHERE  account_id in(10,12) and asset_id >0 group by asset_id  having sum(amount) >0 )');

        foreach ($calist as $ca) {

            $date = new Carbon();
            $date->setTimestamp($ca->datemaint);
            $beginmaint = $date->startOfMonth()->getTimestamp();  //месяц ввода в  эксплуатацию

            if ($beginmaint > $beginmonth) {
                continue; //начисляем со  следующего месяца
            }


            $d = $ca->getDeprecationValue(); //уже начисленый  износ


            if ($this->headerdata['tax'] == 0) {
                if ($ca->depreciation == 1 && $ca->term > 0) { //линейный метод
                    $amount = (int) round(($ca->value - $ca->cancelvalue) / $ca->term);
                }


                if ($d + $amount + $ca->cancelvalue > $ca->value) {
                    $amount = $ca->value - $ca->cancelvalue - $d;  // последнее  начисление  уравниваем  по  оликвидационной стоимости
                }


                if ($amount > 0) {
                    Entry::AddEntry($ca->typeos, "13", $amount, $this->document_id, $this->document_date);
                    $sc = new SubConto($this, 13, $amount);
                    $sc->setAsset($ca->item_id);
                    $sc->save();
                }
            } else {    // налоговый учет
                if ($ca->group > 0) { //производственное
                    //todo
                }
            }
        }


        return true;
    }

}
