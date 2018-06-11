<html>

    <body>

        <h3 style="font-size: 16px;">Шахматка з {{from}} по {{to}} </h3>
        <br>
        <table class="ctable" cellspacing="0" cellpadding="1" border="1">


            {{#_detail}}

            <tr>
                {{#row}}
                <td {{#bold}}}}style="font-weight: bolder;"{{/bold}}  {{^right}} align="right" {{/right}}}}>{{cell}}  </td>

                {{/row}}

            </tr>
            {{/_detail}}

        </table>


        <br>
    </body>
</html>
