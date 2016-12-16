<?php

namespace ZippyERP\ERP\Pages;

use Carbon\Carbon;
use Zippy\Html\DataList\ArrayDataSource;
use Zippy\Html\DataList\DataView;
use Zippy\Html\Label;
use ZippyERP\ERP\DataItem;
use ZippyERP\ERP\Entity\Doc\Document;

;
;

class Main extends \ZippyERP\System\Pages\Base
{

    public function __construct()
    {
        parent::__construct();

        $data = $this->getData();


        $doclist = $this->add(new DataView('pdoclist', new ArrayDataSource($data['planned']), $this, 'doclistOnRow'));
        $doclist->Reload();
        $noliqlist = $this->add(new DataView('noliqlist', new ArrayDataSource($data['noliq']), $this, 'noliqlistOnRow'));
        $noliqlist->Reload();
        $lowlist = $this->add(new DataView('lowlist', new ArrayDataSource($data['lowitems']), $this, 'lowlistOnRow'));
        $lowlist->Reload();
        $highlist = $this->add(new DataView('highlist', new ArrayDataSource($data['highitems']), $this, 'highlistOnRow'));
        $highlist->Reload();


        $this->_tvars['planned'] = count($data['planned']) > 0;
        $this->_tvars['noliq'] = count($data['noliq']) > 0;
        $this->_tvars['lowitems'] = count($data['lowitems']) > 0;
        $this->_tvars['highitems'] = count($data['highitems']) > 0;
    }

    public function doclistOnRow($row)
    {
        $item = $row->getDataItem();
        $item = $item->cast();

        $row->add(new Label('number', $item->document_number));
        $row->add(new Label('date', date('d-m-Y', $item->document_date)));
        $row->add(new Label('type', $item->meta_desc));

        $date = new Carbon();
        $start = $date->startOfDay()->timestamp;
        $end = $date->endOfDay()->timestamp;

        if ($item->document_date < $start) {
            $row->setAttribute('class', 'alert alert-danger');
        }
        if ($item->document_date >= $start && $item->document_date <= $end) {
            $row->setAttribute('class', 'alert alert-warning');
        }
    }

