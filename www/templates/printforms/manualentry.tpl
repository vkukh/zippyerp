


<h4> Ручная хоз. операция № {{document_number}} Дата: {{date}} </h4>
Опис:<br>{{description}}
<br>

{{#entry?}}
<b>Проводки:</b>
<table class="ctable" cellpadding="2">
    <tr>
        <th style="width:40px;border-bottom: black solid 1px;">Дт</th>
        <th style="width:40px;border-bottom: black solid 1px;">Кт</th>
        <th style="width:60px;border-bottom: black solid 1px;">Сумма</th>
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
        <th style="border-bottom: black solid 1px;">Счет</th>
        <th style="border-bottom: black solid 1px;">Склад</th>
        <th style="border-bottom: black solid 1px;">ТМЦ</th>
        <th style="border-bottom: black solid 1px;">Кол.</th>
        <th style="border-bottom: black solid 1px;">Цена</th>
        <th style="border-bottom: black solid 1px;">Сумма</th>

    </tr>
    {{#item}}
    <tr>
        <td>{{opname}} </td>

        <td>{{store_name}}</td>
        <td> {{itemname}} </td>
        <td align="right"> {{qty}} </td>
        <td align="right"> {{price}} </td>
        <td align="right"> {{amount}} </td>
    </tr>
    {{/item}}
</table>
{{/item?}}

{{#emp?}}
<br><b>Сотрудники:</b>
<table class="ctable" cellpadding="2">
    <tr>
        <th style="border-bottom: black solid 1px;">Счет</th>
        <th style="border-bottom: black solid 1px;">ФИО</th>
        <th style="border-bottom: black solid 1px;">Сумма</th>

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
<br><b>Контрагенты:</b>
<table class="ctable" cellpadding="2">
    <tr>
        <th style="border-bottom: black solid 1px;">Счет</th>

        <th style="border-bottom: black solid 1px;">Название</th>
        <th style="border-bottom: black solid 1px;">Сумма</th>

    </tr>
    {{#c}}
    <tr>
        <td>{{opname}}</td>

        <td>{{name}}</td>
        <td align="right">{{amount}}</td>
    </tr>
    {{/c}}  </table>
    {{/c?}}



{{#ca?}}
<br><b>Необоротные активы:</b>
<table class="ctable" cellpadding="2">
    <tr>
        <th style="border-bottom: black solid 1px;">Счет</th>
        <th style="border-bottom: black solid 1px;">Название</th>
        <th style="border-bottom: black solid 1px;">Кол.</th>
        <th style="border-bottom: black solid 1px;">Цена</th>

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




