<?php

namespace ZippyERP\ERP\Entity;

use ZCL\DB\DB;

/**
 * Класс-сущность  бухгалтерский счет
 *
 * @table=erp_account_plan
 * @keyfield=acc_code
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
        $ret['parent'] = false;

        $children = Account::find("acc_pid=" . $this->acc_code);
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

            $start = $ret['startdt'] - $ret['startct'];
            $ret['startdt'] = $start > 0 ? $start : 0;
            $ret['startct'] = $start < 0 ? 0 - $start : 0;

            $end = $ret['enddt'] - $ret['endct'];
            $ret['enddt'] = $end > 0 ? $end : 0;
            $ret['endct'] = $end < 0 ? 0 - $end : 0;
            $ret['parent'] = true;
            return $ret;
        }


        $conn = DB::getConnect();


        //  начальн"":ое  сальдо  по  дебету
        $sql = "select coalesce(sum(amount),0) from  erp_account_entry where  acc_d={$this->acc_code} and date(document_date) < " . $conn->DBDate($from);
        $ret['startdt'] = $conn->GetOne($sql);
        //  начальное  сальдо  по  кредиту
        $sql = "select coalesce(sum(amount),0) from  erp_account_entry where  acc_c={$this->acc_code} and date(document_date) < " . $conn->DBDate($from);
        $ret['startct'] = $conn->GetOne($sql);

        // остаток  на   начало
        $start = $ret['startdt'] - $ret['startct'];
        $ret['startdt'] = $start > 0 ? $start : 0;
        $ret['startct'] = $start < 0 ? 0 - $start : 0;


        //оборот  по  дебету
        $sql = "select coalesce(sum(amount),0)  from  erp_account_entry where  acc_d= {$this->acc_code}  and date(document_date) >= " . $conn->DBDate($from) . " and date(document_date) <= " . $conn->DBDate($to);
        $ret['obdt'] = $conn->GetOne($sql);
        //оборот  по  кредиту
        $sql = "select coalesce(sum(amount),0)  from  erp_account_entry where  acc_c= {$this->acc_code}  and date(document_date) >= " . $conn->DBDate($from) . " and date(document_date) <= " . $conn->DBDate($to);
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
    public function getSaldo($date = null)
    {
        $saldo = 0;
        $children = Account::find("acc_pid=" . $this->acc_code);
        if (count($children) > 0) { // если   есть  субсчета
            foreach ($children as $child) {
                $saldo += $child->getSaldo($date);
            }
            return $saldo;
        }


        $conn = DB::getConnect();
        $sql = "select coalesce(sum(amount),0) from  erp_account_entry where  acc_d=" . $this->acc_code . $sqltag;

        if ($date > 0) {
            $sql = "select coalesce(sum(amount),0) from  erp_account_entry where  acc_d=" . $this->acc_code . "  and date(document_date) <= " . $conn->DBDate($date);
        }


        $deb = $conn->GetOne($sql);
        $sql = "select coalesce(sum(amount),0) from  erp_account_entry where  acc_c =" . $this->acc_code . $sqltag;
        if ($date > 0) {
            $sql = "select coalesce(sum(amount),0) from  erp_account_entry where  acc_c =" . $this->acc_code . "  and date(document_date) <= " . $conn->DBDate($date);
        }

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
    public static function getObBetweenAccount($acc_d, $acc_c, $from, $to, $tagd = 0, $tagc = 0)
    {

        //условие  для  тегов   по аналитике
        $sqltag = "";
        if ($tagd > 0)
            $sqltag = "  and  dtag = " . $tagd;
        if ($tagc > 0)
            $sqltag = $sqltag . "  and  ctag = " . $tagc;

        $conn = DB::getConnect();
        $sql = "select coalesce(sum(amount),0) from  erp_account_entry where  (acc_d= {$acc_d} or acc_d in(select acc_code from erp_account_plan p1 where  p1.acc_pid= {$acc_d}))  and (acc_c={$acc_c} or acc_c in(select acc_code from erp_account_plan p2 where  p2.acc_pid= {$acc_c})) and date(document_date) >= " . $conn->DBDate($from) . " and date(document_date) <= " . $conn->DBDate($to) . $sqltag;
        return $conn->GetOne($sql);
    }

    //возвращает  список с  кодом  и  названием
    public static function findArrayEx($where = '', $orderbyfield = null, $orderbydir = null)
    {
        $entitylist = self::find($where, $orderbyfield, $orderbydir);

        $list = array();
        foreach ($entitylist as $key => $value) {
            $list[$key] = sprintf(" %03d  %s", $value->acc_code, $value->acc_name);
        }

        return $list;
    }

}
