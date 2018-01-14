<html>

<body>

<h3 style="font-size: 16px;">Оборотно-сальдова відомість c {{from}} по {{to}}</h3>
<br>
<table class="ctable" cellspacing="0" cellpadding="1" border="1">
    <tr style="font-weight: bolder;">
        <th width="50px"></th>
        <th colspan="2">Початкове сальдо</th>
        <th colspan="2">Обороти</th>
        <th colspan="2">Кінцеве сальдо</th>
    </tr>
    <tr style="font-weight: bolder;">
        <th width="50px">Рахунок</th>
        <th>Дебет</th>
        <th>Кредит</th>
        <th>Дебет</th>
        <th>Кредит</th>
        <th>Дебет</th>
        <th>Кредит</th>
    </tr>
    {{#_detail}}
    <tr>
        <td width="50px">{{acc_code}}</td>
        <td align="right">{{startdt}}</td>
        <td align="right">{{startct}}</td>
        <td align="right">{{obdt}}</td>
        <td align="right">{{obct}}</td>
        <td align="right">{{enddt}}</td>
        <td align="right">{{endct}}</td>
    </tr>
    {{/_detail}}
    <tr style="font-weight: bolder;">
        <th width="50px">Всього:</th>
        <th align="right">{{totstartdt}}</th>
        <th align="right">{{totstartct}}</th>
        <th align="right">{{totobdt}}</th>
        <th align="right">{{totobct}}</th>
        <th align="right">{{totenddt}}</th>
        <th align="right">{{totendct}}</th>
    </tr>

</table>


<br>
</body>
</html>
