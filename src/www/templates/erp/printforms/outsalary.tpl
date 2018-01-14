<html>
<body>
<table class="ctable" border="0" cellspacing="0" cellpadding="2">
    <tr>
        <th width="30">&nbsp;</th>
        <th width="100">&nbsp;</th>
        <th width="130">&nbsp;</th>
        <th width="50">&nbsp;</th>
        <th width="50">&nbsp;</th>
        <th width="60">&nbsp;</th>
        <th width="80">&nbsp;</th>
    </tr>


    <tr>
        <td style="font-weight: bolder;font-size: larger;" align="center" colspan="7" valign="middle">
            <br><br>Виплата зарплаты № {{document_number}} від {{date}} <br><br>
        </td>
    </tr>
    <tr>
        <td style="font-weight: bolder; ;" align colspan="7" valign="middle">
            За {{sdate}}
            <br></td>
    </tr>

    <tr style="font-weight: bolder;">
        <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="30">№</th>
        <th colspan="2" width="230" style="border-top:1px #000 solid;border-bottom:1px #000 solid;">ПІБ</th>
        <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="100">До виплати</th>
        <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="100">Виплачено</th>
        <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="100">Підпис</th>
    </tr>
    {{#_detail}}
    <tr>
        <td align="right">{{no}}</td>
        <td colspan="2">{{emp_name}}</td>
        <td align="right">{{amount}}</td>
        <td align="right">{{payed}}</td>
        <td align="right"></td>
    </tr>
    {{/_detail}}


</table>
<br> <br>
</body>
</html>
