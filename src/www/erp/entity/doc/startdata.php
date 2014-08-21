<?php

namespace ZippyERP\ERP\Entity\Doc;

/**
 * Класс-сущность  документ ввод начальных остатков
 * 
 */
class StartData extends Document
{

    protected function init()
    {
        parent::init();
    }

    public function Execute()
    {
        $accarr = unserialize(base64_decode($this->headerdata['acc']));
        foreach ($accarr as $item) {

            if ($item->dc == '+') {
                $acc_d = $item->acc_id;
                $acc_c = -1;
            } else {
                $acc_c = $item->acc_id;
                $acc_d = -1;
            };
            \ZippyERP\ERP\Entity\Entry::AddEntry($acc_c, $acc_d, $item->acc_val* 100, $this->document_id, 'Начальные остатки');
        }

        $itemarr = unserialize(base64_decode($this->headerdata['item']));
        foreach ($itemarr as $item) {
            $stock = \ZippyERP\ERP\Entity\Stock::getStock($item->store_id, $item->item_id, $item->partion * 100, true);
            $stock->updateStock($item->qty,  $this->document_id, array());
        }
        $emparr = unserialize(base64_decode($this->headerdata['emp']));
        foreach ($emparr as $emp) {
            \ZippyERP\ERP\Entity\Employee::AddActivity($emp->employee_id, $emp->val* 100, $this->document_id);
        }
        $carr = unserialize(base64_decode($this->headerdata['c']));
        foreach ($carr as $c) {
            \ZippyERP\ERP\Entity\Customer::AddActivity($c->customer_id, $c->val* 100, $this->document_id);
        }
        $farr = unserialize(base64_decode($this->headerdata['f']));
        foreach ($farr as $f) {
            \ZippyERP\ERP\Entity\MoneyFund::AddActivity($f->id, $c->val* 100, $this->document_id);
        }
    }

    public function generateReport()
    {
        return " ";
    }

}

?>
