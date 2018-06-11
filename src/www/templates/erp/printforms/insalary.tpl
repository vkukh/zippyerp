<html>
    <body>
        <table class="ctable" border="0" cellspacing="0" cellpadding="2">
            <tr>
                <th width="30">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="130">&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
            </tr>


            <tr>
                <td style="font-weight: bolder;font-size: larger;" align="center" colspan="10" valign="middle">
                    <br><br>Нарахування зарплати № {{document_number}} від {{date}} <br><br>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td style="font-weight: bolder;  " align="left" colspan="9" valign="middle">
                    <br><br> За {{sdate}} <br><br>
                </td>
            </tr>

            <tr style="font-weight: bolder;">
                <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="30">№</th>
                <th colspan="2" width="230" style="border-top:1px #000 solid;border-bottom:1px #000 solid;">ПІБ</th>
                <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;">Зарплата</th>
                <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;">Відп.</th>
                <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;">Лік.</th>
                <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;">ЕСВ</th>
                <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;">ПДФО</th>
                <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="80">В. збір</th>
                <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="80">До виплати</th>
            </tr>
            {{#_detail}}
            <tr>
                <td align="right">{{no}}&nbsp;</td>
                <td colspan="2">{{emp_name}}</td>
                <td align="right">{{salary}}</td>
                <td align="right">{{vacation}}</td>
                <td align="right">{{sick}}</td>
                <td align="right">{{ecb}}</td>
                <td align="right">{{ndfl}}</td>
                <td align="right">{{mil}}</td>
                <td align="right">{{amount}}  </td>
            </tr>
            {{/_detail}}
            <tr style="font-weight: bolder;">
                <td style="border-top:1px #000 solid; " width="30"></td>
                <td colspan="2" style="border-top:1px #000 solid; "></td>
                <td style="border-top:1px #000 solid; " align="right">Всього:</td>
                <td style="border-top:1px #000 solid; "></td>
                <td style="border-top:1px #000 solid; "></td>
                <td style="border-top:1px #000 solid; " align="right"> {{totecb}}</td>
                <th style="border-top:1px #000 solid; " align="right">{{totndfl}}
                    </td>
                <td style="border-top:1px #000 solid;  " align="right" width="80">{{totmil}} </td>
                <td style="border-top:1px #000 solid;   " align="right" width="80">{{totamount}} </td>
            </tr>


        </table>
        <br> <br>
    </body>
</html>
