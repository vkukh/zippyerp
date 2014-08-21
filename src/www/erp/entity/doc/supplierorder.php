<?php

namespace ZippyERP\ERP\Entity\Doc;

/**
 * Документ - заказ  поставщику
 */
class SupplierOrder extends Document
{

    public function generateReport()
    {
        return ' ';
    }

    protected function Execute()
    {


        return true;
        ;
    }

    //список состояний  для   выпадающих списков
    public static function getStatesList()
    {
        $list = array();
        $list[Document::STATE_NEW] = 'Новый';
        $list[Document::STATE_WA] = 'Ждет подтвержения';
        $list[Document::STATE_APPROVED] = 'Утвержден';
        $list[Document::STATE_WORK] = 'В работе';
        $list[Document::STATE_CLOSED] = 'Закрыт';

        return $list;
    }

}
