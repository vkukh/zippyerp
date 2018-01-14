<html>
<body>

<h4> Ручна хоз. операція № {{document_number}} Дата: {{date}} </h4>
Опис:<br>{{description}}
<br>

{{#entry?}}
<b>Проводки:</b>
<table class="ctable" cellpadding="2">
    <tr>
        <th style="width:40px;border-bottom: black solid 1px;">Дт</th>
        <th style="width:40px;border-bottom: black solid 1px;">Кт</th>
        <th style="width:60px;border-bottom: black solid 1px;">Сума</th>
    </tr>
    {{#entry}}
    <tr>
        <td>{{acc_d}}</td>
        <td>{{acc_c}}</td>
        <td align="right">{{amount}}</td>
    </tr>
    {{/entry}}
</table>
{{/entry?}}

{{#item?}}
<br><b>ТМЦ:</b>
<table class="ctable" cellpadding="2">
    <tr>
        <th style="border-bottom: black solid 1px;">Рахунок</th>
        <th style="border-bottom: black solid 1px;">Склад</th>
        <th style="border-bottom: black solid 1px;">ТМЦ</th>
        <th style="border-bottom: black solid 1px;">Кіл.</th>
        <th style="border-bottom: black solid 1px;">Ціна</th>
        <th style="border-bottom: black solid 1px;">Сума</th>

    </tr>
    {{#item}}
    <tr>
        <td>{{opname}} </td>

        <td>{{store_name}}</td>
        <td> {{item_name}} </td>
        <td align="right"> {{qty}} </td>
        <td align="right"> {{price}} </td>
        <td align="right"> {{amount}} </td>
    </tr>
    {{/item}}
</table>
{{/item?}}

{{#emp?}}
<br><b>Співробітники:</b>
<table class="ctable" cellpadding="2">
    <tr>
        <th style="border-bottom: black solid 1px;">Рахунок</th>
        <th style="border-bottom: black solid 1px;">ПІБ</th>
        <th style="border-bottom: black solid 1px;">Сума</th>

    </tr>
    {{#emp}}
    <tr>
        <td>{{opname}} </td>
        <td>{{name}}</td>
        <td align="right">{{amount}}</td>
    </tr>
    {{/emp}} </table>
{{/emp?}}

{{#c?}}
<br><b>Контрагенти:</b>
<table class="ctable" cellpadding="2">
    <tr>
        <th style="border-bottom: black solid 1px;">Рахунок</th>

        <th style="border-bottom: black solid 1px;">Назва</th>
        <th style="border-bottom: black solid 1px;">Сума</th>

    </tr>
    {{#c}}
    <tr>
        <td>{{opname}}</td>

        <td>{{name}}</td>
        <td align="right">{{amount}}</td>
    </tr>
    {{/c}}  </table>
{{/c?}}

{{#f?}}
<br><b>Грошові рахунки:</b>
<table class="ctable" cellpadding="2">
    <tr>
        <th style="border-bottom: black solid 1px;">Рахунок</th>
        <th style="border-bottom: black solid 1px;">Назва</th>
        <th style="border-bottom: black solid 1px;">Сума</th>

    </tr>
    {{#f}}
    <tr>
        <td>{{opname}}</td>
        <td>{{name}}</td>
        <td align="right">{{amount}}</td>
    </tr>
    {{/f}}
</table>
{{/f?}}

{{#ca?}}
<br><b>Необортні активи:</b>
<table class="ctable" cellpadding="2">
    <tr>
        <th style="border-bottom: black solid 1px;">Рахунок</th>
        <th style="border-bottom: black solid 1px;">Назва</th>
        <th style="border-bottom: black solid 1px;">Кіл.</th>
        <th style="border-bottom: black solid 1px;">Ціна</th>

    </tr>
    {{#ca}}
    <tr>
        <td>{{opname}}</td>
        <td>{{name}}</td>
        <td align="right">{{cnt}}</td>
        <td align="right">{{price}}</td>
    </tr>
    {{/ca}}
</table>
{{/ca?}}


</body>
</html>
