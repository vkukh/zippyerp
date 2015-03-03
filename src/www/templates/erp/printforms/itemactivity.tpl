<html>
    <body>
        <table width="600" border="0" cellpadding="2" cellspacing="0">
            <tr >

                <td align="center" colspan="8">
                    Период  c: {$datefrom} по {$dateto}
                </td>
            </tr>
            <tr >

                <td align="center" colspan="8">
                    Склад: <strong>{$store}</strong> &nbsp;&nbsp;&nbsp;&nbsp;  Товар: <strong>{$item}</strong>,{$measure}  
                </td>
            </tr>
            <tr style="font-weight: bolder;">
                <td  align="center"  colspan="8">
                    Движение  по  складу
                </td>
            </tr>


            <tr style="font-weight: bolder;"><th style="border: solid black 1px" width="20">№</th><th style="border: solid black 1px" width="70" >Дата</th><th style="border: solid black 1px"  width="60">Цена</th><th  style="border: solid black 1px">Начало</th><th style="border: solid black 1px" >Приход</th><th style="border: solid black 1px" >Расход</th><th  style="border: solid black 1px">Конец</th><th  style="border: solid black 1px">Документы</th></tr>
                    {foreach $_detail as $item}
                <tr ><td>{$item.no}</td><td>{$item.date}</td><td align="right">{$item.price}</td><td align="right">{$item.in}</td><td align="right">{$item.obin}</td><td align="right">{$item.obout}</td><td align="right">{$item.out}</td><td>{$item.documents}</td></tr>
                    {/foreach}
        </table>     


        <br> <br>
    </body>
</html>