    public function noliqlistOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('noliqitem', $item->storename));
        $row->add(new Label('noliqstore', $item->itemname));
        $row->add(new Label('now', $item->now));
    }

    public function lowlistOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('lowdate', date('d.m.Y', $item->date)));
        $row->add(new Label('lowitem', $item->itemname));
        $row->add(new Label('lowstore', $item->storename));
        $row->add(new Label('lowqty', " " . $item->quantity));
        $row->add(new Label('lowneed', $item->norma));

        $row->setAttribute('class', 'alert alert-warning');

        if ($item->quantity <= 0) {
            $row->setAttribute('class', 'alert alert-danger');
        }
    }

    public function highlistOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('highdate', date('d.m.Y', $item->date)));
        $row->add(new Label('highitem', $item->itemname));
        $row->add(new Label('highstore', $item->storename));
        $row->add(new Label('highqty', " " . $item->quantity));
        $row->add(new Label('highneed', $item->norma));

        $row->setAttribute('class', 'alert alert-warning');

        if ($item->quantity > $item->norma * 2) {
            $row->setAttribute('class', 'alert alert-danger');
        }
    }

    private function getData()
    {
        $data = array();


        $conn = $conn = \ZDB\DB::getConnect();


        // список  запланированных документов
        $where = "state= " . Document::STATE_EXECUTED;
        $where = $where . " and  document_date >= " . $conn->DBDate(strtotime('-1 week'));
        $where = $where . " and  meta_name in ('RetailIssue','GoodsIssue','GoodsReceipt','MoveItem') ";
        $where = $where . " and  content like '%<plan>1</plan>%'";

        $data['planned'] = Document::find($where);


        // средние  продажи  за последний месяц
        $sql = "select sc.item_id,sc.`itemname`,sc.`storename`,sc.store_id, coalesce( sum( case when sc.quantity <0 then  0-sc.quantity else 0  end),0)  as quantity
               from   `erp_account_subconto_view` sc
               where sc.document_date >" . $conn->DBDate(strtotime('- 30 day')) . " and document_date <  " . $conn->DBDate(time()) . "
               and sc.store_id not in (select s.store_id from erp_store s where  s.store_type=" . \ZippyERP\ERP\Entity\Store::STORE_TYPE_RET_SUM . " )
               group  by sc.item_id,sc.`itemname`,sc.`storename`,sc.store_id
                  ";

        $rs = $conn->Execute($sql);

        $data['noliq'] = array(); //неликвидный  товар

        $avritems = array();

        foreach ($rs as $row) {
            if ($row['quantity'] == 0) {
                $data['noliq'][$row['item_id'] . '_' . $row['store_id']] = new DataItem($row);
            } else {
                $row['quantity'] = $row['quantity'] / 30;
                $avritems[$row['item_id'] . '_' . $row['store_id']] = $row;
            }
        }


        // планируемые  закупки  на следующую неделю

        $planned = array();

        $sql = "select sc.item_id,sc.document_date,  sc.store_id, coalesce( sum( sc.quantity),0)  as quantity
               from    `erp_account_subconto_view` sc
               where sc.document_date >= " . $conn->DBDate(time()) . " and document_date <  " . $conn->DBDate(strtotime('+ 7 day')) . "
               and sc.store_id not in (select s.store_id from erp_store s where  s.store_type=" . \ZippyERP\ERP\Entity\Store::STORE_TYPE_RET_SUM . " )
               and quantity > 0
               group  by sc.item_id, sc.store_id,sc.document_date ";

        $rs = $conn->Execute($sql);


        foreach ($rs as $row) {
            $row['document_date'] = strtotime($row['document_date']);
            $planned[$row['item_id'] . '_' . $row['store_id'] . '_' . $row['document_date']] = $row;
        }


        $lowitems = array(); //нехватка
        $highitems = array(); //затоваривание
        //остатки на  сегодня
        $sql = "select  item_id, store_id,itemname,storename , coalesce( sum(quantity),0)  as quantity
                from     `erp_account_subconto_view`
                where item_id >0 and   document_date <  " . $conn->DBDate(time()) . "
                group  by  item_id,  store_id,itemname,storename
                order  by  itemname ";


        $rs = $conn->Execute($sql);
        foreach ($rs as $row) {
            if (isset($data['noliq'][$row['item_id'] . '_' . $row['store_id']])) {
                $data['noliq'][$row['item_id'] . '_' . $row['store_id']]->now = $row['quantity'];
                continue;
            }
            if (isset($avritems[$row['item_id'] . '_' . $row['store_id']])) {
                $avr = $avritems[$row['item_id'] . '_' . $row['store_id']];
                $norma = $avr['quantity'] * 1.2;  //запас  20%
                if ($norma < 1)
                    $norma = 1;
                $prev = $row['quantity'];
                for ($i = 1; $i <= 5; $i++) {
                    $it = new DataItem($row);
                    $it->quantity = $prev - $avr['quantity'];
                    $it->date = Carbon::now()->addDays($i)->startOfDay()->timestamp;

                    if (isset($planned[$row['item_id'] . '_' . $row['store_id'] . '_' . $it->date])) {
                        $it->quantity += $planned[$row['item_id'] . '_' . $row['store_id'] . '_' . $it->date]['quantity'];
                    }
                    $prev = $it->quantity;
                    $it->quantity = round($it->quantity);
                    $it->state = 0;
                    $it->norma = $norma;
                    if ($it->quantity < $norma / 2) {

                        $lowitems[] = $it;
                    }


                    if ($it->quantity > $norma * 1.5) {
                        $highitems[] = $it;
                    }
                }

                continue;
            }
        }

        $lowitems = \Pinq\Traversable::from($lowitems)
                        ->orderByAscending(function ($row) {
                            return $row->date;
                        })
                        ->thenByAscending(function ($row) {
                            return $row->itemname;
                        })
                        ->thenByAscending(function ($row) {
                            return $row->storename;
                        })
                        ->select(function ($row) {
                            return $row;
                        })->asArray();

        $highitems = \Pinq\Traversable::from($highitems)
                        ->orderByAscending(function ($row) {
                            return $row->date;
                        })
                        ->thenByAscending(function ($row) {
                            return $row->itemname;
                        })
                        ->thenByAscending(function ($row) {
                            return $row->storename;
                        })
                        ->select(function ($row) {
                            return $row;
                        })->asArray();


        $data['lowitems'] = $lowitems;
        $data['highitems'] = $highitems;

        return $data;
    }

    /**
     * @see base
     *
     */
    public function getPageInfo()
    {
        return "Статистика на  начало дня";
    }

}
