<?php

namespace ZippyERP\ERP\Pages\CustomPage;

use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Label;
use ZippyERP\ERP\Entity\Store;
use ZippyERP\ERP\Entity\Stock;
use ZippyERP\ERP\Entity\Item;
use ZippyERP\ERP\Helper as H;

class Tovaronstore extends \ZippyERP\ERP\Pages\Base
{

    public $ds = array();

    public function __construct() {
        parent::__construct();

        $this->add(new Form('filter'))->onSubmit($this, 'onFilter');
        $this->filter->add(new DropDownChoice('store', Store::findArray("storename", "")));
        $this->filter->add(new TextInput('fitem'));
        $this->filter->store->selectFirst();

        $this->add(new DataView('clist', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\ArrayPropertyBinding($this, 'ds')), $this, 'OnRow'));
    }

    public function onFilter($sender) {

        $text = trim($this->filter->fitem->getText());
        if (mb_strlen($text) < 3) {
            $this->setError('Введіть не менше троьох символів');
            return;
        };
        $this->ds = array();
        $text = Stock::qstr("%{$text}%");
        $where = "(item_code like {$text}  or itemname like {$text}) and store_id=" . $this->filter->store->getValue();
        $ds = Stock::find($where);
        foreach ($ds as $st) {
            $qty = Stock::getQuantity($st->stock_id, time()) / 1000;

            if ($qty > 0) {
                $st->qty = $qty;
                $this->ds[] = $st;
            }
        }

        $this->clist->Reload();
    }

    public function OnRow($row) {
        $item = $row->getDataItem();
        $_item = Item::load($item->item_id);

        $row->add(new Label('itemname', $item->itemname));
        $row->add(new Label('code', $item->item_code));

        $row->add(new Label('qty', $item->qty));
        $row->add(new Label('price', H::fm($_item->getOptPrice($item->price))));
    }

}
