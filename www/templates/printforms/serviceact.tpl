<br>
<table class="ctable"   border="0" cellpadding="2" cellspacing="0">
    <tr>
        <td colspan="5">
            <b> Заказчик: </b> {{customer}}
        </td>

    </tr>

    <tr style="font-weight: bolder;">
        <td colspan="5" align="center">
            Акт выполненых работ № {{document_number}} от {{date}}
            <br> <br></td>
    </tr>

    <tr style="font-weight: bolder;">
        <th width="20" style="border: 1px solid black;">№</th>
        <th style="border: 1px solid black;" width="180">Наименование</th>

        <th style="border: 1px solid black;" width="50" align="right">Кол.</th>
        <th style="border: 1px solid black;" width="50" align="right">Цена</th>
        {{#usends}}       <th align="right" style="text-align: right;border-top:1px #000 solid;border-bottom:1px #000 solid;" width="100">Цена с  НДС</th>{{/usends}}

        <th style="border: 1px solid black;" width="50" align="right">Сумма</th>
    </tr>
    {{#_detail}}
    <tr>
        <td>{{no}}</td>
        <td>{{servicename}}</td>

        <td align="right">{{quantity}}</td>
        <td align="right">{{price}}</td>
        {{#usends}}   <td align="right">{{pricends}}</td>  {{/usends}}

        <td align="right">{{amount}}</td>
    </tr>
    {{/_detail}}
    <tr style="font-weight: bolder;">
        <td colspan="4" style="border-top: 1px solid black;" align="right">Всего:</td>
        {{#usends}}   <td style="border-top:1px #000 solid;" align="right"> </td>  {{/usends}}

        <td style="border-top: 1px solid black;" align="right">{{total}} </td>
    </tr>
    {{#usends}}
    <tr style="font-weight: bolder;">
        <td style="border-top:1px #000 solid;" colspan="4" align="right">В т.ч. НДС:</td>
        <td align="right" style="border-top:1px #000 solid;"> </td>  
        <td style="border-top:1px #000 solid;" align="right">{{totalnds}}</td>
    </tr>  {{/usends}}

</table>


