 
<table class="ctable" border="0" cellspacing="0" cellpadding="2">



    <tr>
        <td></td>
        <td><b>Поставщик</b></td>
        <td colspan="4">{{customername}}</td>
    </tr>


    <tr>
        <td style="font-weight: bolder;font-size: larger;" align="center" colspan="6" valign="middle">
            Возврат постващику № {{document_number}} от {{date}} <br> 
        </td>
    </tr>

    <tr style="font-weight: bolder;">
        <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="30">№</th>
        <th     style="border-top:1px #000 solid;border-bottom:1px #000 solid;text-align: left;">Наименование</th>
        <th    style="border-top:1px #000 solid;border-bottom:1px #000 solid;text-align: left;">Код</th>


        <th  align="right"  style="text-align: right;border-top:1px #000 solid;border-bottom:1px #000 solid;" width="60">Кол.</th>
        <th  align="right"  style="text-align: right;border-top:1px #000 solid;border-bottom:1px #000 solid;" width="60">Цена</th>
       {{#usends}}       <th align="right" style="text-align: right;border-top:1px #000 solid;border-bottom:1px #000 solid;" width="100">Цена с  НДС</th>{{/usends}}
        
        <th   style="text-align: right;border-top:1px #000 solid;border-bottom:1px #000 solid;" width="80">Сумма</th>
    </tr>
    {{#_detail}}
    <tr>
        <td align="right">{{no}}</td>
        <td  >{{tovar_name}}</td>
        <td  >{{tovar_code}}</td>


        <td align="right">{{quantity}}</td>
        <td align="right">{{price}}</td>
          {{#usends}}   <td align="right">{{pricends}}</td>  {{/usends}}
      
        <td align="right">{{amount}}</td>
    </tr>
    {{/_detail}}
    <tr style="font-weight: bolder;">
        <td style="border-top:1px #000 solid;" colspan="5" align="right">Итого:</td>
       {{#usends}}   <td style="border-top:1px #000 solid;" align="right"> </td>  {{/usends}}
        
        <td style="border-top:1px #000 solid;" align="right">{{total}}</td>
    </tr>
    {{#usends}}
    <tr style="font-weight: bolder;">
        <td style="border-top:1px #000 solid;" colspan="5" align="right">В т.ч. НДС:</td>
        <td align="right" style="border-top:1px #000 solid;"> </td>  
        <td style="border-top:1px #000 solid;" align="right">{{totalnds}}</td>
    </tr>  {{/usends}}

</table>

