<html>
<body>
<table class="ctable" width="600" border="0" class="ctable" cellpadding="2" cellspacing="0">
    <tr>

        <td align="center" colspan="8">
            Дата: {{datefrom}}  
        </td>
    </tr>
    <tr>

        <td align="center" colspan="8">
            Склад: <strong>{{store}}</strong>  
        </td>
    </tr>
    <tr style="font-weight: bolder;">
        <td align="center" colspan="8">
            Товари на  складі
        </td>
    </tr>


    <tr style="font-weight: bolder;">
     
        <th style="border: solid black 1px" width="200">Найменування</th>
        <th style="border: solid black 1px" width="60">Ціна</th>
        <th style="border: solid black 1px" width="60">Кіл.</th>
 
    </tr>
    {{#_detail}}
    <tr>
     
        <td>{{item}}</td>
        <td align="right"> {{price}}</td>
        <td align="right">{{qty}}</td>
   
        
    </tr>
    {{/_detail}}
</table>


<br> <br>
</body>
</html>
