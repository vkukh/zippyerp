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

}
