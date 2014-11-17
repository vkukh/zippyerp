<html>

    <body>

        <h3 style="font-size: 16px;">Оборотно-сальдовая ведомость c {$from}  по  {$to}</h3>
        <br>
        <table   cellspacing="0" cellpadding="1" border="1">
            <tr style="font-weight: bolder;"><th width="50px"> </th><th  colspan="2">Начальное  сальдо</th><th   colspan="2">Обороты</th><th colspan="2">Конечное сальдо</th></tr>
            <tr style="font-weight: bolder;"><th width="50px">Счет</th><th  >Дебет</th><th >Кредит</th><th >Дебет</th><th >Кредит</th><th  >Дебет</th><th >Кредит</th></tr>
                    {foreach $_detail as $item}
                <tr ><td width="50px">{$item.acc_code}</td><td align="right">{$item.startdt}</td><td align="right">{$item.startct}</td><td align="right">{$item.obdt}</td><td align="right">{$item.obct}</td><td align="right">{$item.enddt}</td><td align="right">{$item.endct}</td></tr>
                    {/foreach}
            <tr style="font-weight: bolder;"><th width="50px">Итого: </th><th align="right">{$totstartdt}</th><th align="right">{$totstartct}</th><th align="right">{$totobdt}</th><th align="right">{$totobct}</th><th align="right">{$totenddt}</th><th align="right">{$totendct}</th></tr>

        </table>     


        <br> 
    </body>
</html>
