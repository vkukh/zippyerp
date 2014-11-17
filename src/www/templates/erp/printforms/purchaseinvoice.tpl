<html>
    <body>
        <table width="500" border="0" cellpadding="2" cellspacing="0">
            <tr >
                <td width="100">
                    Отправитель:
                </td>
                <td >
                    {$customername}
                </td>
                <td>
                    Дата: 
                </td>
                <td> {$date}</td>
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
                    {$paydate}
                </td>
            </tr>
            <tr style="font-weight: bolder;">
                <td colspan="3" align="center" >
                    Счет входящий № {$document_number}
                </td>
            </tr>

        </table>
        <br>
        <table width="500"  cellspacing="0" cellpadding="1" border="0">
            <tr style="font-weight: bolder;"><th width="20" style="border: 1px solid black;">№</th><th style="border: 1px solid black;" width="180" >Наименование</th><th style="border: 1px solid black;"  width="50">Ед.изм</th><th style="border: 1px solid black;" width="50">Кол.</th><th style="border: 1px solid black;" width="50">Цена</th><th style="border: 1px solid black;" width="50">Сумма</th></tr>
                    {foreach $_detail as $item}
                <tr ><td>{$item.no}<td>{$item.tovar_name}</td><td>{$item.measure}</td><td align="right">{$item.quantity}</td><td align="right">{$item.price}</td><td align="right">{$item.amount}</td></tr>
                    {/foreach}
            <tr style="font-weight: bolder;"><td  colspan="5"  style="border-top: 1px solid black;"  align="right">Всего:</td><td width="50" style="border-top: 1px solid black;" align="right">{$total} </td></tr>
            {if $nds > 0}
                <tr style="font-weight: bolder;"><td colspan="5"  align="right">В т.ч. НДС:</td><td   width="50"  align="right">{$nds} </td></tr>
            {/if}

        </table>

        <br>


        <br> <br>
    </body>
</html>
