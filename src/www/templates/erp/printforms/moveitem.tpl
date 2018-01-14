<html>
<body>
<table class="ctable" width="500px" border="0" cellpadding="2">
    <tr>

        <td colspan="2">
            Дата: {{date}}
        </td>
    </tr>
    <tr>
        <td>
            Зі складу: {{from}}
        </td>

        <td>
            На склад: {{to}}
        </td>
    </tr>
    <tr style="font-weight: bolder;">
        <td colspan="2" align="center">
            Переміщення ТМЦ № {{document_number}}
        </td>
    </tr>

</table>
<br>
<table class="ctable" width="500px" cellspacing="0" cellpadding="1" border="1">
    <tr style="font-weight: bolder;">
        <th width="20px">№</th>
        <th width="180px">Назва</th>
        <th width="50px">Од.вим.</th>
        <th width="50px">Кіл.</th>
        <th width="50px">Ціна</th>
    </tr>
    {{#_detail}}
    <tr>
        <td>{{no}}</td>
        <td>{{item_name}}</td>
        <td>{{measure}}</td>
        <td align="right">{{quantity}}</td>
        <td align="right">{{price}}</td>
    </tr>
    {{/_detail}}
</table>


<br> <br>
</body>
</html>
