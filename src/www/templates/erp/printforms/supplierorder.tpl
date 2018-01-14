<html>

<body>
<table class="ctable" width="500" border="0" cellpadding="2" cellspacing="0">
    <tr>
        <td width="100">
            Постачальник:
        </td>
        <td>
            {{customername}}
        </td>
        <td>
            Дата:
        </td>
        <td> {{date}}</td>
        <td>
            Срок:
        </td>
        <td> {{timeline}}</td>
    </tr>
    <tr style="font-weight: bolder;">
        <td colspan="6" align="center">
            Замовленя постачальнику № {{document_number}}
        </td>

    </tr>

</table>
<br>
<table class="ctable" width="500" cellspacing="0" cellpadding="1" border="0">
    <tr style="font-weight: bolder;">
        <th width="20" style="border: 1px solid black;">№</th>
        <th style="border: 1px solid black;" width="180">Найменування</th>
        <th style="border: 1px solid black;" width="50">Од.вим.</th>
        <th style="border: 1px solid black;" width="50">Кіл.</th>
        <th style="border: 1px solid black;" width="50">Ціна</th>
        <th style="border: 1px solid black;" width="50">Сума</th>
    </tr>
    {{#_detail}}
    <tr>
        <td>{{no}}</td>
        <td>{{tovar_name}}</td>
        <td>{{measure}}</td>
        <td align="right">{{quantity}}</td>
        <td align="right">{{price}}</td>
        <td align="right">{{amount}}</td>
    </tr>
    {{/_detail}}
    <tr style="font-weight: bolder;">
        <td colspan="5" style="border-top: 1px solid black;" align="right">Всього:</td>
        <td width="50" style="border-top: 1px solid black;" align="right">{{total}} </td>
    </tr>


</table>
</body>
</html>
