<html>
    <body>
        <table   class="ctable"  border="0" cellspacing="0" cellpadding="2">
            <tr><th width="30">&nbsp;</th><th  width="100" >&nbsp;</th><th  width="130" >&nbsp;</th><th  width="50">&nbsp;</th><th width="50">&nbsp;</th><th width="60">&nbsp;</th><th width="80">&nbsp;</th></tr>

            <tr ><td> </td><td >Поставщик</td><td colspan="5">{$customername}</td></tr>
            <tr ><td> </td><td  > </td><td colspan="5">{$code}</td></tr>
            <tr ><td> </td><td  >Получатель</td><td colspan="5">{$firmname}</td></tr>

            <tr >
                <td style="font-weight: bolder;font-size: larger;" align="center" colspan="7"  valign="middle" >
                    <br><br> Входящая НН № {$document_number}  от  {$date}    <br><br><br>
                </td>
            </tr>
        </table>
         <br>
        <table  class="ctable" width="500"  cellspacing="0" cellpadding="1" border="0">
            <tr style="font-weight: bolder;"><th width="20" style="border: 1px solid black;">№</th><th style="border: 1px solid black;" width="180" >Наименование</th><th style="border: 1px solid black;"  width="50">Ед.изм</th><th style="border: 1px solid black;" width="50">Кол.</th><th style="border: 1px solid black;" width="50">Цена-</th><th style="border: 1px solid black;" width="50">Цена+</th><th style="border: 1px solid black;" width="50">Сумма</th></tr>
                    {foreach $_detail as $item}
                <tr ><td>{$item.no}</td><td>{$item.itemname}</td><td>{$item.measure}</td><td align="right">{$item.quantity}</td><td align="right">{$item.price}</td><td align="right">{$item.pricends}</td><td align="right">{$item.amount}</td></tr>
                    {/foreach}
            <tr style="font-weight: bolder;"><td  colspan="6"  style="border-top: 1px solid black;"  align="right">Всего:</td><td width="50" style="border-top: 1px solid black;" align="right">{$total} </td></tr>
           {if $totalnds > 0}
                <tr style="font-weight: bolder;"><td colspan="6"  align="right">В т.ч. НДС:</td><td    align="right">{$totalnds} </td></tr>
            {/if}


        </table>


    </body>
</html>
