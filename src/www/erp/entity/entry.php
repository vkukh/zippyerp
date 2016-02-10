<?php

namespace ZippyERP\ERP\Entity;

/**
 * Класс-сущность  бухгалтерская   проводка
 *
 * @table=erp_account_entry
 * @view=erp_account_entry_view
 * @keyfield=entry_id
 */
class Entry extends \ZCL\DB\Entity
{

    protected function init()
    {
        $this->entry_id = 0;
    }

    /**
     * Создает  и  записывает  бухгалтерскую  проводку
     *
     * @param mixed $acc_d     код дебетового  счета или 0
     * @param mixed $acc_c     код кредитового  счета или 0
     * @param mixed $amount  Сумма (в копейках)  Отрицательное  значение выполняет сторнирование.
     * @param mixed $document_id  документ-основание
     * @param mixed $document_date  дата  документа
     *
     * @return  возвращает  сообщение  об  ошибке  иначе   пустую  строку
     */
    public static function AddEntry($acc_d, $acc_c, $amount, $document_id, $document_date)
    {
        if ($amount == 0)
            return;

        $dt = Account::load($acc_d);

        if ($dt == false && $acc_d > 0) {
            return "Неверный   код  счета '{$acc_d}'";
        }

        $ct = Account::load($acc_c);
        if ($ct == false && $acc_c > 0) {
            return "Неверный код  счета '{$acc_c}'";
        }
        if ($ct == false && $dt == false) {
            return "Должен быть задан хотя бы  один  счет'";
        }

        $entry = new Entry();

        $entry->acc_d = $dt == false ? 0 : $dt->acc_code; //дебетовый счет
        $entry->acc_c = $ct == false ? 0 : $ct->acc_code; //кредитовый счет
        $entry->amount = $amount;
        $entry->document_id = $document_id;
        $entry->document_date = $document_date;

        $entry->Save();

        return "";
    }

    protected function afterLoad()
    {
        $this->document_date = strtotime($this->document_date);
    }

}
