<html>
    <body >
        <table width="500" border="0" cellspacing="0" cellpadding="2">
            <tr><td colspan="5">Дата: {$date}</td></tr>
            <tr><td colspan="5" align="center"> <b>  Ручная хоз. операция  № {$document_number} </b> </td></tr>
            <tr><td colspan="5">{$description}</td> </tr>
            <tr style="font-weight: bolder;">
                <td style="width:30px;border: 1px solid black;" >№</td><td style="width:40px;border: 1px solid black;" >Дт</td><td  style="width:40px;border: 1px solid black;">Кт</td><td style="width:60px;border: 1px solid black;">Сумма</td><td style="border: 1px solid black;">&nbsp;</td></tr>
                {foreach $_detail as $item}
                <tr ><td style="border: 1px solid black;">{$item.no}<td style="border: 1px solid black;">{$item.dt}</td><td style="border: 1px solid black;">{$item.ct}</td><td  align="right"  style="border: 1px solid black;">{$item.amount}</td><td style="border: 1px solid black;">{$item.comment}&nbsp;</td></tr>
                    {/foreach}
        </table>     

        <br>
    </body>
</html>
