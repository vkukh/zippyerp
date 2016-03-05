<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use \ZippyERP\ERP\Util;
use \ZippyERP\ERP\Entity\Employee;
use \ZippyERP\ERP\Entity\Entry;
use \ZippyERP\ERP\Entity\SubConto;
use \ZippyERP\ERP\Entity\MoneyFund;
use \ZippyERP\ERP\Helper as H;

/**
 *   документ начисление зарплаты
 *
 */
class InSalary extends Document
{

    public function generateReport()
    {


        $i = 1;
        $detail = array();

        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "emp_name" => $value['fullname'],
                "decamount" => H::fm($value['decamount']),
                "incamount" => H::fm($value['incamount']),
                "amount" => H::fm($value['amount'])
            );
        }

        $mlist = H::getMonth();

        $header = array('date' => date('d.m.Y', $this->document_date),
            "document_number" => $this->document_number,
            "sdate" => $mlist[$this->headerdata["month"]] . ' ' . $this->headerdata["year"]

        );

        $report = new \ZippyERP\ERP\Report('insalary.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {
        $tax =  System::getOptions("tax");
        
        $a66 = 0;
        $aexp = array(); // счета  затрат

        $a641 = 0;

        $fop = 0;

        foreach ($this->detaildata as $emp) {

            if($emp['incbasesalary'] >0){
                $sc = new SubConto($this, 66, 0-$emp['incbasesalary']);
                $a66 += $emp['incbasesalary'];
                
                if($tax['minsalary'] > $emp['incbasesalary'] && $emp['combined'] == 0){
                  $fop += $tax['minsalary']; //если не  совместитель и меньше 1378 брать 1378   
                }
                else {
                  $fop += $emp['incbasesalary']; 
                }
                
                
                $aexp[$emp['exptype']] += $emp['incbasesalary'];
                $sc->setEmployee($emp['employee_id']);

                $sc->save();

            }
            if($emp['dectaxfl'] >0){
                $sc = new SubConto($this, 66,$emp['dectaxfl']);
                $a66 -= $emp['dectaxfl'];
                $sc->setEmployee($emp['employee_id']);
                $a641 += $emp['dectaxfl'];
                $sc->save();

            }


        }


        if($fop >0){
          
          $ecb = $fop*$tax['ecbfot']/100;
          $sc = new SubConto($this, 65, 0-$ecb);
          $sc->setExtCode(\ZippyERP\ERP\Consts::TAX_ECB);
          $sc->save();

          Entry::AddEntry("91", "65", $ecb, $this->document_id, $this->document_date);

        }

        foreach($aexp as $expacc=>$value){
           Entry::AddEntry($expacc, "66", $value, $this->document_id, $this->document_date);
        }

        if($a641 >0){
          $sc = new SubConto($this, 641, 0-$a641);
          $sc->setExtCode(\ZippyERP\ERP\Consts::TAX_ECB);
          $sc->save();

          Entry::AddEntry("66", "641", $a641, $this->document_id, $this->document_date);


        }


        return true;
    }

    public function getRelationBased()
    {
        $list = array();
        $list['OutSalary'] = 'Выплата зарплаты';

        return $list;
    }

}
