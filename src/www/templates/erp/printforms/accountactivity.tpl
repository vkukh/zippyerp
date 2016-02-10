<html>

    <body>

        <h3 style="font-size: 16px;">Движения по  счету '{{acc}}' c {{from}}  по  {{to}}</h3>
        <br>
        <table  class="ctable" cellspacing="0" cellpadding="1" border="1">

            <tr style="font-weight: bolder;">
            <th width="50px">Дата</th>
            <th  >Дебет</th>
            <th >Кредит</th>
            <th >Сумма</th>
            <th  >Дебет</th>
            <th >Кредит</th>
            <th >Документ</th></tr>
             {{#_detail}}
                <tr >
                <td width="50px">{{date}}</td>
                <td align="right">{{startdt}}</td>
                <td align="right">{{startct}}</td>
                <td align="right">{{amount}}</td>
                <td align="right">{{enddt}}</td>
                <td align="right">{{endct}}</td>
                <td align="right">{{{doc}}}</td>
                </tr>
               {{/_detail}}


        </table>


        <br>
    </body>
</html>
