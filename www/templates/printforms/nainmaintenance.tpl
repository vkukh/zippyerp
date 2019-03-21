

<table class="ctable" border="0" cellspacing="0" cellpadding="2">



    <tr>
        <td style="font-weight: bolder;font-size: larger;" align="center" colspan="6" valign="middle">
            <br><br>Ввод ОС в эксплуатацию № {{document_number}} от {{date}} <br><br>
        </td>
    </tr>


    <tr style="font-weight: bolder;">

        <th colspan="3" style="border-top:1px #000 solid;border-bottom:1px #000 solid;">Наименование</th>
        <th colspan="3" style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="200">Инвентарный  номер
        </th>
    </tr>
    {{#_detail}}
    <tr>
        <td colspan="3">{{ca_name}}</td>
        <td colspan="3">{{inventory}}</td>
    </tr>
    {{/_detail}}


</table>
<br> <br>


