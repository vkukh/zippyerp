<?php

namespace ZippyERP\ERP\Entity\Doc;

/**
 * документ - заказ  клиента
 */
class CustomerOrder extends Document
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
        $list[Document::STATE_WORK] = 'В производстве';
        $list[Document::STATE_WP] = 'Ожидает оплату';
        $list[Document::STATE_INSHIPMENT] = 'Отгружен';

        return $list;
    }

}
