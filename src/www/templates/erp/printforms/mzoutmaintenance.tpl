<html>
<body>
<table class="ctable" border="0" cellspacing="0" cellpadding="2">
    <tr>
        <th width="250px">&nbsp;</th>
        <th width="50">&nbsp;</th>
        <th width="50">&nbsp;</th>
    </tr>


    <tr>
        <td style="font-weight: bolder;font-size: larger;" align="center" colspan="7" valign="middle">
            Списання МЦ з екслуатації № {{document_number}} від {{date}} <br><br>
        </td>
    </tr>


    <tr style="font-weight: bolder;">
        <th
        ="230"  style="border-top: 1px #000 solid
        ;
            border-bottom: 1px #000 solid
        ;" >Назва</th>
        <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="50">Од.вим.</th>
        <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="50">Кіл.</th>
    </tr>
    {{#_detail}}
    <tr>
        <td>{{tovar_name}}</td>
        <td>{{measure}}</td>
        <td align="right">{{quantity}}</td>
    </tr>
    {{/_detail}}


</table>
<br> <br>
</body>
</html>
