<html>

<body>

<h3 style="font-size: 16px;">Рух по рахунку '{{acc}}' з {{from}} по {{to}}</h3>
<br>
<table class="ctable" cellspacing="0" cellpadding="1" border="1">

    <tr style="font-weight: bolder;">
        <th width="50px"></th>
        <th colspan="2">Початкове сальдо</th>
        <th colspan="2">Обороти</th>
        <th colspan="2">Кінцеве сальдо</th>
        <th></th>
    </tr>


    <tr style="font-weight: bolder;">
        <th width="50px">Дата</th>
        <th>Дебет</th>
        <th>Кредит</th>
        <th>Дебет</th>
        <th>Кредит</th>
        <th>Дебет</th>
        <th>Кредит</th>
        <th>Документ</th>
    </tr>
    {{#_detail}}
    <tr>
        <td width="50px">{{date}}</td>
        <td align="right">{{startdt}}</td>
        <td align="right">{{startct}}</td>
        <td align="right">{{amountdt}}</td>
        <td align="right">{{amountct}}</td>
        <td align="right">{{enddt}}</td>
        <td align="right">{{endct}}</td>
        <td align="right">{{{doc}}}</td>
    </tr>
    {{/_detail}}


</table>


<br>
</body>
</html>
