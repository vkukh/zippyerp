<?php

namespace ZippyERP\ERP\Blocks;

use \Zippy\Binding\PropertyBinding as Bind;
use \Zippy\Html\DataList\ArrayDataSource;
use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Label;
use \ZippyERP\ERP\Helper;
use \ZippyERP\System\System;
use \ZippyERP\ERP\Entity\Doc\Document;
use \Carbon\Carbon;
use \ZippyERP\ERP\Entity\Item;
use \ZippyERP\ERP\DataItem;

/**
 * Виджет для  просмотра
 */
class WNoliq extends \Zippy\Html\PageFragment
{

    public function __construct($id) {
        parent::__construct($id);

        $visible = (strpos(System::getUser()->widgets, 'wnoliq') > 0 || System::getUser()->userlogin == 'admin');

        $conn = $conn = \ZDB\DB::getConnect();
        $data = array();


        $sql = "select distinct  sv.`item_id`,sv.`store_id`, sv.`itemname`,sv.`storename` from `erp_stock_view`   sv where closed <> 1   
               and sv.item_id not  in(select sc.item_id 
               from   `erp_account_subconto_view` sc
               where sc.document_date >" . $conn->DBDate(strtotime('- 30 day')) . " and document_date <  " . $conn->DBDate(time()) . "
               and sc.quantity < 0  and sc.store_id not in (select s.store_id from erp_store s where  s.store_type=" . \ZippyERP\ERP\Entity\Store::STORE_TYPE_RET_SUM . " )
                ) and sv.item_type = " . Item::ITEM_TYPE_STUFF . " 
                 
                
                 ";

        if ($visible) {
            $rs = $conn->Execute($sql);

            foreach ($rs as $row) {

                $data[$row['item_id'] . '_' . $row['store_id']] = new DataItem($row);
            }
        }

        $noliqlist = $this->add(new DataView('noliqlist', new ArrayDataSource($data), $this, 'noliqlistOnRow'));
        $noliqlist->setPageSize(20);
        $this->add(new \Zippy\Html\DataList\Paginator("noliqpag", $noliqlist));
        $noliqlist->Reload();


        if (count($data) == 0 || $visible == false) {
            $this->setVisible(false);
        };
    }

    public function noliqlistOnRow($row) {
        $item = $row->getDataItem();

        $row->add(new Label('noliqitem', $item->storename));
        $row->add(new Label('noliqstore', $item->itemname));
        $row->add(new Label('now', $item->now));
    }

}
