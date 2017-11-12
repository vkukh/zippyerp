<?php

namespace ZippyERP\Shop\Pages;

use \Zippy\Html\Label;
use \Zippy\Html\Image;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\TextArea;
use \ZippyERP\Shop\Helper;
use \ZippyERP\Shop\Basket;
use \Zippy\WebApplication as App;

//страница формирования заказа  пользователя
class Order extends Base
{

    public $sum = 0;
    public $basketlist;

    public function __construct() {
        parent::__construct();
        $this->basketlist = Basket::getBasket()->list;
        $form = $this->add(new Form('listform'));
        $form->onSubmit($this, 'OnUpdate');

        $form->add(new \Zippy\Html\DataList\DataView('pitem', new \Zippy\Html\DataList\ArrayDataSource(new \Zippy\Binding\PropertyBinding($this, 'basketlist')), $this, 'OnAddRow'))->Reload();
        $form->add(new Label('summa', new \Zippy\Binding\PropertyBinding($this, 'sum')));
        $this->OnUpdate($this);
        $form = $this->add(new Form('orderform'));
        $form->add(new TextArea('contact'));
        $form->onSubmit($this, 'OnSave');
    }

    public function OnUpdate($sender) {
        $this->basketlist = Basket::getBasket()->list;
        //$this->listform->pitem->Reload();
        $this->sum = 0;

        $rows = $this->listform->pitem->getDataRows();
        foreach ($rows as $row) {
            $product = $row->GetDataItem();
            if (!is_numeric($product->quantity)) {
                $this->setError('Неверное количество');
                break;
            }

            $this->sum = $this->sum + $product->price * $product->quantity;
            $this->sum = Helper::fm($this->sum);
        }
    }

    public function OnDelete($sender) {
        $product_id = $sender->owner->getDataItem()->product_id;
        Basket::getBasket()->deleteProduct($product_id);
        if (Basket::getBasket()->isEmpty()) {
            App::Redirect("\\ZippyERP\\Shop\\Pages\\Catalog");
        } else {
            $this->OnUpdate($this);
        }
    }

    //формирование  заказа
    public function OnSave($sender) {

        $this->OnUpdate($this);
        $contact = $this->orderform->contact->getText();
        if ($contact == '') {
            $this->setError("Введите контакт");
            return;
        }

        if (count($this->basketlist) == 0)
            return;

        Helper::saveOrder(Basket::getBasket(), $contact);


        $this->orderform->contact->setText('');
        $this->basketlist = array();
        Basket::getBasket()->list = array();

        $this->orderform->setVisible(false);
        $this->listform->setVisible(false);
        $this->setSuccess("Заказ  отправлен");
        $this->addJS("setTimeout(function(){window.location='/' ;}, 2000)");
    }

    public function OnAddRow(\Zippy\Html\DataList\DataRow $datarow) {
        $item = $datarow->getDataItem();
        $datarow->setDataItem($item);
        $datarow->add(new \Zippy\Html\Link\RedirectLink('pname', '\ZippyERP\Shop\Pages\ProductView', $item->product_id))->setValue($item->productname);
        $datarow->add(new Label('price', Helper::fm($item->price)));
        $datarow->add(new TextInput('quantity', new \Zippy\Binding\PropertyBinding($item, 'quantity')));
        $datarow->add(new \Zippy\Html\Link\ClickLink('delete', $this, 'OnDelete'));
        $datarow->add(new Image('photo', "/simage/{$item->iimage_id}/t"));
    }

}
