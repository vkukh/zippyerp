<?php

namespace App\Entity;

/**
 * Класс-сущность  бухгалтерская   проводка
 *
 * @table=account_entry
 * @view=account_entry_view
 * @keyfield=entry_id
 */
class AccountEntry extends \ZCL\DB\Entity
{

    protected function init() {
        $this->entry_id = 0;
    }

    /**
     * Создает  и  записывает  бухгалтерскую  проводку
     *
     * @param mixed $acc_d код дебетового  счета или null
     * @param mixed $acc_c код кредитового  счета или null
     * @param mixed $amount Сумма (в копейках)  Отрицательное  значение выполняет сторнирование.
     * @param mixed $document_id документ-основание
     *
     */
    public static function AddEntry($acc_d, $acc_c, $amount, $document_id) {

        if (strlen($acc_d) > 0) {
            $acc = \App\Entity\Account::load($acc_d);
            if ($acc == null)
                throw new \Exception("Не найден  счет '" . $acc_d . "'");
        }
        if (strlen($acc_c) > 0) {
            $acc = \App\Entity\Account::load($acc_c);
            if ($acc == null)
                throw new \Exception("Не найден  счет '" . $acc_c . "'");
        }




        if ($amount != 0) {

            $entry = new AccountEntry();

            $entry->acc_d = $acc_d; //дебетовый счет
            $entry->acc_c = $acc_c; //кредитовый счет
            $entry->amount = $amount;
            $entry->document_id = $document_id;

            $entry->save();
        }
        return "";
    }

    protected function afterLoad() {
        $this->document_date = strtotime($this->document_date);
    }

}
