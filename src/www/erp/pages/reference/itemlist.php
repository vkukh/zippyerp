<?php

namespace ZippyERP\ERP\Pages\Reference;

use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Panel;
use ZippyERP\ERP\Entity\Item;

class ItemList extends \ZippyERP\ERP\Pages\Base
{

    private $_item;

    public function __construct() {
        parent::__construct();

        $this->add(new Form('filter'))->onSubmit($this, 'OnSubmit');
        $this->filter->add(new TextInput('searchkey'));
        $this->filter->add(new DropDownChoice('stype', \ZippyERP\ERP\Entity\Item::getTMZList()))->setValue(Item::ITEM_TYPE_STUFF);



        $this->add(new Panel('itemtable'))->setVisible(true);
        $this->itemtable->add(new DataView('itemlist', new ItemDataSource($this), $this, 'itemlistOnRow'))->Reload();
        $this->itemtable->add(new ClickLink('addnew'))->onClick($this, 'addOnClick');
        $this->itemtable->itemlist->setPageSize(25);
        $this->itemtable->add(new \Zippy\Html\DataList\Paginator('pag', $this->itemtable->itemlist));
        $this->itemtable->itemlist->reload();

        $this->add(new \ZippyERP\ERP\Blocks\Item('itemdetail', $this, 'OnDetail'))->setVisible(false);
    }

    public function itemlistOnRow($row) {
        $item = $row->getDataItem();
        $row->add(new Label('itemname', $item->itemname));
        $row->add(new Label('measure', $item->measure_name));

        $row->add(new Label('code', $item->item_code));
        $row->add(new ClickLink('edit'))->onClick($this, 'editOnClick');
        $row->add(new ClickLink('delete'))->onClick($this, 'deleteOnClick');
    }

    public function deleteOnClick($sender) {
        //проверка на партии
        if (false == Item::delete($sender->owner->getDataItem()->item_id)) {
            $this->setError("Не можна видаляти цей  товар");
            return;
        }
        $this->itemtable->itemlist->Reload();
    }

    public function editOnClick($sender) {
        $item = $sender->owner->getDataItem();
        $this->itemtable->setVisible(false);
        $this->itemdetail->open($item);
    }

    public function addOnClick($sender) {
        $this->itemtable->setVisible(false);
        $this->itemdetail->open();
    }

    /**
     * вызывается  блоком  редактирования
     *
     * @param mixed true если cancel
     */
    public function OnDetail($cancel = false) {
        $this->itemtable->setVisible(true);
        $this->itemtable->itemlist->Reload(false);
    }

    public function OnSubmit($sender) {
        $this->itemtable->itemlist->Reload();
    }

}

class ItemDataSource implements \Zippy\Interfaces\DataSource
{

    private $page;

    public function __construct($page) {
        $this->page = $page;
    }

    private function getWhere() {

        $form = $this->page->filter;
        $where = "item_type   = " . $form->stype->getValue();

        if (strlen($form->searchkey->getText()) > 0) {
            $where = $where . " and (itemname like " . Item::qstr('%' . $form->searchkey->getText() . '%') . " or item_code like " . Item::qstr('%' . $form->searchkey->getText() . '%') . " )  ";
        }
        return $where;
    }

    public function getItemCount() {
        return Item::findCnt($this->getWhere());
    }

    public function getItems($start, $count, $sortfield = null, $asc = null) {
        return Item::find($this->getWhere(), "itemname asc", $count, $start);
    }

    public function getItem($id) {
        return Item::load($id);
    }

}
