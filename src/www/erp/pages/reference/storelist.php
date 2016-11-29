<?php

namespace ZippyERP\ERP\Pages\Reference;

use Zippy\Html\DataList\DataView;
use Zippy\Html\DataList\Paginator;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextArea;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Panel;
use ZippyERP\ERP\Entity\Stock;
use ZippyERP\ERP\Entity\Store;
use ZippyERP\ERP\Helper as H;

class StoreList extends \ZippyERP\System\Pages\Base
{

    public $_store = null;

    public function __construct()
    {
        parent::__construct();

        $storepanel = $this->add(new Panel('storetable'));
        $storepanel->add(new DataView('storelist', new \ZCL\DB\EntityDataSource('\ZippyERP\ERP\Entity\Store'), $this, 'storelistOnRow'));
        $storepanel->add(new ClickLink('storeadd'))->setClickHandler($this, 'storeaddOnClick');
        $this->add(new Form('storeform'))->setVisible(false);
        $this->storeform->add(new TextInput('storeeditname'));
        $this->storeform->add(new TextArea('storeeditdesc'));
        $this->storeform->add(new DropDownChoice('storeedittype'));
        $this->storeform->add(new SubmitButton('storesave'))->setClickHandler($this, 'storesaveOnClick');
        $this->storeform->add(new Button('storecancel'))->setClickHandler($this, 'storecancelOnClick');
        $itempanel = $this->add(new Panel('itemtable'));
        $itempanel->setVisible(false);
        $itempanel->add(new DataView('itemlist', new StockDataSource($this), $this, 'itemlistOnRow'));
        $itempanel->itemlist->setPageSize(10);
        $itempanel->add(new Paginator('pag', $itempanel->itemlist));
        $storepanel->storelist->Reload();
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
        $this->storeform->storeedittype->setValue($this->_store->store_type);
    }

    public function storedeleteOnClick($sender)
    {
        try {
            Store::delete($sender->owner->getDataItem()->store_id);
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
        $this->_store->store_type = $this->storeform->storeedittype->getValue();
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
        $store = $sender->owner->getDataItem();
        $this->storetable->storelist->setSelectedRow($store->store_id);
        $this->storetable->storelist->Reload();
        $this->_store = $store;
        $this->itemtable->setVisible(true);
        $this->itemtable->itemlist->Reload();
    }

    public function itemlistOnRow($row)
    {
        $item = $row->getDataItem();

        $row->add(new Label('itemname', $item->itemname));
        $row->add(new Label('measure', $item->measure_name));
        $row->add(new Label('price', $item->price > 0 ? H::fm($item->price) : ''));
        $qty = Stock::getQuantity($item->stock_id, time());
        $f = Stock::getQuantityFuture($item->stock_id, time());
        $row->add(new Label('quantity', $qty / 1000));
        $row->add(new Label('quantityw', $f['w'] / 1000));
        $row->add(new Label('quantityr', $f['r'] / 1000));
        $row->add(new ClickLink('pcancel'))->setClickHandler($this, 'partionOnClick');
        $row->pcancel->setVisible(false);

        if ($qty == 0 && $f['w'] == 0 && $f['r'] == 0) {
            $row->pcancel->setVisible(true);
        }
    }

    // отключаем  неитспользуемую  партию
    public function partionOnClick($sender)
    {
        $item = $sender->getOwner()->getDataItem();
        $item->closed = 1;
        $item->Save();
        $this->itemtable->itemlist->Reload();
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
        return Stock::findCnt("closed  <> 1 and store_id=" . $this->page->_store->store_id);
    }

    public function getItems($start, $count, $sortfield = null, $asc = null)
    {
        return Stock::find("closed  <> 1 and store_id=" . $this->page->_store->store_id, "itemname asc", $count, $start);
    }

    public function getItem($id)
    {
        return Stock::load($id);
    }

}
