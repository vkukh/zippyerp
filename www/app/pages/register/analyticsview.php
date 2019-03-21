<?php

namespace App\Pages\Register;

use Zippy\Html\DataList\Column;
use Zippy\Html\Form\CheckBox;
use Zippy\Html\Form\Date;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\TextInput;
use \App\Helper as H;

class AnalyticsView extends \App\Pages\Base
{

    public $datalist;

    public function __construct() {
        parent::__construct();

        $this->_tvars['list'] = true;


        $this->add(new Form('filter'))->onSubmit($this, 'OnSubmit');
        $this->filter->add(new Date('from', time() - (7 * 24 * 3600)));
        $this->filter->add(new Date('to', time()));
        $this->filter->add(new Date('sdate', time()))->setVisible(false);
        $this->filter->add(new DropDownChoice('viewtype'))->onChange($this, 'typeOnClick');
        ;
        $this->filter->viewtype->setValue(1);
        $this->filter->add(new TextInput('searchkey'));

        $this->filter->add(new CheckBox('chdoc'));
        $this->filter->add(new CheckBox('chcustomer'));
        $this->filter->add(new CheckBox('chos'));
        $this->filter->add(new CheckBox('chitems'));
        $this->filter->add(new CheckBox('chacc'));
        $this->filter->add(new CheckBox('chemployee'));
        $this->filter->add(new CheckBox('chservice'));

        $this->filter->add(new DropDownChoice('groupby'));
        $this->filter->groupby->setValue(1);

        $this->datalist = $this->add(new \Zippy\Html\DataList\DataTable("tlist", new AWDataSource($this), true, true));

        //$this->datalist->setCellDrawEvent($this, 'OnDrawCell');
        $this->datalist->setCellClickEvent($this, 'OnClickCell');
        $this->datalist->setSelectedClass('active');
        $this->datalist->setPageSize(25);


        $this->add(new \App\Widgets\DocView('docview'))->setVisible(false);
    }

    public function typeOnClick($sender) {


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

    public function OnSubmit($sender) {


        $this->datalist->removeAllColumns();

        if ($this->filter->viewtype->getValue() == 1) {
            if ($this->filter->chdoc->isChecked()) {
                $this->datalist->addColumn(new Column('document_number', 'Док. номер', true, true, true));
            }
            if ($this->filter->chcustomer->isChecked())
                $this->datalist->addColumn(new Column('customer_name', 'Контрагент', true, true));
            if ($this->filter->chitems->isChecked()) {
                $this->datalist->addColumn(new Column('itemname', 'ТМЦ', true, true));
                // $this->datalist->addColumn(new Column('partion', 'Партия', false, true, false, '', 'text-right'));
            };
            if ($this->filter->chos->isChecked())
                $this->datalist->addColumn(new Column('osname', 'ОС и НМА', true, true));
            if ($this->filter->chservice->isChecked())
                $this->datalist->addColumn(new Column('service_name', 'Услуга', true, true));
            if ($this->filter->chemployee->isChecked())
                $this->datalist->addColumn(new Column('employee_name', 'Сотрудник', true, true));;
            if ($this->filter->chacc->isChecked())
                $this->datalist->addColumn(new Column('acc_code', 'Счет', true, true));;

            $this->datalist->addColumn(new Column('da', 'Сумма, Дт', false, true, false, ''));
            $this->datalist->addColumn(new Column('ca', 'Сумма, Кт', false, true, false, ''));
            $this->datalist->addColumn(new Column('dq', 'Кол. Дт', false, true, false, ''));
            $this->datalist->addColumn(new Column('cq', 'Кол. Кт', false, true, false, ''));
        }

        if ($this->filter->viewtype->getValue() > 1) {
            if ($this->filter->groupby->getValue() == 1)
                $this->datalist->addColumn(new Column('acc_code', 'Счет', true, true));
            if ($this->filter->groupby->getValue() == 2)
                $this->datalist->addColumn(new Column('customer_name', 'Контрагент', true, true));
            if ($this->filter->groupby->getValue() == 3)
                $this->datalist->addColumn(new Column('itemname', 'ТМЦ', true, true));
            if ($this->filter->groupby->getValue() == 4)
                $this->datalist->addColumn(new Column('osname', 'ОС и НМА', true, true));
            if ($this->filter->groupby->getValue() == 5)
                $this->datalist->addColumn(new Column('service_name', 'Услуга', true, true));
            if ($this->filter->groupby->getValue() == 6)
                $this->datalist->addColumn(new Column('employee_name', 'Сотрудник', true, true));;
            if ($this->filter->groupby->getValue() == 7)
                $this->datalist->addColumn(new Column('dоcument_numbet', 'Документ ', true, true));;

            $this->datalist->addColumn(new Column('da', 'Сумма, Дт', false, true, false, ''));
            $this->datalist->addColumn(new Column('ca', 'Сумма, Кт', false, true, false, ''));
            $this->datalist->addColumn(new Column('dq', 'Кол. Дт', false, true, false, ''));
            $this->datalist->addColumn(new Column('cq', 'Кол. Кт', false, true, false, ''));
        }


        $this->datalist->Reload();
        $this->datalist->setVisible(true);
        $this->docview->setVisible(false);
    }

    public function OnClickCell($sender, $data) {
        $item = $data['dataitem'];
        $this->docview->setDoc(\App\Entity\Doc\Document::load($item->document_id));

        $this->docview->setVisible(true);
    }

}

class AWDataSource implements \Zippy\Interfaces\DataSource
{

