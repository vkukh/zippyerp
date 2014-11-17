<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">

    <head>

    </head>

    <body>

        <table  border=0 cellpadding=0 cellspacing=0 width="510" style="font-size:12px;">


            <tr height=21 style='height:15.75pt'>
                <td height=21 style='height:15.75pt'></td>
                <td  colspan=6 ><b>ПЛАТІЖНЕ ДОРУЧЕННЯ № {document_number}</b></td>

                <td></td>
                <td  align="right" width="75px" >410001</td>

            </tr>
            <tr height=20 style='height:15.0pt'>
                <td height=20  style='height:15.0pt'></td>
                <td  colspan=4 >від  <b>{document_date}</b></td>
                <td colspan=3 ></td>
                <td></td>
            </tr>
            <tr height=15 style='height:11.85pt'>
                <td height=15 colspan=5  style='height:11.85pt;'></td>
                <td></td>
                <td ></td>
                <td  colspan=2 >Одержано банком</td>

            </tr>
            <tr height=20 style='height:15.0pt'>
                <td height=20 colspan=4 ='height:15.0pt;'></td>
                <td colspan=2 > </td>


                <td colspan=5> від  «___»__________20___р.</td>

            </tr>
            <tr height=20 style='height:15.0pt'>
                <td height=20 >Платник</td>
                <td colspan=3> <b>{myname}</b></td>
                <td colspan=5 ></td>

            </tr>
            <tr height=20 style='height:15.0pt'>
                <td height=20  style='height:15.0pt'>Код</td>
                <td colspan=2 style="padding:1px;border: 1px solid #000"><b>{mycode}</b></td>
                <td colspan=6 > </td>

            </tr>
            <tr height=20 style='height:15.0pt'>
                <td height=20  colspan=2 style='height:15.0pt;'>Банк платника</td>
                <td colspan=7  > </td>

            </tr>
            <tr height=20 style='height:15.0pt'>
                <td height=20 colspan=3  style='height:15.0pt;'></td>
                <td></td>
                <td ></td>
                <td >Код банку</td>
                <td  colspan=2 >ДЕБЕТ рах. №</td>
                <td >СУМА</td>

            </tr>
            <tr height=20 style='height:15.0pt'>
                <td colspan="3" height=20  style='border-bottom: 1px solid #000;height:15.0pt'><b>{mybank}</b></td>

                <td >&nbsp;</td>
                <td >&nbsp; </td>
                <td  style="padding:1px;border: 1px solid #000"><b>{mybankcode}</b></td>
                <td  colspan="2" style="padding:1px;border: 1px solid #000"><b>{mybankaccount}</b></td>
                <td  style="border-top: 1px solid #000;border-right: 1px solid #000" align="center"><b>{amount}</b></td>


            </tr>

            <tr  style='height:35.0pt'>
                <td   style='height:35.0pt' valign="bottom">Отримувач</td>
                <td colspan=4   valign="bottom"><b>{cname}</b></td>
                <td >&nbsp;</td>
                <td colspan=2 style="border-right: 1px solid #000">&nbsp;</td>
                <td style="border-right: 1px solid #000">&nbsp;</td>

            </tr>
            <tr height=20 style='height:15.0pt'>
                <td height=20  style='height:15.0pt' >Код </td>
                <td colspan=2 style="padding:1px;border: 1px solid #000"><b>{ccode}</b></td>
                <td colspan=5  style="border-right: 1px solid #000">&nbsp;</td>
                <td style="border-right: 1px solid #000">&nbsp;</td>

            </tr>
            <tr height=20 style='height:15.0pt'>
                <td height=20  colspan=2 style='height:15.0pt;'>Банк  отримувача</td>
                <td ></td>
                <td colspan=2  ></td>
                <td></td>
                <td  colspan=2 style="border-right: 1px solid #000">КРЕДИТ рах. №</td>
                <td style="border-right: 1px solid #000">&nbsp;</td>

            </tr>
            <tr height=20 style='height:15.0pt'>
                <td height=20 colspan=3  style='height:15.0pt;'></td>
                <td></td>
                <td ></td>
                <td >Код банку</td>
                <td colspan=2 style="padding:1px;border: 1px solid #000"><b>{cbankaccount}</b></td>

                <td  style="border-right: 1px solid #000">&nbsp;</td>

            </tr>
            <tr height=20 style='height:15.0pt'>
                <td colspan="3" height=20  style='border-bottom: 1px solid #000;height:15.0pt'><b>{cbank}</b></td>

                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td style="padding:1px;border: 1px solid #000"><b>{cbankcode}</b></td>
                <td colspan="2" style="border: 1px solid #000">&nbsp;</td>

                <td  style="border-bottom: 1px solid #000;border-right: 1px solid #000">&nbsp;</td>

            </tr>
            <tr height=20 style='height:15.0pt'>
                <td height=20  colspan=2 style='height:15.0pt;'>Сума  (словами)</td>
                <td colspan=7  ></td>

            </tr>
            <tr height=20 style='height:15.0pt'>
                <td colspan="7" style="border-bottom: 1px solid #000" height=20  style='height:15.0pt'><b>{amountstr}</b></td>

                <td  ></td>
                <td></td>
            </tr>
            <tr height=20 style='height:15.0pt'>
                <td height=20  colspan=2 style='height:15.0pt;'>Призначення   платежу</td>
                <td colspan=3 ></td>
                <td >&nbsp;</td>
                <td colspan=2 >&nbsp;</td>
                <td style="border: 1px solid #000">&nbsp;</td>

            </tr>
            <tr height=10 style='height:7.5pt'>
                <td height=10 colspan=5  style='height:7.5pt;'><b>{notes}</b></td>
                <td >&nbsp;</td>
                <td colspan=3 >&nbsp;</td>

            </tr>
            <tr height=20 style='height:15.0pt'>
                <td height=20  style='height:15.0pt'>ДР</td>
                <td style="border: 1px solid #000">&nbsp;</td>
                <td colspan=3 ></td>
                <td ></td>
                <td colspan=3 ></td>

            </tr>
            <tr height=16 style='height:12.6pt'>
                <td height=16 colspan=2  style='height:12.6pt;'></td>
                <td ></td>
                <td colspan=6 ></td>

            </tr>
            <tr height=20 style='height:15.0pt'>
                <td height=20 class=xl68 style='height:15.0pt'>М.П.</td>
                <td >Підписи </td>
                <td colspan=3 ></td>
                <td></td>
                <td ></td>
                <td  colspan=2 >Проведено банком</td>

            </tr>
            <tr height=20 style='height:15.0pt'>
                <td height=20 colspan=2  style='height:15.0pt;'></td>
                <td colspan=4 >&nbsp;</td>

                <td colspan=3  >від  «___»__________20___р.</td>


            </tr>
            <tr height=17 style='height:12.75pt'>
                <td height=17 colspan=8  style='height:12.75pt;'>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr height=20 style='height:15.0pt'>
                <td height=20 colspan=7  style='height:15.0pt;'></td>

                <td  colspan=2 >Підпис банку</td>

            </tr>




        </table>

    </body>

</html>