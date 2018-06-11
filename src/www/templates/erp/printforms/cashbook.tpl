<html>
    <body>


        <table class="ctable" border="0" cellspacing="0" cellpadding="2">
            {{#header}}
            <tbody>
                <tr>
                    <td>&nbsp; </td>
                    <td>&nbsp; </td>
                    <td>&nbsp; </td>
                    <td>&nbsp; </td>
                    <td>&nbsp; </td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp; </td>

                    <td colspan="2">Типова форма № КО-4</td>

                </tr>
                <tr>
                    <td colspan="5">&nbsp; </td>
                </tr>

                <tr>
                    <td colspan="2">{{firm}}</td>
                    <td>&nbsp;</td>
                    <td colspan="2">ЄДРПОУ {{code}} </td>

                </tr>
                <tr>
                    <td colspan="5">&nbsp; </td>
                </tr>

                <tr>
                <tr>
                    <td colspan="5" align="center" style="font-weight: bolder;font-size: 20px;">Касова книга на {{mn}} {{yr}} </td>
                </tr>
                <tr>
                    <td colspan="5">&nbsp; </td>
                </tr>
                <tr>
                    <td>&nbsp; </td>
                    <td>У цій книзі пронумеровано
                        та прошнуровано ____ сторінок,
                        опечатано печаткою
                    </td>
                    <td colspan="2">&nbsp; </td>
                </tr>
                <tr>
                    <td colspan="5">&nbsp; </td>
                </tr>
                <tr>
                    <td>&nbsp; </td>
                    <td>МП &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Пiдпис
                    </td>
                    <td colspan="2">&nbsp; </td>
                </tr>
                <tr>
                    <td colspan="5">&nbsp; </td>
                </tr>
                <tr>
                    <td>&nbsp; </td>
                    <td>"___" ________________ 20__ р.
                    </td>
                    <td colspan="2">&nbsp; </td>
                </tr>
                <tr>
                <tr>
                    <td colspan="5">&nbsp; </td>
                </tr>
            </tbody>
            {{/header}}

            {{#_detail}}
            <tbody style="page-break-before: always">
                <tr>
                    <td colspan="5">Каса за {{date}}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000">Номер документа</td>
                    <td style="border: 1px solid #000">Від кого отримано чи кому видано</td>
                    <td style="border: 1px solid #000">Номер кор.<br> рахунку</td>
                    <td style="border: 1px solid #000">Надходження</td>
                    <td style="border: 1px solid #000">Видаток</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000;text-align: center;">1</td>
                    <td style="border: 1px solid #000;text-align: center;">2</td>
                    <td style="border: 1px solid #000;text-align: center;">3</td>
                    <td style="border: 1px solid #000;text-align: center;">4</td>
                    <td style="border: 1px solid #000;text-align: center;">5</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000">&nbsp; </td>
                    <td style="border: 1px solid #000">Залишок на початок дня</td>
                    <td style="border: 1px solid #000">&nbsp; </td>
                    <td style="border: 1px solid #000" align="right">{{start}}  </td>
                    <td style="border: 1px solid #000"> &nbsp;</td>
                </tr>
                {{#lines}}

                <tr>
                    <td style="border: 1px solid #000">{{doc}} </td>
                    <td style="border: 1px solid #000">{{desc}} </td>
                    <td style="border: 1px solid #000">&nbsp;&nbsp;{{acc}} </td>
                    <td style="border: 1px solid #000" align="right">{{in}} </td>
                    <td style="border: 1px solid #000" align="right">{{out}} </td>
                </tr>

                {{/lines}}
                <tr>
                    <td style="border: 1px solid #000"> &nbsp;</td>
                    <td style="border: 1px solid #000">Залишок на кінець дня</td>
                    <td style="border: 1px solid #000"> &nbsp;</td>
                    <td style="border: 1px solid #000" align="right">{{end}} </td>
                    <td style="border: 1px solid #000">&nbsp; </td>
                </tr>
                <tr>
                    <td colspan="5">&nbsp;</td>
                </tr>
                <tr>
                    <td>Кассир</td>
                    <td>________________</td>
                    <td colspan="3">&nbsp;</td>
                </tr>
            </tbody>

            {{/_detail}}
        </table>
        {{^_detail}}
        <h4>Не знайдені документи за вказаний період</h4>

        {{/_detail}} <br>
    </body>
</html>
