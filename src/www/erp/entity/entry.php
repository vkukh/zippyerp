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
        $this->created = time();
    }

    /**
     * Создает  и  записывает  бухгалтерскую  проводку
     * 
     * @param mixed $acc_d  id или  код дебетового  счета
     * @param mixed $acc_c  id или  код кредитового  счета
     * @param mixed $amount  Сумма (в копейках) 
     * @param mixed $document_id  документ-основание
     * @param mixed $comment    комментарий
     * @param mixed $dtag    тэг для  дебета 
     * @param mixed $ctag    тэг для  кредита
     * 
     * @return  возвращает  сообщение  об  ошибке  иначе   пустую  строку
     */
    public static function AddEntry($acc_d, $acc_c, $amount, $document_id, $comment = "", $dtag = 0, $ctag = 0)
    {


        $dt = Account::load($acc_d);

        if ($dt == null && $acc_d != -1) {
            return "Неверный   код  счета '{$acc_d}'";
        }

        $ct = Account::load($acc_c);
        if ($ct == null && $acc_c != -1) {
            return "Неверный код  счета '{$acc_c}'";
        }
        $entry = new Entry();

        $entry->acc_d = $acc_d == -1 ? 0 : $dt->acc_code; //дебетовый счет
        $entry->acc_c = $acc_c == -1 ? 0 : $ct->acc_code; //кредитовый счет
        $entry->amount = $amount;
        $entry->document_id = $document_id;
        $entry->comment = $comment;
        $entry->dtag = $dtag;
        $entry->ctag = $ctag;
        $entry->Save();

        return "";
    }

    protected function afterLoad()
    {
        $this->created = strtotime($this->created);
    }

}
