<html>
    <body>
        <table  class="ctable" width="500px" border="0" cellpadding="2">
            <tr >

                <td colspan="2">
                    Дата: {{date}}
                </td>
            </tr>
            <tr >
                <td >
                   Склад:  {{store}}
                </td>

                <td>

                </td>
            </tr>
            <tr style="font-weight: bolder;">
                <td  colspan="2" align="center" >
                    Переоценка   в  рознице  № {{document_number}}
                </td>
            </tr>

        </table>
        <br>
        <table  class="ctable" width="500px"  cellspacing="0" cellpadding="1" border="1">
            <tr style="font-weight: bolder;"><th width="20px">№</th><th width="180px" >Наименование</th><th  width="60px">Ед.изм</th><th width="60px">Цена</th><th width="100px">Новая цена</th></tr>
                    {{#_detail}}
                <tr ><td>{{no}}</td><td>{{item_name}}</td><td>{{measure}}</td><td align="right">{{price}}</td><td align="right">{{newprice}}</td></tr>
                    {{/_detail}}
        </table>


        <br> <br>
    </body>
</html>
