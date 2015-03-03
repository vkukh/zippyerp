<?php 
/** Fenom template 'manualentry.tpl' compiled at 2015-03-03 22:54:36 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><html>
    <body >
    
<h4> Ручная хоз. операция  № <?php
/* manualentry.tpl:4: {$document_number} */
 echo $var["document_number"]; ?>  Дата: <?php
/* manualentry.tpl:4: {$date} */
 echo $var["date"]; ?> </h4>
Описание:<br><?php
/* manualentry.tpl:5: {$description} */
 echo $var["description"]; ?>
<br>

            <?php
/* manualentry.tpl:8: {if count($_detail['entry']) > 0 } */
 if(count($var["_detail"]['entry']) > 0) { ?>
                <b>Проводки:</b>
                <table cellpadding="2">
                <tr >
                    <th style="width:40px;border-bottom: black solid 1px;" >Дт</th>
                    <th style="width:40px;border-bottom: black solid 1px;">Кт</th>
                    <th style="width:60px;border-bottom: black solid 1px;">Сумма</th>
                </tr>
                <?php
/* manualentry.tpl:16: {foreach $_detail['entry'] as $entry} */
  if(!empty($var["_detail"]['entry'])) {  foreach($var["_detail"]['entry'] as $var["entry"]) {  ?>
                    <tr ><td ><?php
/* manualentry.tpl:17: {$entry.acc_d} */
 echo $var["entry"]["acc_d"]; ?></td>
                        <td ><?php
/* manualentry.tpl:18: {$entry.acc_c} */
 echo $var["entry"]["acc_c"]; ?></td>
                        <td  align="right" ><?php
/* manualentry.tpl:19: {$entry.amount} */
 echo $var["entry"]["amount"]; ?></td> 
                     </tr>
                    <?php
/* manualentry.tpl:21: {/foreach} */
   } } ?>
                </table >
            <?php
/* manualentry.tpl:23: {/if} */
 } ?> 
            <?php
/* manualentry.tpl:24: {if count($_detail['item']) > 0 } */
 if(count($var["_detail"]['item']) > 0) { ?>   
                <br><b>ТМЦ:</b> 
                <table cellpadding="2">                   
                <tr >
                    <th style="border-bottom: black solid 1px;">+/-</th>
                    <th style="width:60px;border-bottom: black solid 1px;">Код</th>                    
                    <th style="border-bottom: black solid 1px;">Склад</th>
                    <th style="border-bottom: black solid 1px;">ТМЦ</th>
                    <th style="border-bottom: black solid 1px;">Кол.</th>
                    <th style="border-bottom: black solid 1px;">Цена</th>
                    <th style="border-bottom: black solid 1px;">Сумма</th>

                </tr>
                <?php
/* manualentry.tpl:37: {foreach $_detail['item'] as $item} */
  if(!empty($var["_detail"]['item'])) {  foreach($var["_detail"]['item'] as $var["item"]) {  ?>
                    <tr >
                        <td ><?php
/* manualentry.tpl:39: {$item.opname} */
 echo $var["item"]["opname"]; ?></td>
                        <td ><?php
/* manualentry.tpl:40: {$item.code} */
 echo $var["item"]["code"]; ?></td>
                        <td ><?php
/* manualentry.tpl:41: {$item.store_name} */
 echo $var["item"]["store_name"]; ?></td>
                        <td ><?php
/* manualentry.tpl:42: {$item.item_name} */
 echo $var["item"]["item_name"]; ?></td>
                        <td  align="right" ><?php
/* manualentry.tpl:43: {$item.qty} */
 echo $var["item"]["qty"]; ?></td>
                        <td  align="right" ><?php
/* manualentry.tpl:44: {$item.price} */
 echo $var["item"]["price"]; ?></td>
                        <td  align="right" ><?php
/* manualentry.tpl:45: {$item.amount} */
 echo $var["item"]["amount"]; ?></td>
                    </tr>  
                <?php
/* manualentry.tpl:47: {/foreach} */
   } } ?>    </table>        
            <?php
/* manualentry.tpl:48: {/if} */
 } ?>     
          <?php
