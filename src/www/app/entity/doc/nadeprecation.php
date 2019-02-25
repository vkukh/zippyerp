<?php

namespace App\Entity\Doc;

use Carbon\Carbon;
use App\Entity\CAsset;
use App\Entity\Entry;
use App\Entity\AccountEntry;

/**
 * Класс-сущность  документ начсисление амортизации
 *
 */
class NADeprecation extends Document
{

    public function generateReport() {


        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "tax" => $this->headerdata['tax']
        );

        $report = new \App\Report('nadeprecation.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute() {
        $date = new Carbon();
        $date->setTimestamp($this->document_date);
        $beginmonth = $date->startOfMonth()->getTimestamp();  //месяц начислниея

        $calist = CAsset::find("   ca_id in (SELECT ca_id  FROM `entrylist_view` WHERE  acc_code in('104','12') and ca_id >0 group by ca_id  having sum(amount) >0 ) ");

        foreach ($calist as $ca) {
            $amount = 0;
            $a13 = '131';
            if ($ca->acc_code == '12')
                $a13 = '133';
            if (in_array($ca->acc_code, array(104, 106)))
                $a13 = '131';;
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
                    AccountEntry::AddEntry($ca->expenses, $a13, $amount, $this->document_id);
                    $sc = new Entry($this->document_id, $a13, 0 - $amount, 1);
                    $sc->setAsset($ca->ca_id);
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