    private $page;

    private function getSQL() {
        $conn = \ZDB\DB::getConnect();

        if ($this->page->filter->viewtype->getValue() == 1) {
            $sql = " from entrylist_view where document_date >= " . $conn->DBDate($this->page->filter->from->getDate()) . " and  document_date <= " . $conn->DBDate($this->page->filter->to->getDate());
            $searchkey = $this->page->filter->searchkey->getText();
            if (strlen(trim($searchkey)) > 0) {
                $searchkey = "%{$searchkey}%";
                $sql = $sql . " and (1=1 ";
                if ($this->page->filter->chitems->isChecked())
                    $sql = $sql . " or itemname like " . $conn->qstr($searchkey);
                if ($this->page->filter->chcustomer->isChecked())
                    $sql = $sql . " or customer_name like " . $conn->qstr($searchkey);
                if ($this->page->filter->chos->isChecked())
                    $sql = $sql . " or osname like " . $conn->qstr($searchkey);
                if ($this->page->filter->chdoc->isChecked())
                    $sql = $sql . " or document_number like " . $conn->qstr($searchkey);
                if ($this->page->filter->chemployee->isChecked())
                    $sql = $sql . " or employee_name like " . $conn->qstr($searchkey);
                if ($this->page->filter->chservice->isChecked())
                    $sql = $sql . " or service_name like " . $conn->qstr($searchkey);

                $sql = $sql . " )";
            }
        }


        if ($this->page->filter->viewtype->getValue() == 2) {
            $sql = "select " . $this->getFields() . " ,  sum(da)   as da,sum(ca) as ca,sum(dq) as dq, sum(cq) as cq from entrylist_view where document_date >= " . $conn->DBDate($this->page->filter->from->getDate()) . " and  document_date <= " . $conn->DBDate($this->page->filter->to->getDate());
            $sql = $sql . " and  " . $this->getFields() . " is not null  group by " . $this->getFields();
        }
        if ($this->page->filter->viewtype->getValue() == 3) {
            $sql = "select " . $this->getFields() . ",
                   case when sum(da-ca) >0 then  sum(da-ca)  else   0   end   as da,
                   case when sum(da-ca) <0 then  0-sum(da-ca)  else    0   end as ca,
                   case when sum(dq-cq) >0 then   sum(dq-cq)  else  0    end as dq,
                   case when sum(dq-cq) <0 then   0-sum(dq-cq)  else   0   end as cq 
                   from  entrylist_view where document_date < " . $conn->DBDate($this->page->filter->sdate->getDate());
            $sql = $sql . " and  " . $this->getFields() . "   group by " . $this->getFields();
        }

        return $sql;
    }

    private function getFields() {
        $fields = "";
        if ($this->page->filter->viewtype->getValue() == 1) {

            $fields .= " ,";
            if ($this->page->filter->chcustomer->isChecked())
                $fields .= "customer_name,";
            if ($this->page->filter->chitems->isChecked())
                $fields .= "itemname,acc_code,";
            if ($this->page->filter->chos->isChecked())
                $fields .= "osname,";
            if ($this->page->filter->chservice->isChecked())
                $fields .= "service_name,";
            if ($this->page->filter->chemployee->isChecked())
                $fields .= "employee_name,";
            if ($this->page->filter->chdoc->isChecked())
                $fields .= "document_number,";
        } else {
            $v = $this->page->filter->groupby->getValue();
            if ($v == 1)
                $fields = " acc_code ";
            if ($v == 2)
                $fields = " customer_name ";
            if ($v == 3)
                $fields = " itemname ";
            if ($v == 4)
                $fields = " osname ";
            if ($v == 5)
                $fields = " service_name ";
            if ($v == 6)
                $fields = " employee_name ";
            if ($v == 7)
                $fields = " document_number ";
            if (strlen($fields) == 0)
                $fields = " acc_code ";
        }
        return $fields;
    }

    public function __construct($page) {
        $this->page = $page;
    }

    public function getItemCount() {
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

    public function getItems($start, $count, $sortfield = null, $asc = null) {
        $conn = \ZDB\DB::getConnect();

        if ($this->page->filter->viewtype->getValue() == 1) {
            $sql = "select  document_id,document_date" . $this->getFields() . "
                      da, ca, dq, cq  " . $this->getSQL();
        }
        if ($this->page->filter->viewtype->getValue() > 1) {
            $sql = $this->getSQL();
        }

        if (strlen($sortfield) > 0) {
            $order = " order  by {$sortfield} {$asc} ";
        }
        $sql .= " {$order} limit {$start}, {$count}";
        //H::log($sql);
        $list = array();
        $rs = $conn->Execute($sql);
        foreach ($rs as $row) {
            $item = new \App\DataItem($row);
            $item->da = H::famt($item->da);
            $item->ca = H::famt($item->ca);
            $item->dq = H::fqty($item->dq);
            $item->cq = H::fqty($item->cq);
            $list[] = $item;
        }

        return $list;
    }

    public function getItem($id) {
        
    }

}
