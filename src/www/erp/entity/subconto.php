<?php

namespace ZippyERP\ERP\Entity;

/**
 * сущность для хранения аналитического  усчета
 *
 * @table=erp_account_subconto
 * @keyfield=subconto_id
 */
class SubConto extends \ZCL\DB\Entity
{

    /**
     *
     *
     * @param mixed $document Ссылка  на  документ
     * @param mixed $account_id Синтетический  счет
     * @param mixed $amount Сумма. Отрицательная если  счет  идет по  кредиту
     */
    public function __construct($document, $account_id, $amount)
    {
        parent::__construct();

        if ($document instanceof \ZippyERP\ERP\Entity\Doc\Document) {
            $this->document_id = $document->document_id;
            $this->document_date = $document->document_date;
        } else {
            throw new \ZippyERP\System\Exception("Не задан документ для субконто");
        }
        if ($account_id > 0) {
            $this->account_id = $account_id;
        } else {
            throw new \ZippyERP\System\Exception("Не задан счет для субконто");
        }
        if ($amount != 0) {
            $this->amount = $amount;
        } else {
            throw new \ZippyERP\System\Exception("Не задана ссумма для субконто");
        }
    }

    protected function afterLoad()
    {
        $this->document_date = strtotime($this->document_date);
    }

    public function setStock($stock_id)
    {
        $this->stock_id = $stock_id;
    }

    public function setEmployee($employee_id)
    {
        $this->employee_id = $employee_id;
    }

    public function setCustomer($customer_id)
    {
        $this->customer_id = $customer_id;
    }

    public function setMoneyfund($moneyfund_id)
    {
        $this->moneyfund_id = $moneyfund_id;
    }

    public function setAsset($item_id)
    {
        $this->asset_id = $item_id;
    }

    //типы  налогов, начислений  удержаний, прочая вспомагтельная  аналитика
    public function setExtCode($code)
    {
        if ($code > 0)
            $this->extcode = $code;
        else
            $this->extcode = 0;
    }

    //отрицательное  если  счет по  кредиту
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
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
    public static function getQuantity($date = 0, $acc = 0, $stock = 0, $customer = 0, $emp = 0, $mf = 0, $assets = 0, $code = 0)
    {
        $conn = \ZDB\DB::getConnect();
        $where = "   1=1";
        if ($date > 0) {
            $where = $where . "   date(document_date) <= " . $conn->DBDate($date);
        }

        if ($acc > 0) {
            $where = $where . " and account_id= " . $acc;
        }
        if ($emp > 0) {
            $where = $where . " and employee_id= " . $emp;
        }
        if ($mf > 0) {
            $where = $where . " and moneyfund_id= " . $mf;
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
        $sql = " select coalesce(sum(quantity),0) AS quantity  from erp_account_subconto  where " . $where;
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
    public static function getAmount($date = 0, $acc = 0, $stock = 0, $customer = 0, $emp = 0, $mf = 0, $assets = 0, $code = 0)
    {
        $conn = \ZDB\DB::getConnect();
        $where = "   1=1";
        if ($date > 0) {
            $where = $where . " and  date(document_date) <= " . $conn->DBDate($date);
        }
        if ($acc > 0) {
            $where = $where . " and account_id= " . $acc;
        }
        if ($emp > 0) {
            $where = $where . " and employee_id= " . $emp;
        }
        if ($mf > 0) {
            $where = $where . " and moneyfund_id= " . $mf;
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
        $sql = " select coalesce(sum(amount),0) AS quantity  from erp_account_subconto  where " . $where;
        return $conn->GetOne($sql);
    }

}
