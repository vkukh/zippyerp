<html>
    <body>
        <table  class="ctable" width="500" border="0" cellpadding="2" cellspacing="0">
            <tr >
                <td width="100">
                    Отправитель:
                </td>
                <td >
                    {{customername}}
                </td>
                <td>
                    Дата:
                </td>
                <td> {{date}}</td>
            </tr>
            <tr >
                <td style="width: 100;">
                    Плательщик:
                </td>
                <td >
                    Эта  фирма
                </td>
                <td>
                    Дата оплаты:
                </td>
                <td>
                    {{paydate}}
                </td>
            </tr>
            <tr style="font-weight: bolder;">
                <td colspan="4" align="center" >
                    Счет входящий № {{document_number}}
                </td>
            </tr>

        </table>
        <br>
        <table   class="ctable" width="500"  cellspacing="0" cellpadding="1" border="0">
            <tr style="font-weight: bolder;"><th width="20" style="border: 1px solid black;">№</th><th style="border: 1px solid black;" width="180" >Наименование</th><th style="border: 1px solid black;"  width="50">Ед.изм</th><th style="border: 1px solid black;" width="50">Кол.</th><th style="border: 1px solid black;" width="50">Цена-</th><th style="border: 1px solid black;" width="50">Цена+</th><th style="border: 1px solid black;" width="50">Сумма</th></tr>
                    {{#_detail}}
                <tr ><td>{{no}}</td><td>{{itemname}}</td><td>{{measure}}</td><td align="right">{{quantity}}</td><td align="right">{{price}}</td><td align="right">{{pricends}}</td><td align="right">{{amount}}</td></tr>
                    {{/_detail}}
            <tr style="font-weight: bolder;"><td  colspan="6"  style="border-top: 1px solid black;"  align="right">Всего:</td><td width="50" style="border-top: 1px solid black;" align="right">{{total}} </td></tr>
            {{#totalnds}}
                <tr style="font-weight: bolder;"><td colspan="6"  align="right">В т.ч. НДС:</td><td    align="right">{{totalnds}} </td></tr>
            {{/totalnds}}


        </table>

        <br>


        <br> <br>
    </body>
</html>
