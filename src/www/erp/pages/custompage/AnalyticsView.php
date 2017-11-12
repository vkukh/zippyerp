<?php

namespace ZippyERP\ERP\Pages\CustomPage;

use Zippy\Html\DataList\Column;
use Zippy\Html\Form\CheckBox;
use Zippy\Html\Form\Date;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\TextInput;

class AnalyticsView extends \ZippyERP\ERP\Pages\Base
{

    public $datalist;

    public function __construct()
    {
        parent::__construct();

        $this->_tvars['list'] = true;


        $dt = new \Carbon\Carbon;
        $dt->subMonth();
        $from = $dt->startOfMonth()->timestamp;
        $to = $dt->endOfMonth()->timestamp;

        $this->add(new Form('filter'))->onSubmit($this, 'OnSubmit');
        $this->filter->add(new Date('from', $from));
        $this->filter->add(new Date('to', $to));
        $this->filter->add(new Date('sdate', time()))->setVisible(false);
        $this->filter->add(new DropDownChoice('viewtype'))->onChange($this, 'typeOnClick');
        ;
        $this->filter->viewtype->setValue(1);
        $this->filter->add(new TextInput('searchkey'));
        $this->filter->add(new CheckBox('chaccount'));
        $this->filter->add(new CheckBox('chcustomer'));
        $this->filter->add(new CheckBox('chos'));
        $this->filter->add(new CheckBox('chitems'));
        $this->filter->add(new CheckBox('chstore'));
        $this->filter->add(new CheckBox('chemployee'));
        $this->filter->add(new CheckBox('chmoneyfund'));

        $this->filter->add(new DropDownChoice('groupby'));
        $this->filter->groupby->setValue(1);

        $this->datalist = $this->add(new \Zippy\Html\DataList\DataTable("tlist", new AWDataSource($this), true, true));

        //$this->datalist->setCellDrawEvent($this, 'OnDrawCell');
        $this->datalist->setCellClickEvent($this, 'OnClickCell');
        $this->datalist->setCellDrawEvent($this, 'OnDrowCell');
        $this->datalist->setSelectedClass('active');
        $this->datalist->setPageSize(10);


        $this->add(new \ZippyERP\ERP\Blocks\DocView('docview'))->setVisible(false);
    }

    public function typeOnClick($sender)
    {


        if ($this->filter->viewtype->getValue() == 1) {
            $this->filter->to->setVisible(true);
            $this->filter->from->setVisible(true);
            $this->filter->sdate->setVisible(false);
            $this->_tvars['list'] = true;
        } else {
            $this->filter->to->setVisible(false);
            $this->filter->from->setVisible(false);
            $this->filter->sdate->setVisible(true);
            $this->_tvars['list'] = false;
        }


        $this->datalist->setVisible(false);
        $this->docview->setVisible(false);
    }

