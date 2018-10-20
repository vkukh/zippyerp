
    
        <table class="ctable" width="750" border="0" class="ctable" cellpadding="2" cellspacing="0">

            <tr style="font-size:larger; font-weight: bolder;">
                <td align="center" colspan="8">
                    Рух по складу 
                </td>
            </tr>            
            <tr>

                <td align="center" colspan="8">
                    Період з: {{datefrom}} по {{dateto}}&nbsp;&nbsp;&nbsp;&nbsp; Склад: <strong>{{store}}</strong> 
                </td>
            </tr>




            <tr style="font-weight: bolder;">

                <th style="border: solid black 1px"  >Дата</th>
                <th style="border: solid black 1px" >Код</th>
                <th style="border: solid black 1px" >Найменування</th>
                <th style="border: solid black 1px"  >Ціна</th>
                <th style="border: solid black 1px">Поч.</th>
                <th style="border: solid black 1px">Прих.</th>
                <th style="border: solid black 1px">Розх.</th>
                <th style="border: solid black 1px">Кін.</th>
                <th style="border: solid black 1px">Документи</th>
            </tr>
            {{#_detail}}
            <tr>

                <td>{{date}}</td>
                <td>{{code}}</td>
                <td>{{name}}</td>
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
    

