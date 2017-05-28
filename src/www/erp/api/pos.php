<?php

namespace ZippyERP\ERP\API;

/**
 * Класс  для  работы  с  кассовыми  апаратами
 */
class Pos  extends \Zippy\RestFul   
{

    public function post($xml)
    {
        $error = "";
   

        $doc = new  \ZippyERP\ERP\Entity\Doc\RegisterReceipt();

        // todo
        $doc->save();


      
        $this->OKAnswer();
    }

}
