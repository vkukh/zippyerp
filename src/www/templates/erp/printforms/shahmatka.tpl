<html>

    <body>

        <h3 style="font-size: 16px;">Шахматка c {$from}  по  {$to}</h3>
        <br>
        <table   class="ctable"  cellspacing="0" cellpadding="1" border="1">
            {set  $i = 0}

            {foreach $_detail as $row}
                {set  $j = 0}
                <tr>
                    {foreach $row as $cell}
                        <td {if $j == 0 || $i == 0 || $j == $size || $i == $size} style="font-weight: bolder;" {/if}   {if $j != 0 && $i != 0} align="right"  {/if}>{$cell}</td>
                        {set $j = $j+1}
                    {/foreach}
                    {set $i = $i+1}
                </tr>
            {/foreach}

        </table>


        <br>
    </body>
</html>
