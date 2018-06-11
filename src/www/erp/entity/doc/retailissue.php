<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\ERP\Entity\Entry;
use \ZippyERP\ERP\Entity\SubConto;
use \ZippyERP\ERP\Entity\Store;
use \ZippyERP\ERP\Entity\MoneyFund;
use \ZippyERP\ERP\Helper as H;
use \ZippyERP\ERP\Util;

/**
 * Класс-сущность  документ розничная  накладая
 *
 */
class RetailIssue extends Document
{

    public function generateReport() {
        $detail = array();

        foreach ($this->detaildata as $value) {
            $detail[] = array(
                "code" => $value['item_code'],
                "tovar_name" => $value['itemname'],
                "measure" => $value['measure_name'],
                "quantity" => $value['quantity'] / 1000,
                "price" => H::fm($value['price']),
                "amount" => H::fm(($value['quantity'] / 1000) * $value['price'])
            );
        }

        $firm = \ZippyERP\System\System::getOptions("firmdetail");

        $header = array('date' => date('d.m.Y', $this->document_date),
            "firmname" => $firm['name'],
            "firmcode" => $firm['code'],
            "empname" => $this->headerdata["empname"],
            "customername" => $this->headerdata["customername"],
            "customer" => $this->headerdata["customer"],
            "document_number" => $this->document_number,
            "total" => H::fm($this->headerdata["total"]),
            "totalnds" => H::fm($this->headerdata["totalnds"]),
            "summa" => Util::ucfirst(Util::money2str($this->headerdata["total"] + $this->headerdata["nds"] / 100))
        );

        $report = new \ZippyERP\ERP\Report('retailissue.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute() {
        $ret = 0;
        $cost = 0;
        $sklad = Store::load($this->headerdata["store"])->store_type == Store::STORE_TYPE_OPT ? 281 : 282;
        foreach ($this->detaildata as $value) {
            $stock = \ZippyERP\ERP\Entity\Stock::load($value['stock_id']);
            $ret = $ret + $stock->price - $stock->partion;
            $cost = $cost + $stock->partion;

            $sc = new SubConto($this, $sklad, 0 - ($value['quantity'] / 1000) * $stock->price);
            $sc->setStock($stock->stock_id);
            $sc->setQuantity(0 - $value['quantity']);
            $sc->setExtCode($value['price'] - $value['partion']);   //Для АВС

            $sc->save();
        }

        // себестоимость реализации
        Entry::AddEntry("902", $sklad, $cost, $this->document_id, $this->document_date);




        if ($sklad == 282) {
            // списываем  наценку
            Entry::AddEntry("285", "282", $ret, $this->document_id, $this->document_date);
            $sc = new SubConto($this, 285, $ret);
            $sc->setExtCode($this->headerdata["store"]);
            $sc->save();
        }
        if ($sklad == 281 && $this->headerdata["ccard"] == 0) {
            // наличные
            $cash = MoneyFund::getCash();
            Entry::AddEntry("30", "702", $value['amount'], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 36, 0 - $this->headerdata["total"]);
            $sc->setCustomer($this->headerdata["customer"]);

            $sc->save();
        }

        if ($this->headerdata["ccard"] == 1) {
            $bank = MoneyFund::getBankAccount();
            Entry::AddEntry("31", "702", $value['amount'], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 31, $this->headerdata["total"]);
            $sc->setMoneyfund($bank->id);
            $sc->save();
        }


        if ($this->headerdata['emp'] > 0) {
            // в  подотчет
            Entry::AddEntry("372", "702", $this->amount, $this->document_id, $this->document_date);
            $sc = new SubConto($this, 272, $this->amount);
            $sc->setEmployee($this->headerdata['emp']);
            $sc->save();
        }


        //налоговые  обязательства
        if ($this->headerdata['totalnds'] > 0) {
            Entry::AddEntry("643", "702", $this->headerdata['totalnds'], $this->document_id, $this->document_date);
            $sc = new SubConto($this, 643, $this->headerdata["totalnds"]);
            $sc->setExtCode(\ZippyERP\ERP\Consts::TAX_NDS);
            $sc->save();
        }
        return true;
    }

    public function getRelationBased() {
        $list = array();
        $list['Warranty'] = 'Гарантійний талон';
        $list['ReturnRetailIssue'] = 'Накладна  на  повернення ';

        return $list;
    }

}
