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
            Списание МЦ с эксплуатации № {{document_number}} от {{date}} <br><br>
        </td>
    </tr>


    <tr style="font-weight: bolder;">
        <th
        ="230"  style="border-top: 1px #000 solid
        ;
            border-bottom: 1px #000 solid
        ;" >Наименование</th>
        <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="50">Ед.изм</th>
        <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="50">Кол.</th>
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
