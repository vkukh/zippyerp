<?php

namespace ZippyERP\Shop\Pages;

//страница  для  загрузки  файла  
class ShowReport extends \Zippy\Html\WebPage
{

    public function __construct($type) {
        $html = \ZippyERP\System\Session::getSession()->sellreport;

        if (strlen($html) > 0) {

            if ($type == "print") {
                Header("Content-Type: text/html;charset=UTF-8");
                echo $html;
            }
            if ($type == "doc") {
                header("Content-type: application/vnd.ms-word");
                header("Content-Disposition: attachment;Filename=sellreport.doc");
                header("Content-Transfer-Encoding: binary");

                echo $html;
            }
            if ($type == "xls") {
                header("Content-type: application/vnd.ms-excel");
                header("Content-Disposition: attachment;Filename=sellreport.xls");
                header("Content-Transfer-Encoding: binary");
                //  echo '<meta http-equiv=Content-Type content="text/html; charset=UTF-8">'; 
                echo $html;
            }
        }
        die;
    }

}

?>
