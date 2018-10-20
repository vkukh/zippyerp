
    
        <table class="ctable" width="600" border="0" cellpadding="2" cellspacing="0">
            <tr>
                <td width="100">
                    виконавець:
                </td>
                <td>
                    {{customer}}
                </td>
                <td>
                    Дата: {{date}}
                </td>
            </tr>

            <tr style="font-weight: bolder;">
                <td colspan="3" align="center">
                    Послуги стороньої організації № {{document_number}}
                </td>
            </tr>

        </table>
        <br>
        <table class="ctable" width="600" cellspacing="0" cellpadding="1" border="0">
            <tr style="font-weight: bolder;">
                <th width="20" style="border: 1px solid black;">№</th>
                <th style="border: 1px solid black;" width="180">Найменування</th>
                <th style="border: 1px solid black;" width="50">Од.вим.</th>
                <th style="border: 1px solid black;" width="50">Кіл.</th>
                <th style="border: 1px solid black;" width="50">Ціна-</th>
                <th style="border: 1px solid black;" width="50">Ціна+</th>
                <th style="border: 1px solid black;" width="50">Сума</th>
            </tr>
            {{#_detail}}
            <tr>
                <td>{{no}}</td>
                <td>{{itemname}}</td>
                <td>{{measure}}</td>
                <td align="right">{{quantity}}</td>
                <td align="right">{{price}}</td>
                <td align="right">{{pricends}}</td>
                <td align="right">{{amount}}</td>
            </tr>
            {{/_detail}}
            <tr style="font-weight: bolder;">
                <td colspan="6" style="border-top: 1px solid black;" align="right">Всього:</td>
                <td width="50" style="border-top: 1px solid black;" align="right">{{total}} </td>
            </tr>
            {{#totalnds}}
            <tr style="font-weight: bolder;">
                <td colspan="6" align="right">В т.ч. ПДВ:</td>
                <td align="right">{{totalnds}} </td>
            </tr>
            {{/totalnds}}
        </table>

        <br>
    

