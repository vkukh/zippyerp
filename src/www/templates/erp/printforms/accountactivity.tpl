<html>

    <body>

        <h3 style="font-size: 16px;">Движения по  счету '{$acc}' c {$from}  по  {$to}</h3>
        <br>
        <table  class="ctable" cellspacing="0" cellpadding="1" border="1">

            <tr style="font-weight: bolder;">
            <th width="50px">Дата</th>
            <th  >Дебет</th>
            <th >Кредит</th>
            <th >Сумма</th>
            <th  >Дебет</th>
            <th >Кредит</th>
            <th >Документ</th></tr>
            {foreach $_detail as $item}
                <tr >
                <td width="50px">{$item.date}</td>
                <td align="right">{$item.startdt}</td>
                <td align="right">{$item.startct}</td>
                <td align="right">{$item.amount}</td>
                <td align="right">{$item.enddt}</td>
                <td align="right">{$item.endct}</td>
                <td align="right">{$item.doc}</td>
                </tr>
                    {/foreach}


        </table>


        <br>
    </body>
</html>
