<?php

namespace ZippyERP\API;

/**
 * Класс  для  работы  с  кассовыми  апаратами
 */
class Pos
{

    /**
     * /api/Pos/addDoc
     *
     *
     */
    public function addDoc()
    {
        $error = "";
        $xml = $_POST['data'];

        $doc = new  \ZippyERP\ERP\Entity\Doc\RegisterReceipt();

        // todo
        $doc->save();


        $xml = "<answer>";
        $xml .= "<status>OK</status>";


        $xml .= "</answer>";


        return $xml;
    }

}
