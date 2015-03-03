<html>
    <body>
        <table width="500px" border="0" cellpadding="2">
            <tr >

                <td colspan="2">
                    Дата: {$date}
                </td>
            </tr>
            <tr >
                <td >
                    Со склада:  {$from}
                </td>

                <td>
                    На  склад: {$to}
                </td>
            </tr>
            <tr style="font-weight: bolder;">
                <td  colspan="2" align="center" >
                    Перемещение товара  № {$document_number}
                </td>
            </tr>

        </table>
        <br>
        <table width="500px"  cellspacing="0" cellpadding="1" border="1">
            <tr style="font-weight: bolder;"><th width="20px">№</th><th width="180px" >Наименование</th><th  width="50px">Ед.изм</th><th width="50px">Кол.</th><th width="50px">Цена</th></tr>
                    {foreach $_detail as $item}
                <tr ><td>{$item.no}</td><td>{$item.item_name}</td><td>{$item.measure}</td><td align="right">{$item.quantity}</td><td align="right">{$item.price}</td></tr>
                    {/foreach}
        </table>     


        <br> <br>
    </body>
</html>
