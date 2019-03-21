

<table class="ctable" border="0" cellspacing="0" cellpadding="2">



    <tr>
        <td style="font-weight: bolder;font-size: larger;" align="center" colspan="7" valign="middle">
            Списание МЦ с эксплуатации № {{document_number}} от {{date}} <br><br>
        </td>
    </tr>


    <tr style="font-weight: bolder;">
        <th style="border-top:1px #000 solid; border-bottom: 1px #000 solid ;" >Название</th>

        <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="50"  class="text-right">Кол.</th>
    </tr>
    {{#_detail}}
    <tr>
        <td>{{item_name}}</td>

        <td class="text-right">{{quantity}}</td>
    </tr>
    {{/_detail}}


</table>
<br> <br>


