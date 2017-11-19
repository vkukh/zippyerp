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

    /**
     * Возвращает  кассу
     *
     */
    public static function getCash()
    {
        return self::getFirst('ftype=' . self::MF_CASH);
    }

    /**
    * Возвращает основной счет
    *     
    */
    public static function getBankAccount()
    {
        return self::getFirst('ftype=' . self::MF_BANK);
    }

}
