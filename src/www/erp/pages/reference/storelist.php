<?php

namespace ZippyERP\ERP\Pages\Reference;

use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Label;
use \Zippy\Html\Panel;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\TextArea;
use \Zippy\Html\Form\SubmitButton;
use \Zippy\Html\Form\Button;
 use\ZippyERP\ERP\Entity\Store;
 use\ZippyERP\ERP\Entity\Stock;
use \Zippy\Html\DataList\Paginator;

class StoreList extends \ZippyERP\ERP\Pages\Base
{

        public $_store = null;

        public function __construct()
        {
                parent::__construct();

                $storepanel = $this->add(new Panel('storetable'));
                $storepanel->add(new DataView('storelist', new \ZCL\DB\EntityDataSource('\ZippyERP\ERP\Entity\Store'), $this, 'storelistOnRow'))->Reload();
                $storepanel->add(new ClickLink('storeadd'))->setClickHandler($this, 'storeaddOnClick');
                $this->add(new Form('storeform'))->setVisible(false);
                $this->storeform->add(new TextInput('storeeditname'));
                $this->storeform->add(new TextArea('storeeditdesc'));
                $this->storeform->add(new SubmitButton('storesave'))->setClickHandler($this, 'storesaveOnClick');
                $this->storeform->add(new Button('storecancel'))->setClickHandler($this, 'storecancelOnClick');
                $itempanel = $this->add(new Panel('itemtable'));
                $itempanel->setVisible(false);
                $itempanel->add(new DataView('itemlist', new StockDataSource($this), $this, 'itemlistOnRow'));
                $itempanel->itemlist->setPageSize(10);
                $itempanel->add(new Paginator('pag', $itempanel->itemlist));
        }

        public function storelistOnRow($row)
        {
                $item = $row->getDataItem();

                $row->add(new Label('storename', $item->storename));
                $row->add(new Label('storedesc', $item->description));
                $row->add(new ClickLink('showitem'))->setClickHandler($this, 'showitemOnClick');
                $row->add(new ClickLink('storeedit'))->setClickHandler($this, 'storeeditOnClick');
                $row->add(new ClickLink('storedelete'))->setClickHandler($this, 'storedeleteOnClick');
        }

        public function storeeditOnClick($sender)
        {
                $this->_store = $sender->owner->getDataItem();
                $this->storetable->setVisible(false);
                $this->itemtable->setVisible(false);
                $this->storeform->setVisible(true);
                $this->storeform->storeeditname->setText($this->_store->storename);
                $this->storeform->storeeditdesc->setText($this->_store->description);
        }

        public function storedeleteOnClick($sender)
        {
                try {
                        $b = Store::delete($sender->owner->getDataItem()->store_id);
                } catch (\Exception $e) {
                        $this->setError("Нельзя удалить этот  склад");
                }
                $this->storetable->storelist->Reload();
        }

        public function storeaddOnClick($sender)
        {
                $this->storetable->setVisible(false);
                $this->itemtable->setVisible(false);
                $this->storeform->setVisible(true);
                $this->storeform->storeeditname->setText('');
                $this->storeform->storeeditdesc->setText('');
                $this->_store = new Store();
        }

        public function storesaveOnClick($sender)
        {

                $this->_store->storename = $this->storeform->storeeditname->getText();
                $this->_store->description = $this->storeform->storeeditdesc->getText();
                if ($this->_store->storename == '') {
                        $this->setError("Введите имя");
                        return;
                }

                $this->_store->Save();
                $this->storeform->setVisible(false);
                $this->storetable->setVisible(true);
                $this->storetable->storelist->Reload();
        }

        public function storecancelOnClick($sender)
        {
                $this->storeform->setVisible(false);
                $this->storetable->setVisible(true);
        }

        public function showitemOnClick($sender)
        {
                $item = $sender->owner->getDataItem();
                $this->storetable->storelist->setSelectedRow($item->store_id);
                $this->storetable->storelist->Reload();
                $this->_store = $item;
                $this->itemtable->setVisible(true);
                $this->itemtable->itemlist->Reload();
        }

        public function itemlistOnRow($row)
        {
                $item = $row->getDataItem();

                $row->add(new Label('itemname', $item->itemname));
                $row->add(new Label('measure', $item->measure_name));
                $row->add(new Label('price', $item->price > 0 ? number_format($item->price / 100, 2) : ''));

                $row->add(new Label('quantity', $item->quantity));
        }

}

class StockDataSource implements \Zippy\Interfaces\DataSource
{

        private $page;

        public function __construct($page)
        {
                $this->page = $page;
        }

        public function getItemCount()
        {
                return Stock::findCnt("store_id=" . $this->page->_store->store_id);
        }

        public function getItems($start, $count, $sortfield = null, $asc = null)
        {
                return Stock::find("store_id=" . $this->page->_store->store_id, "itemname", "asc", $start, $count);
        }

        public function getItem($id)
        {
                return Stock::load($image_id);
        }

}