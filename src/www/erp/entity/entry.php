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
         * 
         * @return  возвращает  сообщение  об  ошибке  иначе   пустую  строку
         */
        public static function AddEntry($acc_d, $acc_c, $amount, $document_id, $comment = "")
        {

                $dt = null;
                if ($acc_d > 10000) {  // используется  id
                        $dt = Account::load($acc_d);
                } else {
                        // используется код  счета
                        $list = Account::find("acc_code=" . $acc_d);
                        if (count($list) == 1) {
                                $dt = array_pop($list);
                        }
                }
                if ($dt == null) {
                        return "Неверный   код  счета '{$acc_d}'";
                }
                $ct = null;
                if ($acc_c > 10000) {  // используется  id
                        $ct = Account::load($acc_c);
                } else {
                        $list = Account::find("acc_code=" . $acc_c);
                        if (count($list) == 1) {
                                $ct = array_pop($list);
                        }
                }
                if ($dt == null) {
                        return "Неверный код  счета '{$acc_c}'";
                }
                $entry = new Entry();

                $entry->acc_d = $dt->acc_id; //дебетовый счет
                $entry->acc_c = $ct->acc_id; //кредитовый счет
                $entry->amount = $amount;
                $entry->document_id = $document_id;
                $entry->comment = $comment;
                $entry->Save();

                return "";
        }

        protected function afterLoad()
        {
                $this->created = strtotime($this->created);
        }

}