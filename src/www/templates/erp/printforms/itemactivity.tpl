<html>
    <body>
        <table  class="ctable" width="600" border="0" class="ctable" cellpadding="2" cellspacing="0">
            <tr >

                <td align="center" colspan="8">
                    Период  c: {{datefrom}} по {{dateto}}
                </td>
            </tr>
            <tr >

                <td align="center" colspan="8">
                    Склад: <strong>{{store}}</strong> &nbsp;&nbsp;&nbsp;&nbsp;  Товар: <strong>{{item}}</strong>,{{measure}}
                </td>
            </tr>
            <tr style="font-weight: bolder;">
                <td  align="center"  colspan="8">
                    Движение  по  складу
                </td>
            </tr>


            <tr style="font-weight: bolder;"><th style="border: solid black 1px" width="20">№</th><th style="border: solid black 1px" width="70" >Дата</th><th style="border: solid black 1px"  width="60">Цена</th><th  style="border: solid black 1px">Начало</th><th style="border: solid black 1px" >Приход</th><th style="border: solid black 1px" >Расход</th><th  style="border: solid black 1px">Конец</th><th  style="border: solid black 1px">Документы</th></tr>
                     {{#_detail}}
                <tr ><td>{{no}}</td><td>{{date}}</td><td align="right"> {{price}}</td><td align="right">{{in}}</td><td align="right">{{obin}}</td><td align="right">{{obout}}</td><td align="right">{{out}} </td><td> {{{documents}}}</td></tr>
                     {{/_detail}}
        </table>


        <br> <br>
    </body>
</html>