    public function OnSubmit($sender)
    {


        $this->datalist->removeAllColumns();

        if ($this->filter->viewtype->getValue() == 1) {
            $this->datalist->addColumn(new Column('document_date', 'Док. дата', true, true, true));
            $this->datalist->addColumn(new Column('document_number', 'Док. номер', true, true, true));
            $this->datalist->addColumn(new Column('meta_desc', 'Док. тип', true, true, true));
            if ($this->filter->chaccount->isChecked())
                $this->datalist->addColumn(new Column('account_id', 'Счет', true, true));
            if ($this->filter->chcustomer->isChecked())
                $this->datalist->addColumn(new Column('customer_name', 'Контрагент', true, true));
            if ($this->filter->chitems->isChecked()) {
                $this->datalist->addColumn(new Column('itemname', 'ТМЦ', true, true));
                $this->datalist->addColumn(new Column('partion', 'Партии', false, true, false, '', 'text-right'));
            };
            if ($this->filter->chos->isChecked())
                $this->datalist->addColumn(new Column('osname', 'ОС и НМА', true, true));
            if ($this->filter->chstore->isChecked())
                $this->datalist->addColumn(new Column('storename', 'Склад', true, true));
            if ($this->filter->chemployee->isChecked())
                $this->datalist->addColumn(new Column('employee_name', 'Сотрудник', true, true));;
            if ($this->filter->chmoneyfund->isChecked())
                $this->datalist->addColumn(new Column('moneyfundname', 'Ден. счет', true, true));;
            if ($this->filter->chaccount->isChecked())
                $this->datalist->addColumn(new Column('extcode', 'Доп. код', false, true, false));
            $this->datalist->addColumn(new Column('da', 'Сумма, Дт', false, true, false, '', 'text-right'));
            $this->datalist->addColumn(new Column('ca', 'Сумма, Кт', false, true, false, '', 'text-right'));
            $this->datalist->addColumn(new Column('dq', 'Кол. Дт', false, true, false, '', 'text-right'));
            $this->datalist->addColumn(new Column('cq', 'Кол. Кт', false, true, false, '', 'text-right'));
        }

        if ($this->filter->viewtype->getValue() > 1) {
            if ($this->filter->groupby->getValue() == 1)
                $this->datalist->addColumn(new Column('account_id', 'Счет', true, true));
            if ($this->filter->groupby->getValue() == 2)
                $this->datalist->addColumn(new Column('customer_name', 'Контрагент', true, true));
            if ($this->filter->groupby->getValue() == 3)
                $this->datalist->addColumn(new Column('itemname', 'ТМЦ', true, true));
            if ($this->filter->groupby->getValue() == 4)
                $this->datalist->addColumn(new Column('osname', 'ОС и НМА', true, true));
            if ($this->filter->groupby->getValue() == 5)
                $this->datalist->addColumn(new Column('storename', 'Склад', true, true));
            if ($this->filter->groupby->getValue() == 6)
                $this->datalist->addColumn(new Column('employee_name', 'Сотрудник', true, true));;
            if ($this->filter->groupby->getValue() == 7)
                $this->datalist->addColumn(new Column('moneyfundname', 'Ден. счета', true, true));;
            $this->datalist->addColumn(new Column('da', 'Сумма, Дт', false, true, false, '', 'text-right'));
            $this->datalist->addColumn(new Column('ca', 'Сумма, Кт', false, true, false, '', 'text-right'));
            $this->datalist->addColumn(new Column('dq', 'Кол. Дт', false, true, false, '', 'text-right'));
            $this->datalist->addColumn(new Column('cq', 'Кол. Кт', false, true, false, '', 'text-right'));
        }


        $this->datalist->Reload();
        $this->datalist->setVisible(true);
        $this->docview->setVisible(false);
    }

    public function OnClickCell($sender, $data)
    {
        $item = $data['dataitem'];
        $this->docview->setDoc(\ZippyERP\ERP\Entity\Doc\Document::load($item->document_id));

        $this->docview->setVisible(true);
    }

    public function OnDrowCell($sender, $data)
    {
        $item = $data['dataitem'];
        if ($data['field'] != 'extcode')
            return null;
        if ($item->extcode > 0) {
            $acc = $item->account_id;
            if ($acc == 285) {
                $store = \ZippyERP\ERP\Entity\Store::load($item->extcode);
                if ($store != null) {
                    return $store->storename;
                }
            }
            if ($acc == 281) {
                return \ZippyERP\ERP\Helper::fm($item->extcode);
            }

            $clist = \ZippyERP\ERP\Consts::getCodesList();
            return $clist[$item->extcode];
        } else {
            return "";
        }
    }

}

class AWDataSource implements \Zippy\Interfaces\DataSource
{

    private $page;

