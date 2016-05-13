<?php

namespace ZippyERP\ERP\Entity\Doc;

use ZippyERP\ERP\Entity\Entry;
use ZippyERP\ERP\Entity\SubConto;
use ZippyERP\ERP\Helper as H;

/**
 * Класс-сущность  документ переоценка  в  суммовом  учете
 *
 */
class RevaluationRetSum extends Document
{

    public function generateReport()
    {


        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "summa" => H::fm($this->headerdata['summa']),
            "actual" => H::fm($this->headerdata['actual']),
            "storename" => $this->headerdata['storename']
        );

        switch ($this->headerdata['type']) {
            case 1:
                $header['typename'] = "Переоценка";
                break;
            case 2:
                $header['typename'] = "Списание недостач";
                break;
            case 3:
                $header['typename'] = "Оприходование излишков";
                break;
        }

        $report = new \ZippyERP\ERP\Report('revaluationretsum.tpl');

        $html = $report->generate($header);

        return $html;
    }

    public function Execute()
    {
        $diff = $this->headerdata['summa'] - $this->headerdata['actual'];;

        Entry::AddEntry("282", "285", $diff, $this->document_id, $this->document_date);
        $sc = new SubConto($this, 282, $diff);
        $sc->setStock($this->headerdata["stock_id"]);
        $sc->setQuantity($diff);

        $sc->save();
        $sc = new SubConto($this, 285, 0 - $diff);
        $sc->setExtCode($this->headerdata["store_id"]);

        $sc->save();


        return true;
    }

}
