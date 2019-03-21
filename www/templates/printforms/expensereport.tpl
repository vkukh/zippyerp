

<table   border="0" cellpadding="2" cellspacing="0">
    <tr style="font-weight: bolder;">
        <td colspan="7" align="center">
            Авансовый отчет № {{document_number}}  от {{date}}
        </td>
    </tr>            

    <tr>
        <td colspan="2" >
            Сотрудник:
        </td>
        <td colspan="56">
            {{employee}}
        </td>

    </tr>


    {{#expenseamount}}
    <tr>
        <td colspan="2">
            Сумма  накладных затрат 
        </td>
        <td colspan="5">
            {{expenseamount}}
        </td>
    </tr> {{/expenseamount}}
    <tr>
        <td colspan="7">
            {{comment}}
        </td>
    </tr>   
    <tr style="font-weight: bolder;">
        <th width="20" style="border: 1px solid black;">№</th>
        <th style="border: 1px solid black;" comment>Название</th>
        <th style="border: 1px solid black;" width="50">Ед.изм.</th>
        <th style="border: 1px solid black;" width="50">Кол.</th>
        <th style="border: 1px solid black;" width="50">Цена-</th>
        {{#usends}}  <th style="border: 1px solid black;" width="50">Цена+</th>{{/usends}}
        <th style="border: 1px solid black;" width="50">Сумма</th>
    </tr>
    {{#_detail}}
    <tr>
        <td>{{no}}</td>
        <td>{{itemname}}</td>
        <td>{{measure}}</td>
        <td align="right">{{quantity}}</td>
        <td align="right">{{price}}</td>
        {{#usends}}   <td align="right">{{pricends}}</td> {{/usends}}
        <td align="right">{{amount}}</td>
    </tr>
    {{/_detail}}
    <tr style="font-weight: bolder;">
        <td colspan="5" style="border-top: 1px solid black;" align="right">Всего:</td>
        {{#usends}}   <td style="border-top:1px #000 solid;" align="right"> </td>  {{/usends}}                
        <td width="50" style="border-top: 1px solid black;" align="right">{{total}} </td>
    </tr>
    {{#usends}}
    <tr style="font-weight: bolder;">
        <td colspan="5" align="right">В т.ч. НДС:</td>
        <td align="right">  </td>
        <td align="right">{{totalnds}} </td>
    </tr>
    {{/usends}}
</table>




