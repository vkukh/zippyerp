<?php

namespace ZippyERP\ERP\Entity;

/**
 * Клас-сущность  денежные счета
 * 
 * @table=erp_moneyfunds
 * @keyfield=id
 */
class MoneyFund extends \ZCL\DB\Entity
{

    public static function AddActivity($mf_id, $amount, $document_id)
    {
        $conn = \ZCL\DB\DB::getConnect();
        $conn->Execute("insert into erp_moneyfunds_activity  (id_moneyfund,document_id,amount) values ({$mf_id},{$document_id},{$amount})");
    }

}

?>
