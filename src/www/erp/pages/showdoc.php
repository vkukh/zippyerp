<?php

namespace ZippyERP\ERP\Pages;

//страница  для  загрузки  файла экпорта 
class ShowDoc extends \Zippy\Html\WebPage
{

    public function __construct($type, $p1 = "report")
    {
        $html = \ZippyERP\System\Session::getSession()->printform;
        $xml = \ZippyERP\System\Session::getSession()->xmlform;

        if (strlen($html) > 0) {

            $filename = $p1;

            if ($type == "print") {
                Header("Content-Type: text/html;charset=UTF-8");
                echo $html;
            }
            if ($type == "doc") {
                header("Content-type: application/vnd.ms-word");
                header("Content-Disposition: attachment;Filename={$filename}.doc");
                header("Content-Transfer-Encoding: binary");

                echo $html;
            }
            if ($type == "xls") {
                header("Content-type: application/vnd.ms-excel");
                header("Content-Disposition: attachment;Filename={$filename}.xls");
                header("Content-Transfer-Encoding: binary");
                //echo '<meta http-equiv=Content-Type content="text/html; charset=windows-1251">'; 
                echo $html;
            }
            if ($type == "html") {
                header("Content-type: text/plain");
                header("Content-Disposition: attachment;Filename={$filename}.html");
                header("Content-Transfer-Encoding: binary");

                echo $html;
            }
            if ($type == "xml") {
                header("Content-type: text/xml");
                header("Content-Disposition: attachment;Filename={$filename}");
                header("Content-Transfer-Encoding: binary");

                echo $xml;
            }
            /*  if ($type == "pdf") {

              $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
              $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
              $pdf->SetFont('freesans', '', 12);
              $pdf->setPrintHeader(false);
              $pdf->AddPage();
              $pdf->writeHTML($html, true, false, true, false, 'J');
              $pdf->Output("{$filename}.pdf", 'D');
              } */
        }


        if ($type == "metaie") { // экспорт  файлов  метаобьекта
            if ($p1 > 0) {
                $item = \ZippyERP\ERP\Entity\MetaData::load($p1);
                $filename = $item->meta_name . ".zip";


                header("Content-type: application/zip");
                header("Content-Disposition: attachment;Filename={$filename}");
                header("Content-Transfer-Encoding: binary");

                echo $zip;
            }
        }
        die;
    }

}
