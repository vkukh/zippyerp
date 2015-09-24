<html>
    <body >

<h4> Ручная хоз. операция  № {$document_number}  Дата: {$date} </h4>
Описание:<br>{$description}
<br>

            {if count($_detail['entry']) > 0 }
                <b>Проводки:</b>
                <table  class="ctable" cellpadding="2">
                <tr >
                    <th style="width:40px;border-bottom: black solid 1px;" >Дт</th>
                    <th style="width:40px;border-bottom: black solid 1px;">Кт</th>
                    <th style="width:60px;border-bottom: black solid 1px;">Сумма</th>
                </tr>
                {foreach $_detail['entry'] as $entry}
                    <tr ><td >{$entry.acc_d}</td>
                        <td  >{$entry.acc_c}</td>
                        <td   align="right" >{$entry.amount}</td>
                     </tr>
                    {/foreach}
                </table >
            {/if}
            {if count($_detail['item']) > 0 }
                <br><b>ТМЦ:</b>
                <table  class="ctable" cellpadding="2">
                <tr >
                    <th style="border-bottom: black solid 1px;">Счет</th>
                    <th style="border-bottom: black solid 1px;">Склад</th>
                    <th style="border-bottom: black solid 1px;">ТМЦ</th>
                    <th style="border-bottom: black solid 1px;">Кол.</th>
                    <th style="border-bottom: black solid 1px;">Цена</th>
                    <th style="border-bottom: black solid 1px;">Сумма</th>

                </tr>
                {foreach $_detail['item'] as $item}
                    <tr >
                        <td >{$item.opname} </td>

                        <td  >{$item.store_name}</td>
                        <td  > {$item.item_name} </td>
                        <td   align="right" > {$item.qty} </td>
                        <td   align="right" > {$item.price} </td>
                        <td   align="right" > {$item.amount} </td>
                    </tr>
                {/foreach}    </table>
            {/if}
          {if count($_detail['emp']) > 0 }
                <br><b>Сотрудники:</b>
                <table  class="ctable" cellpadding="2">
                <tr >
                    <th style="border-bottom: black solid 1px;">Счет</th>
                    <th style="border-bottom: black solid 1px;">ФИО</th>
                    <th style="border-bottom: black solid 1px;">Сумма</th>

                </tr>
                {foreach $_detail['emp'] as $item}
                    <tr >
                        <td  >{$item.opname} </td>
                        <td  >{$item.name}</td>
                        <td   align="right" >{$item.amount}</td>
                    </tr>
                {/foreach}  </table>
            {/if}
        {if count($_detail['c']) > 0 }
                <br><b>Контрагенты:</b>
                <table  class="ctable" cellpadding="2">
                <tr >
                    <th style="border-bottom: black solid 1px;">Счет</th>

                    <th style="border-bottom: black solid 1px;">Наименование</th>
                    <th style="border-bottom: black solid 1px;">Сумма</th>

                </tr>
                {foreach $_detail['c'] as $item}
                    <tr >
                        <td  >{$item.opname}</td>

                        <td  >{$item.name}</td>
                        <td   align="right" >{$item.amount}</td>
                    </tr>
                {/foreach}  </table>
            {/if}
       {if count($_detail['f']) > 0 }
                <br><b>Денежные  счета:</b>
                <table  class="ctable" cellpadding="2">
                <tr >
                    <th style="border-bottom: black solid 1px;">Счет</th>
                    <th style="border-bottom: black solid 1px;">Наименование</th>
                    <th style="border-bottom: black solid 1px;">Сумма</th>

                </tr>
                {foreach $_detail['f'] as $item}
                    <tr >
                        <td  >{$item.opname}</td>
                        <td  >{$item.name}</td>
                        <td   align="right" >{$item.amount}</td>
                    </tr>
                {/foreach}
                </table>
            {/if}

</body>
</html>
