<html>
<body>
<table class="ctable" width="600" border="0" class="ctable" cellpadding="2" cellspacing="0">
    <tr>

        <td align="center" colspan="8">
            Період з: {{datefrom}} по {{dateto}}
        </td>
    </tr>
    <tr>

        <td align="center" colspan="8">
            Склад: <strong>{{store}}</strong> &nbsp;&nbsp;&nbsp;&nbsp; Товар: <strong>{{item}}</strong>,{{measure}}
        </td>
    </tr>
    <tr style="font-weight: bolder;">
        <td align="center" colspan="8">
            Рух по складу
        </td>
    </tr>


    <tr style="font-weight: bolder;">
        <th style="border: solid black 1px" width="20">№</th>
        <th style="border: solid black 1px" width="70">Дата</th>
        <th style="border: solid black 1px" width="60">Ціна</th>
        <th style="border: solid black 1px">Початок</th>
        <th style="border: solid black 1px">Прибуток</th>
        <th style="border: solid black 1px">Видаток</th>
        <th style="border: solid black 1px">Кінець</th>
        <th style="border: solid black 1px">Документи</th>
    </tr>
    {{#_detail}}
    <tr>
        <td>{{no}}</td>
        <td>{{date}}</td>
        <td align="right"> {{price}}</td>
        <td align="right">{{in}}</td>
        <td align="right">{{obin}}</td>
        <td align="right">{{obout}}</td>
        <td align="right">{{out}} </td>
        <td> {{{documents}}}</td>
    </tr>
    {{/_detail}}
</table>


<br> <br>
</body>
</html>
