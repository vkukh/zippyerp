<?php

namespace ZippyERP\Shop\Pages;

use \ZippyERP\Shop\Helper;
use \ZippyERP\System\System;
use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Label;

class Orderlist extends Base
{

    public $currentorder = null;
    public $productlist = array();

    public function __construct() {
        parent::__construct();


        $datalist = $this->add(new DataView("orderlist", new \ZCL\DB\EntityDataSource("\\ZippyERP\\Shop\\Entity\\Order", "", "order_id desc"), $this, "onOrderRow"));
        $datalist->setSelectedClass('info');
        $datalist->setPageSize(10);
        $this->add(new \Zippy\Html\DataList\Paginator("pag", $datalist));
        $datalist->Reload();

        $this->add(new \Zippy\Html\Panel('details'))->setVisible(false);
        $this->details->add(new Label('contacts'));
        $orderform = $this->details->add(new \Zippy\Html\Form\Form('orderform'));


        $orderform->add(new \Zippy\Html\Form\TextArea('comment'));
        $orderform->add(new \Zippy\Html\Form\SubmitButton('inprocess'))->onClick($this, 'OnSubmit');
        $orderform->add(new \Zippy\Html\Form\SubmitButton('close'))->onClick($this, 'OnSubmit');
        $orderform->add(new \Zippy\Html\Form\SubmitButton('cancel'))->onClick($this, 'OnSubmit');

        $this->details->add(new DataView("orderdetaillist", new \Zippy\Html\DataList\ArrayDataSource($this, 'productlist'), $this, "onDetailRow"));
    }

    public function onOrderRow($row) {
        $item = $row->getDataItem();

        $row->add(new Label("order_id", $item->order_id));
        $row->add(new Label("created", date("Y-m-d", $item->created)));
        $row->add(new Label("amount", Helper::fm($item->amount)));
        $row->add(new Label("comment", $item->comment));
        $row->add(new \Zippy\Html\Link\ClickLink("edit", $this, "OnEdit"));

        $row->add(new Label("statusnew"))->setVisible($item->status == 0);
        $row->add(new Label("statusinprocess"))->setVisible($item->status == 1);
        $row->add(new Label("statusclosed"))->setVisible($item->status == 2);
        $row->add(new Label("statuscancel"))->setVisible($item->status == 3);
    }

    public function onDetailRow($row) {
        $item = $row->getDataItem();

        $row->add(new Label("code", $item->product_id));
        $row->add(new Label("qty", $item->quantity));
        $row->add(new Label("price", Helper::fm($item->price)));
        $row->add(new \Zippy\Html\Link\BookmarkableLink("name", "/sp/" . $item->product_id))->setValue($item->productname);
    }

    public function OnEdit($sender) {
        $this->currentorder = $sender->getOwner()->getDataItem();

        $this->details->setVisible(true);
        $this->orderlist->setSelectedRow($sender->getOwner());
        $this->details->contacts->setText($this->currentorder->description);
        $this->details->orderform->comment->setText($this->currentorder->comment);

        $this->productlist = \ZippyERP\Shop\Entity\OrderDetail::find('order_id=' . $this->currentorder->order_id);
        $this->orderlist->Reload();
        $this->details->orderdetaillist->Reload();
    }

    public function OnSubmit($sender) {
        $this->currentorder->comment = $this->details->orderform->comment->getText();

        if ($sender->id == 'inprocess') {
            $this->currentorder->status = 1;
        }
        if ($sender->id == 'close') {
            $this->currentorder->status = 2;
            $this->currentorder->closed = time();
        }
        if ($sender->id == 'cancel') {
            $this->currentorder->status = 3;
        }

        $this->currentorder->Save();
        $this->orderlist->Reload();
    }

    public function beforerender() {
        if ($this->currentorder != null) {
            if ($this->currentorder->status > 0) {
                $this->details->orderform->inprocess->setVisible(false);
            } else {
                $this->details->orderform->inprocess->setVisible(true);
            }

            if ($this->currentorder->status > 1) {
                $this->details->orderform->setVisible(false);
            } else {
                $this->details->orderform->setVisible(true);
            }
        }
    }

}
