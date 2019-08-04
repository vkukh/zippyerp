<?php

namespace App\Entity;

/**
 * Клас-сущность единица измерения
 *
 * @table=item_measures
 * @keyfield=measure_id
 */
class Measure extends \ZCL\DB\Entity
{

    protected function init() {
        $this->measure_id = 0;
    }

    
    protected function beforeDelete() {

       $cnt = \App\Entity\Item::findCnt("msr_id=" . $this->measure_id);
   
       return ($cnt > 0) ? "Нельзя удалить единицу с ТМЦ" : "";
    }    
}