    private function getSQL()
    {
        $conn = \ZDB\DB::getConnect();

        if ($this->page->filter->viewtype->getValue() == 1) {
            $sql = " from erp_account_subconto_view where document_date >= " . $conn->DBDate($this->page->filter->from->getDate()) . " and  document_date <= " . $conn->DBDate($this->page->filter->to->getDate());
            $searchkey = $this->page->filter->searchkey->getText();
            if (strlen(trim($searchkey)) > 0) {
                $searchkey = "%{$searchkey}%";
                $sql = $sql . " and (document_number like " . $conn->qstr($searchkey);
                if ($this->page->filter->chitems->isChecked())
                    $sql = $sql . " or itemname like " . $conn->qstr($searchkey);
                if ($this->page->filter->chcustomer->isChecked())
                    $sql = $sql . " or customer_name like " . $conn->qstr($searchkey);
                if ($this->page->filter->chos->isChecked())
                    $sql = $sql . " or osname like " . $conn->qstr($searchkey);
                if ($this->page->filter->chstore->isChecked())
                    $sql = $sql . " or store_name like " . $conn->qstr($searchkey);
                if ($this->page->filter->chemployee->isChecked())
                    $sql = $sql . " or employee_name like " . $conn->qstr($searchkey);
                if ($this->page->filter->chmoneyfund->isChecked())
                    $sql = $sql . " or moneyfundname like " . $conn->qstr($searchkey);

                $sql = $sql . " or meta_desc like " . $conn->qstr($searchkey) . ")";
            }
        }


        if ($this->page->filter->viewtype->getValue() == 2) {
            $sql = "select " . $this->getFields() . " , cast(sum(da)/100 as  decimal(10,2)) as da,cast(sum(ca)/100 as  decimal(10,2))  as ca,cast(sum(dq)/1000 as  decimal(10,3)) as dq, cast(sum(cq)/1000 as  decimal(10,3)) as cq from erp_account_subconto_view where document_date >= " . $conn->DBDate($this->page->filter->from->getDate()) . " and  document_date <= " . $conn->DBDate($this->page->filter->to->getDate());
            $sql = $sql . " and  " . $this->getFields() . " is not null  group by " . $this->getFields();
        }
        if ($this->page->filter->viewtype->getValue() == 3) {
            $sql = "select " . $this->getFields() . ",
                   case when sum(da-ca) >0 then cast(sum(da-ca)/100 as  decimal(10,2)) else  cast( 0 as  decimal(10,2)) end   as da,
                   case when sum(da-ca) <0 then cast(0-sum(da-ca)/100 as  decimal(10,2)) else  cast( 0 as  decimal(10,2)) end as ca,
                   case when sum(dq-cq) >0 then cast(sum(dq-cq)/1000 as  decimal(10,3)) else  cast( 0 as  decimal(10,3)) end as dq,
                   case when sum(dq-cq) <0 then cast(0-sum(dq-cq)/1000 as  decimal(10,3)) else  cast( 0 as  decimal(10,3)) end as cq
                   from erp_account_subconto_view where document_date < " . $conn->DBDate($this->page->filter->sdate->getDate());
            $sql = $sql . " and  " . $this->getFields() . " is not null group by " . $this->getFields();
        }

        return $sql;
    }

    private function getFields()
    {
        $fields = "";
        if ($this->page->filter->viewtype->getValue() == 1) {

            if ($this->page->filter->chaccount->isChecked())
                $fields .= "account_id,";
            if ($this->page->filter->chcustomer->isChecked())
                $fields .= "customer_name,";
            if ($this->page->filter->chitems->isChecked())
                $fields .= "itemname,";
            if ($this->page->filter->chos->isChecked())
                $fields .= "osname,";
            if ($this->page->filter->chstore->isChecked())
                $fields .= "storename,";
            if ($this->page->filter->chemployee->isChecked())
                $fields .= "employee_name,";
            if ($this->page->filter->chmoneyfund->isChecked())
                $fields .= "moneyfundname,";
        } else {
            $v = $this->page->filter->groupby->getValue();
            if ($v == 1)
                $fields = " account_id ";
            if ($v == 2)
                $fields = " customer_name ";
            if ($v == 3)
                $fields = " itemname ";
            if ($v == 4)
                $fields = " osname ";
            if ($v == 5)
                $fields = " storename ";
            if ($v == 6)
                $fields = " employee_name ";
            if ($v == 7)
                $fields = " moneyfundname ";
            if (strlen($fields) == 0)
                $fields = " account_id ";
        }
        return $fields;
    }

    public function __construct($page)
    {
        $this->page = $page;
    }

    public function getItemCount()
    {
        $conn = \ZDB\DB::getConnect();
        if ($this->page->filter->viewtype->getValue() == 1) {
            $sql = "select count(*) as cnt " . $this->getSQL();
        } else {
            $sql = "select  count(*) as cnt from (" . $this->getSQL() . ") t ";
        }
        if (strlen($sql) == 0)
            return 0;
        return $conn->GetOne($sql);
    }

    public function getItems($start, $count, $sortfield = null, $asc = null)
    {
        $conn = \ZDB\DB::getConnect();

        if ($this->page->filter->viewtype->getValue() == 1) {
            $sql = "select  document_id,document_date,extcode,meta_desc,document_number,partion," . $this->getFields() . "
                     cast(da/100 as decimal(10,2)) as da, cast(ca/100 as decimal(10,2)) as ca,cast(dq/1000 as decimal(10,3)) as dq,cast(cq/1000 as decimal(10,3)) as cq  " . $this->getSQL();
        }
        if ($this->page->filter->viewtype->getValue() > 1) {
            $sql = $this->getSQL();
        }

        if (strlen($sortfield) > 0) {
            $order = " order  by {$sortfield} {$asc} ";
        }
        $sql .= " {$order} limit {$start}, {$count}";

        $list = array();
        $rs = $conn->Execute($sql);
        foreach ($rs as $row) {
            $item = new \ZippyERP\ERP\DataItem($row);
            $list[] = $item;
        }

        return $list;
    }

    public function getItem($id)
    {
        
    }

}
