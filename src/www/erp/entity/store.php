<?php

namespace ZippyERP\ERP\Entity;

/**
 * Клас-сущность  склад
 *
 * @table=erp_store
 * @keyfield=store_id
 */
class Store extends \ZCL\DB\Entity
{

    const STORE_TYPE_OPT = 1; //  Оптовый  склад
    const STORE_TYPE_RET = 2; //  Розничный  склад
    const STORE_TYPE_RET_SUM = 3; //  Магазин  с  суммовым  учетом
    

    protected function beforeDelete()
    {
        
        $conn = \ZDB\DB::getConnect();
        $sql = "  select count(*)  from  erp_store_stock where   store_id = {$this->store_id}";
        $cnt = $conn->GetOne($sql);
        return $cnt == 0;
    }

    /**
     * Получает список  бух. счетов к  которым  привязаны
     * ТМЦ хранищиеся  на  этом складе
     * @param mixed $store_id
     * @return mixed массив  для   комбобокса
     */
    public static function getAccounts($store_id)
    {
        $list = array();
        $conn = \ZDB\DB::getConnect();
        $sql = " select distinct sc.account_id, ap.acc_name  from erp_account_subconto sc join erp_account_plan ap on sc.account_id = ap.acc_code  where   stock_id in (select stock_id  from  erp_store_stock where   store_id = {$store_id}) order  by  ap.acc_name";
        $rs = $conn->Execute($sql);
        foreach ($rs as $row) {
            $list[$row['account_id']] = $row['acc_name'];
        }

        return $list;
    }

}
