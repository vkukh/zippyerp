<?php

namespace ZippyERP\ERP\Entity;


use ZippyERP\ERP\Consts;
/**
 * Клас-сущность  товар
 * 
 * @table=erp_item
 * @view=erp_item_view
 * @keyfield=item_id
 */
class Item extends \ZCL\DB\Entity
{

   protected function afterLoad(){
   
       switch ($this->item_type){
            case Consts::ITEM_TYPE_GOODS : $this->typename ='Товар'; break;
            case Consts::ITEM_TYPE_MBP : $this->typename ='МБП'; break;
            case Consts::ITEM_TYPE_SERVICE : $this->typename ='МБП'; break;
       }
   }  
}

