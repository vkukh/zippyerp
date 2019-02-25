<?php

namespace App\Entity;

/**
 * сущность для хранения аналитического  учета
 * субконто в  терминах 1С
 *
 * @table=entrylist
 * @keyfield=entry_id
 */
class Entry extends \ZCL\DB\Entity
{

    /**
     *
     *
     * @param mixed $document Ссылка  на  документ
     * @param mixed $amount Сумма. 
     * @param mixed $quantity количество
     */
    public function __construct($document_id, $acc_code, $amount = 0, $quantity = 0) {
        parent::__construct();

        if (strlen($acc_code) > 0) {
            $acc = \App\Entity\Account::load($acc_code);
            if ($acc == null)
                throw new \Exception("Не найден  счет '" . $acc_code . "'");
        }

        $this->document_id = $document_id;
        $this->amount = $amount;
        //отрицательное  если  счет по  кредиту
        $this->quantity = $quantity;
        $this->acc_code = strlen($acc_code) > 0 ? $acc_code : "";
    }

    protected function afterLoad() {
        $this->document_date = strtotime($this->document_date);
    }

    public function setStock($stock_id) {
        $this->stock_id = $stock_id;
    }

    public function setEmployee($employee_id) {
        $this->employee_id = $employee_id;
    }

    public function setCustomer($customer_id) {
        $this->customer_id = $customer_id;
    }

    public function setService($service_id) {
        $this->service_id = $service_id;
    }

    public function setAsset($ca_id) {
        $this->ca_id = $ca_id;
    }

    //типы  налогов, начислений  удержаний, прочая вспомагтельная  аналитика
    public function setExtCode($code) {
        if ($code > 0)
            $this->extcode = $code;
        else
            $this->extcode = 0;
    }

    /**
     * Получение  количества   по  комбинации измерений
     * неиспользуемые значения  заполняются  нулем
     *
     * @param mixed $date дата на  конец дня
     * @param mixed $acc синтетичкеский счет
     * @param mixed $stock товар (партия)
     * @param mixed $customer контрашент
     * @param mixed $emp сотрудник
     * @param mixed $mf денежный счет
     * @param mixed $asset необоротный актив
     * @param mixed $code универсальное поле
     */
    public static function getQuantity($date = 0, $acc_code = '', $stock = 0, $customer = 0, $emp = 0, $code = 0) {
        $conn = \ZDB\DB::getConnect();
        $where = "   1=1";
        if ($date > 0) {
            $where = $where . "   date(document_date) <= " . $conn->DBDate($date);
        }
        if (strlen($acc_code) > 0) {
            $where = $where . " and   acc_code = " . $conn->qstr($acc_code);
        }
        if ($emp > 0) {
            $where = $where . " and employee_id= " . $emp;
        }

        if ($code > 0) {
            $where = $where . " and extcode= " . $code;
        }

        if ($stock > 0) {
            $where = $where . " and stock_id= " . $stock;
        }
        if ($customer > 0) {
            $where = $where . " and customer_id= " . $customer;
        }
        $sql = " select coalesce(sum(quantity),0) AS quantity  from entrylist_view  where " . $where;
        return $conn->GetOne($sql);
    }

    /**
     * Получение  суммы   по  комбинации измерений
     * неиспользуемые значения  заполняются  нулем
     *
     * @param mixed $date дата на  конец дня
     * @param mixed $acc синтетичкеский счет
     * @param mixed $stock товар (партия)
     * @param mixed $customer контрашент
     * @param mixed $emp сотрудник
     * @param mixed $mf денежный счет
     * @param mixed $asset необоротный актив
     * @param mixed $code универсальное поле
     */
    public static function getAmount($date = 0, $acc_code = '', $stock = 0, $customer = 0, $emp = 0, $code = 0) {
        $conn = \ZDB\DB::getConnect();
        $where = "   1=1";
        if ($date > 0) {
            $where = $where . " and  date(document_date) <= " . $conn->DBDate($date);
        }
        if (strlen($acc_code) > 0) {
            $where = $where . " and   acc_code = " . $conn->qstr($acc_code);
        }

        if ($emp > 0) {
            $where = $where . " and employee_id= " . $emp;
        }

        if ($code > 0) {
            $where = $where . " and extcode= " . $code;
        }

        if ($stock > 0) {
            $where = $where . " and stock_id= " . $stock;
        }
        if ($customer > 0) {
            $where = $where . " and customer_id= " . $customer;
        }
        $sql = " select coalesce(sum(amount),0) AS quantity  from entrylist_view  where " . $where;
        return $conn->GetOne($sql);
    }

}
