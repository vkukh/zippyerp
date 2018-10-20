
    
        <table class="ctable" border="0" cellspacing="0" cellpadding="2">
            <tr>
                <th width="30">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="130">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="80">&nbsp;</th>
            </tr>

            <tr>
                <td></td>
                <td>Постачальник</td>
                <td>{{firmname}}</td>
                <td></td>
                <td colspan="3" style="font-size:larger;font-weight:bolder; ">Рахунок-фактура</td>
            </tr>
            <tr>
                <td></td>
                <td>ЄДРПОУ</td>
                <td>{{firmcode}}</td>
                <td></td>
                <td colspan="3" style="font-size:larger;font-weight:bolder; ">№ {{document_number}}</td>
            </tr>
            <tr>
                <td></td>
                <td>Р/c</td>
                <td>{{account}}</td>
                <td></td>
                <td colspan="3" style="font-size:larger;font-weight:bolder; ">від {{date}}</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td colspan="5">в "{{bank}}"</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td colspan="5">МФО {{mfo}}</td>
            </tr>
            <tr>
                <td></td>
                <td>Адрес</td>
                <td colspan="5">{{address}}</td>
            </tr>
            <tr>
                <td></td>
                <td>Отримувач</td>
                <td colspan="5">{{customername}}</td>
            </tr>
            <tr>
                <td></td>
                <td>Платник</td>
                <td colspan="5">Той же</td>
            </tr>

            <tr>
                <td colspan="7">
                    <br>Підстава:{{base}}<br>
                </td>
            </tr>

            <tr style="font-weight: bolder;">
                <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="30">№</th>
                <th colspan="2" width="230" style="border-top:1px #000 solid;border-bottom:1px #000 solid;">Назва</th>
                <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="50">Од.вим.</th>
                <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="50">Кіл.</th>
                <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="60">Ціна</th>
                <th style="border-top:1px #000 solid;border-bottom:1px #000 solid;" width="80">Сума</th>
            </tr>
            {{#_detail}}
            <tr>
                <td align="right">{{no}}
                <td colspan="2">{{tovar_name}}</td>
                <td>{{measure}}</td>
                <td align="right">{{quantity}}</td>
                <td align="right">{{price}}</td>
                <td align="right">{{amount}}</td>
            </tr>
            {{/_detail}}
            <tr style="font-weight: bolder;">
                <td style="border-top:1px #000 solid;" colspan="6" align="right">Всього:</td>
                <td style="border-top:1px #000 solid;" align="right">{{total}}</td>
            </tr>
            <tr style="font-weight: bolder;">
                <td colspan="6" align="right">Всього з ПДВ:</td>
                <td align="right">{{totalnds}}</td>
            </tr>
            <tr>
                <td colspan="7" valign="middle"><br><br><span>Всього на суму:</span> <span
                        style="font-weight: bolder;">{{summa}}</span> <br><br><br></td>
            </tr>

            <tr>
                <td></td>
                <td colspan="2">
                    К оплате до <b>{{paydate}}</b>
                </td>
                <td colspan="4">
                    Виписав
                </td>

            </tr>
        </table>
        <br> <br>
    

