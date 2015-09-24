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
        return false;
    }

    /**
     * Получает список  бух. счетов к  которым  привязаны
     * ТМЦ хранищиеся  на  этом складе
     * @param mixed $store_id
     * @return массив  для   комбобокса
     */
    public static function getAccounts($store_id)
    {
        $list = array();
        $conn = \ZCL\DB\DB::getConnect();
        $sql = " select distinct sc.account_id, ap.acc_name  from erp_account_subconto sc join erp_account_plan ap on sc.account_id = ap.acc_code  where   stock_id in (select stock_id  from  erp_store_stock where   store_id = {$store_id}) order  by  ap.acc_name";
        $rs = $conn->Execute($sql);
        foreach ($rs as $row) {
            $list[$row['account_id']] = $row['acc_name'];
        }

        return $list;
    }

}
