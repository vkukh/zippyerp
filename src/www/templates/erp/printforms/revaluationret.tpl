
    
        <table class="ctable" width="500px" border="0" cellpadding="2">
            <tr>

                <td colspan="2">
                    Дата: {{date}}
                </td>
            </tr>
            <tr>
                <td>
                    Склад: {{store}}
                </td>

                <td>

                </td>
            </tr>
            <tr style="font-weight: bolder;">
                <td colspan="2" align="center">
                    Переоцінка в  роздрібі № {{document_number}}
                </td>
            </tr>

        </table>
        <br>
        <table class="ctable" width="500px" cellspacing="0" cellpadding="1" border="1">
            <tr style="font-weight: bolder;">
                <th width="20px">№</th>
                <th width="180px">Найменування</th>
                <th width="60px">Од.вим.</th>
                <th width="60px">Ціна</th>
                <th width="100px">Нова  ціна</th>
            </tr>
            {{#_detail}}
            <tr>
                <td>{{no}}</td>
                <td>{{item_name}}</td>
                <td>{{measure}}</td>
                <td align="right">{{price}}</td>
                <td align="right">{{newprice}}</td>
            </tr>
            {{/_detail}}
        </table>


        <br> <br>
    