/* manualentry.tpl:49: {if count($_detail['emp']) > 0 } */
 if(count($var["_detail"]['emp']) > 0) { ?>   
                <br><b>Сотрудники:</b> 
                <table cellpadding="2">                   
                <tr >
                    <th style="border-bottom: black solid 1px;">+/-</th>
                    <th style="border-bottom: black solid 1px;">ФИО</th>
                    <th style="border-bottom: black solid 1px;">Сумма</th>

                </tr>
                <?php
/* manualentry.tpl:58: {foreach $_detail['emp'] as $item} */
  if(!empty($var["_detail"]['emp'])) {  foreach($var["_detail"]['emp'] as $var["item"]) {  ?>
                    <tr >
                        <td ><?php
/* manualentry.tpl:60: {$item.opname} */
 echo $var["item"]["opname"]; ?></td>
                        <td ><?php
/* manualentry.tpl:61: {$item.name} */
 echo $var["item"]["name"]; ?></td>
                        <td  align="right" ><?php
/* manualentry.tpl:62: {$item.amount} */
 echo $var["item"]["amount"]; ?></td>
                    </tr>  
                <?php
/* manualentry.tpl:64: {/foreach} */
   } } ?>  </table>          
            <?php
/* manualentry.tpl:65: {/if} */
 } ?>     
        <?php
/* manualentry.tpl:66: {if count($_detail['c']) > 0 } */
 if(count($var["_detail"]['c']) > 0) { ?>   
                <br><b>Контрагенты:</b> 
                <table cellpadding="2">                   
                <tr >
                    <th style="border-bottom: black solid 1px;">+/-</th>
                    <th style="border-bottom: black solid 1px;">Тип</th>
                    <th style="border-bottom: black solid 1px;">Наименование</th>
                    <th style="border-bottom: black solid 1px;">Сумма</th>

                </tr>
                <?php
/* manualentry.tpl:76: {foreach $_detail['c'] as $item} */
  if(!empty($var["_detail"]['c'])) {  foreach($var["_detail"]['c'] as $var["item"]) {  ?>
                    <tr >
                        <td ><?php
/* manualentry.tpl:78: {$item.opname} */
 echo $var["item"]["opname"]; ?></td>
                        <td ><?php
/* manualentry.tpl:79: {$item.optype} */
 echo $var["item"]["optype"]; ?></td>
                        <td ><?php
/* manualentry.tpl:80: {$item.name} */
 echo $var["item"]["name"]; ?></td>
                        <td  align="right" ><?php
/* manualentry.tpl:81: {$item.amount} */
 echo $var["item"]["amount"]; ?></td>
                    </tr>  
                <?php
/* manualentry.tpl:83: {/foreach} */
   } } ?>  </table>          
            <?php
/* manualentry.tpl:84: {/if} */
 } ?>     
       <?php
/* manualentry.tpl:85: {if count($_detail['f']) > 0 } */
 if(count($var["_detail"]['f']) > 0) { ?>   
                <br><b>Денежные  счета:</b> 
                <table cellpadding="2">                   
                <tr >
                    <th style="border-bottom: black solid 1px;">+/-</th>
                    <th style="border-bottom: black solid 1px;">Наименование</th>
                    <th style="border-bottom: black solid 1px;">Сумма</th>

                </tr>
                <?php
/* manualentry.tpl:94: {foreach $_detail['f'] as $item} */
  if(!empty($var["_detail"]['f'])) {  foreach($var["_detail"]['f'] as $var["item"]) {  ?>
                    <tr >
                        <td ><?php
/* manualentry.tpl:96: {$item.opname} */
 echo $var["item"]["opname"]; ?></td>
                        <td ><?php
/* manualentry.tpl:97: {$item.name} */
 echo $var["item"]["name"]; ?></td>
                        <td  align="right" ><?php
/* manualentry.tpl:98: {$item.amount} */
 echo $var["item"]["amount"]; ?></td>
                    </tr>  
                <?php
/* manualentry.tpl:100: {/foreach} */
   } } ?> 
                </table>           
            <?php
/* manualentry.tpl:102: {/if} */
 } ?>     
       
</body>
</html>
<?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'manualentry.tpl',
	'base_name' => 'manualentry.tpl',
	'time' => 1425416030,
	'depends' => array (
  0 => 
  array (
    'manualentry.tpl' => 1425416030,
  ),
),
	'macros' => array(),

        ));
