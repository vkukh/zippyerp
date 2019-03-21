<?php

namespace App\Entity;

/**
 * Клас-сущность  банк
 *
 * @table=banklist
 * @keyfield=bank_id
 */
class Bank extends \ZCL\DB\Entity
{

    protected function beforeSave() {
        parent::beforeSave();
        //упаковываем  данные в detail
        $this->detail = "<detail><mfo>{$this->mfo}</mfo>";
        $this->detail .= "</detail>";

        return true;
    }

    protected function afterLoad() {
        //распаковываем  данные из detail
        $xml = simplexml_load_string($this->detail);
        $this->mfo = (string) ($xml->mfo[0]);

        parent::afterLoad();
    }

}
