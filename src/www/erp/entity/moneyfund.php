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

    const MF_CASH = 0; //касса
    const MF_BANK = 1; //основной  банковский  счет
    const MF_BANK_ADD = 2; //дополнительный банковский счет

    /*
      public static function AddActivity($mf_id, $amount, $document_id)
      {
      $conn = \ZCL\DB\DB::getConnect();
      $conn->Execute("insert into erp_moneyfunds_activity  (id_moneyfund,document_id,amount) values ({$mf_id},{$document_id},{$amount})");
      }
     */

    /**
     * Возвращает  кассу
     * 
     */
    public static function getCash()
    {
        return self::getFirst('ftype=' . self::MF_CASH);
    }

}
