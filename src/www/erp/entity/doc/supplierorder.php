<?php

namespace ZippyERP\ERP\Entity\Doc;

/**
 * Документ - заказ  поставщику
 */
class SupplierOrder extends Document
{

    protected function init()
    {
        parent::init();
        $this->intattr1 = 0; //поставщик
        $this->intattr2 = 0; // оплата
    }

    public function generateReport()
    {
        return '';
    }

    public function Execute()
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

    public function getRelationBased()
    {
        $list = array();
        $list['PurchaseInvoice'] = 'Счет входящий';
        return $list;
    }

}
