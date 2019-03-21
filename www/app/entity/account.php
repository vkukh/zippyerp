<?php

namespace App\Entity;

use ZCL\DB\DB;

/**
 * Класс-сущность  бухгалтерский счет
 *
 * @table=account_plan
 * @keyfield=acc_code
 */
class Account extends \ZCL\DB\Entity
{

    public function getChildren() {
        $children = Account::find("pcode =" . Account::qstr($this->acc_code));
        return $children;
    }

    /**
     * Получение  остатков  и  оборотов за   период
     *
     * @param mixed $from
     * @param mixed $to
     */
    public function getSaldoAndOb($from, $to) {

        $ret = array('startdt' => 0, 'startct' => 0, 'obdt' => 0, 'obct' => 0, 'enddt' => 0, 'endct' => 0);
        $ret['parent'] = false;

        $children = $this->getChildren();
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
        $code = $conn->qstr($this->acc_code);

        //  начальное  сальдо  по  дебету
        $sql = "select coalesce(sum(amount),0) from   account_entry_view where  acc_d={$code} and date(document_date) < " . $conn->DBDate($from);
        $ret['startdt'] = $conn->GetOne($sql);
        //  начальное  сальдо  по  кредиту
        $sql = "select coalesce(sum(amount),0) from   account_entry_view where  acc_c={$code} and date(document_date) < " . $conn->DBDate($from);
        $ret['startct'] = $conn->GetOne($sql);

        // остаток  на   начало
        $start = $ret['startdt'] - $ret['startct'];
        $ret['startdt'] = $start > 0 ? $start : 0;
        $ret['startct'] = $start < 0 ? 0 - $start : 0;


        //оборот  по  дебету
        $sql = "select coalesce(sum(amount),0)  from   account_entry_view where  acc_d= {$code}  and date(document_date) >= " . $conn->DBDate($from) . " and date(document_date) <= " . $conn->DBDate($to);
        $ret['obdt'] = $conn->GetOne($sql);
        //оборот  по  кредиту
        $sql = "select coalesce(sum(amount),0)  from   account_entry_view where  acc_c= {$code}  and date(document_date) >= " . $conn->DBDate($from) . " and date(document_date) <= " . $conn->DBDate($to);
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
     * @param mixed $date На дату
     */
    public function getSaldo($date = null) {
        $saldo = 0;
        $children = $this->getChildren();
        if (count($children) > 0) { // если   есть  субсчета
            foreach ($children as $child) {
                $saldo += $child->getSaldo($date);
            }
            return $saldo;
        }


        $conn = DB::getConnect();
        $acc_code = $conn->qstr($this->acc_code);

        $sql = "select coalesce(sum(amount),0) from   account_entry_view where  acc_d=" . $acc_code;

        if ($date > 0) {
            $sql = "select coalesce(sum(amount),0) from   account_entry_view where  acc_d=" . $acc_code . "  and date(document_date) <= " . $conn->DBDate($date);
        }

        $deb = $conn->GetOne($sql);
        $sql = "select coalesce(sum(amount),0) from   account_entry_view where  acc_c =" . $acc_code;
        if ($date > 0) {
            $sql = "select coalesce(sum(amount),0) from   account_entry_view where  acc_c =" . $acc_code . "  and date(document_date) <= " . $conn->DBDate($date);
        }

        $cr = $conn->GetOne($sql);

        return $deb - $cr;
    }

    /**
     * Дебетовое  сальдо
     *
     * @param mixed $date
     */
    public function getSaldoD($date = null) {
        $a = $this->getSaldo($date);
        return $a > 0 ? $a : 0;
    }

    /**
     * Кредитовое  сальдо
     *
     * @param mixed $date
     */
    public function getSaldoC($date = null) {
        $a = $this->getSaldo($date);
        return $a < 0 ? 0 - $a : 0;
    }

    /**
     * обороты между счетами  за   период
     *
     * @param mixed $acc_d
     * @param mixed $acc_c
     * @param mixed $from
     * @param mixed $to
     */
    public static function getObBetweenAccount($acc_d, $acc_c, $from, $to) {


        $conn = DB::getConnect();
        $acc_d = $conn->qstr($acc_d . '%');
        $acc_c = $conn->qstr($acc_c . '%');

        $sql = "select coalesce(sum(amount),0) from   account_entry_view where  acc_d like {$acc_d} and  acc_c like {$acc_c}   and date(document_date) >= " . $conn->DBDate($from) . " and date(document_date) <= " . $conn->DBDate($to);

        return $conn->GetOne($sql);
    }

    //возвращает  список с  кодом  и  названием
    public static function findArrayEx($where = '') {

        $entitylist = self::find($where, "acc_code ");

        $list = array();
        foreach ($entitylist as $key => $value) {
            $list[$key] = sprintf(" %4s  %s", $value->acc_code, $value->acc_name);
        }

        return $list;
    }

    public static function remove($acc_code) {
        $conn = DB::getConnect();
        $acc_code = $conn->qstr($acc_code);

        $sql = "delete from account_plan where acc_code = {$acc_code}";
        $conn->Execute($sql);
    }

    public static function create($acc_code, $acc_name, $code = "") {
        $conn = DB::getConnect();
        $acc_code = $conn->qstr($acc_code);
        $acc_name = $conn->qstr($acc_name);
        $code = $conn->qstr($code);
        $sql = "insert into account_plan (acc_code,acc_name,pcode) values ({$acc_code},{$acc_name},{$code});";
        $conn->Execute($sql);
    }

    //возвращает  список с  кодом  и  названием  для проводки
    public static function findArrayEntry() {

        $entitylist = self::find("acc_code not in (select pcode from account_plan where pcode is   not NULL)", "acc_code ");

        $list = array();
        foreach ($entitylist as $key => $value) {
            $list[$key] = sprintf(" %4s  %s", $value->acc_code, $value->acc_name);
        }

        return $list;
    }

}
