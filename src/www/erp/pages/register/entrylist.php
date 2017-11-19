<?php

namespace ZippyERP\ERP\Pages\Register;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\Date;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use ZippyERP\ERP\Entity\Entry;
use ZippyERP\System\Filter;
use ZippyERP\ERP\Helper as H;

/**
 * Класс  страницы  журнала  проводок
 */
class EntryList extends \ZippyERP\ERP\Pages\Base
{

    public function __construct()
    {
        parent::__construct();
        $filter = Filter::getFilter("entrylist");
        $this->add(new Form('filter'))->onSubmit($this, 'filterOnSubmit');
        $this->filter->add(new Date('from', strlen($filter->from) > 0 ? $filter->from : time() - (7 * 24 * 3600)));
        $this->filter->add(new Date('to', strlen($filter->to) > 0 ? $filter->to : time()));
        $this->filter->add(new DropDownChoice('dt', \ZippyERP\ERP\Entity\Account::findArrayEx("acc_code not in (select acc_pid  from erp_account_plan)")));
        $this->filter->add(new DropDownChoice('ct', \ZippyERP\ERP\Entity\Account::findArrayEx("acc_code not in (select acc_pid  from erp_account_plan)")));
        if (strlen($filter->dt) > 0)
            $this->filter->dt->setValue($filter->dt);
        if (strlen($filter->ct) > 0)
            $this->filter->ct->setValue($filter->ct);
        $this->add(new DataView('entrylist', new EntryDataSource(), $this, 'entrylistOnRow'));
        $this->add(new \ZippyERP\ERP\Blocks\DocView('docview'))->setVisible(false);
        $this->add(new \Zippy\Html\DataList\Paginator("paginator", $this->entrylist));
        $this->entrylist->setPageSize(10);
        $this->entrylist->setSelectedClass('success');
        $this->entrylist->Reload();
    }

    public function filterOnSubmit($sender)
    {
        $this->docview->setVisible(false);
        $filter = Filter::getFilter("entrylist");
        $filter->from = $this->filter->from->getDate();
        $filter->to = $this->filter->to->getDate(true);
        $filter->dt = $this->filter->dt->getValue();
        $filter->ct = $this->filter->ct->getValue();
        $this->entrylist->Reload();
    }

    public function entrylistOnRow($row)
    {
        $item = $row->getDataItem();
        $row->add(new Label('acc_d', $item->acc_d));
        $row->add(new Label('acc_c', $item->acc_c));
        $row->add(new Label('amount', ($item->amount > 0) ? H::fm($item->amount) : ""));

        $row->add(new Label('created', date('d-m-Y', $item->document_date)));
        $row->add(new ClickLink('show', $this, 'showOnClick'))->setValue($item->meta_desc . ' ' . $item->document_number);
    }

    public function showOnClick($sender)
    {
        $item = $sender->owner->getDataItem();
        $this->docview->setVisible(true);
        $this->docview->setDoc(\ZippyERP\ERP\Entity\Doc\Document::load($item->document_id));
        $this->entrylist->setSelectedRow($sender->getOwner());
        $this->entrylist->Reload();
    }

}

class EntryDataSource implements \Zippy\Interfaces\DataSource
{

    private function getWhere()
    {

        $conn = \ZDB\DB::getConnect();

        $filter = Filter::getFilter("entrylist");

        $where = " date(document_date) >= " . $conn->DBDate($filter->from) . " and  date(document_date) <= " . $conn->DBDate($filter->to);

        if ($filter->dt > 0) {
            $where .= " and (acc_d = " . $filter->dt . " or acc_c = " . $filter->dt . ")";
        }

        if ($filter->ct > 0) {
            $where .= " and (acc_d = " . $filter->ct . " or acc_c = " . $filter->ct . ")";
        }
        return $where;
    }

    public function getItemCount()
    {
        return Entry::findCnt($this->getWhere());
    }

    public function getItems($start, $count, $sortfield = null, $asc = null)
    {
        return Entry::find($this->getWhere(), "document_date  " . $asc, $count, $start);
    }

    public function getItem($id)
    {
        
    }

}
