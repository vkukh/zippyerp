<?php

namespace ZippyERP\ERP\Entity;

use ZCL\DB\DB;

/**
 * Класс-сущность  бухгалтерский счет
 * 
 * @table=erp_account_plan
 * @keyfield=acc_id
 */
class Account extends \ZCL\DB\Entity
{

    /**
     * Получение  остатков  и  оборотов за   период
     * 
     * @param mixed $from
     * @param mixed $to
     */
    public function getSaldoAndOb($from, $to)
    {

        $ret = array('startdt' => 0, 'startct' => 0, 'obdt' => 0, 'obct' => 0, 'enddt' => 0, 'endct' => 0);

        $children = Account::find("acc_pid=" . $this->acc_id);
        if (count($children) > 0) { // если   есть  субсчета
            foreach ($children as $child) {
                $data = $child->getSaldoAndOb($from, $to);
                $ret['startdt'] += $data['startdt'];
                $ret['startct'] += $data['startct'];
                $ret['obdt'] += $data['obdt'];
                $ret['obct'] += $data['obct'];
                $ret['enddt'] += $data['enddt'];
                $ret['endct'] += $data['endct'];
            }
            $ret['parent'] = true;
            return $ret;
        }


        $conn = DB::getConnect();

        //  начальное  сальдо  по  дебету
        $sql = "select coalesce(sum(amount),0) from  erp_account_entry_view where  acc_d={$this->acc_id} and date(created) < " . $conn->DBDate($from);
        $ret['startdt'] = $conn->GetOne($sql);
        //  начальное  сальдо  по  кредиту
        $sql = "select coalesce(sum(amount),0) from  erp_account_entry_view where  acc_c={$this->acc_id} and date(created) < " . $conn->DBDate($from);
        $ret['startct'] = $conn->GetOne($sql);


        //оборот  по  дебету
        $sql = "select coalesce(sum(amount),0)  from  erp_account_entry_view where  acc_d= {$this->acc_id}  and date(created) >= " . $conn->DBDate($from) . " and date(created) <= " . $conn->DBDate($to);
        $ret['obdt'] = $conn->GetOne($sql);
        //оборот  по  кредиту
        $sql = "select coalesce(sum(amount),0)  from  erp_account_entry_view where  acc_c= {$this->acc_id}  and date(created) >= " . $conn->DBDate($from) . " and date(created) <= " . $conn->DBDate($to);
        $ret['obct'] = $conn->GetOne($sql);

        // остаток  на   конец
        $end = $ret['startdt'] - $ret['startct'] + $ret['obdt'] - $ret['obct'];
        $ret['enddt'] = $end > 0 ? $end : 0;
        $ret['endct'] = $end < 0 ? 0 - $end : 0;

        return $ret;
    }

    /**
     * Возвращает сальдо  с   учетом субсчетов
     * Положительное  значение  сальдо  по  дебету,  отрицательное - по  кредиту.
     * 
     */
    public function getSaldo()
    {
        $saldo = 0;
        $children = Account::find("acc_pid=" . $this->acc_id);
        if (count($children) > 0) { // если   есть  субсчета
            foreach ($children as $child) {
                $saldo += $child->getSaldo();
            }
            return $saldo;
        }


        $conn = DB::getConnect();
        $sql = "select coalesce(sum(amount),0) from  erp_account_entry where  acc_d=" . $this->acc_id;
        ;
        $deb = $conn->GetOne($sql);
        $sql = "select coalesce(sum(amount),0) from  erp_account_entry where  acc_c=" . $this->acc_id;

        $cr = $conn->GetOne($sql);

        return $deb - $cr;
    }

    /**
     * обороты между счетами  за   период
     * 
     * @param mixed $acc_d
     * @param mixed $acc_c
     * @param mixed $from
     * @param mixed $to
     */
    public static function getObBetweenAccount($acc_d, $acc_c, $from, $to)
    {
        $conn = DB::getConnect();
        $sql = "select coalesce(sum(amount),0) from  erp_account_entry_view where  (acc_d= {$acc_d} or acc_d in(select acc_id from erp_account_plan p1 where  p1.acc_pid= {$acc_d}))  and (acc_c={$acc_c} or acc_c in(select acc_id from erp_account_plan p2 where  p2.acc_pid= {$acc_c})) and date(created) >= " . $conn->DBDate($from) . " and date(created) <= " . $conn->DBDate($to);
        return $conn->GetOne($sql);
    }

}
