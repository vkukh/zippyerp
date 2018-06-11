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

/**
 * Виджет для  просмотра
 */
class WPlannedDocs extends \Zippy\Html\PageFragment
{

    public function __construct($id) {
        parent::__construct($id);

        $visible = (strpos(System::getUser()->widgets, 'wplan') > 0 || System::getUser()->userlogin == 'admin');

        $conn = $conn = \ZDB\DB::getConnect();
        $data = array();

        // список  запланированных документов
        $where = "state= " . Document::STATE_EXECUTED;
        $where = $where . " and  document_date >= " . $conn->DBDate(strtotime('-1 week'));
        $where = $where . " and  meta_name in ('RetailIssue','GoodsIssue','GoodsReceipt','MoveItem') ";
        $where = $where . " and  content like '%<plan>1</plan>%'";
        if ($visible) {
            $data = Document::find($where);
        }

        $doclist = $this->add(new DataView('pdoclist', new ArrayDataSource($data), $this, 'doclistOnRow'));
        $doclist->Reload();

        if (count($data) == 0 || $visible == false) {
            $this->setVisible(false);
        };
    }

    public function doclistOnRow($row) {
        $item = $row->getDataItem();
        $item = $item->cast();

        $row->add(new Label('number', $item->document_number));
        $row->add(new Label('date', date('d-m-Y', $item->document_date)));
        $row->add(new Label('type', $item->meta_desc));

        $date = new Carbon();
        $start = $date->startOfDay()->timestamp;
        $end = $date->endOfDay()->timestamp;

        if ($item->document_date < $start) {
            $row->number->setAttribute('class', 'label label-danger');
        }
        if ($item->document_date >= $start && $item->document_date <= $end) {
            $row->number->setAttribute('class', 'label label-warning');
        }
    }

}
