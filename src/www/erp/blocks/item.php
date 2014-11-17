<?php

namespace ZippyERP\ERP\Blocks;

use \Zippy\Html\Form\Form;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Form\TextArea;
use \Zippy\Html\Label;
use \Zippy\Html\Form\SubmitButton;
use \Zippy\Html\Form\Button;
use \Zippy\Html\Form\DropDownChoice;

/**
 * Виджет для  редатирования  товара
 */
class Item extends \Zippy\Html\PageFragment
{

    private $caller, $callback, $item;

    /**
     * put your comment there...
     * 
     * @param mixed $id    id компонента
     * @param mixed $caller   ссылка на  класс  вызвавшей  страницы
     * @param mixed $callback  имя функции  к  вызвавшей странице  для возврата
     */
    public function __construct($id, $caller, $callback)
    {
        parent::__construct($id);

        $this->caller = $caller;
        $this->callback = $callback;

        $this->add(new Form('itemdetail'));
        $this->itemdetail->add(new TextInput('editname'));
        $this->itemdetail->add(new TextInput('editpriceopt'));
        $this->itemdetail->add(new TextInput('editpriceret'));
        $this->itemdetail->add(new DropDownChoice('editmeasure', \ZippyERP\ERP\Helper::getMeasureList()));
        $this->itemdetail->add(new DropDownChoice('edittype', \ZippyERP\ERP\Entity\Item::getTypeList()));
        $this->itemdetail->add(new DropDownChoice('editgroup', \ZippyERP\ERP\Helper::getItemGroupList()));
        $this->itemdetail->add(new TextInput('editbarcode'));
        $this->itemdetail->add(new TextArea('editdescription'));

        $this->itemdetail->add(new SubmitButton('save'))->setClickHandler($this, 'saveOnClick');
        $this->itemdetail->add(new Button('cancel'))->setClickHandler($this, 'cancelOnClick');
    }

    public function saveOnClick($sender)
    {

        $this->item->itemname = $this->itemdetail->editname->getText();
        $this->item->priceret = 100 * $this->itemdetail->editpriceret->getText();
        $this->item->priceopt = 100 * $this->itemdetail->editpriceopt->getText();



        $this->item->barcode = $this->itemdetail->editbarcode->getText();
        $this->item->description = $this->itemdetail->editdescription->getText();
        $this->item->measure_id = $this->itemdetail->editmeasure->getValue();
        $this->item->group_id = $this->itemdetail->editgroup->getValue();
        $this->item->item_type = $this->itemdetail->edittype->getValue();

        $this->item->Save();

        $this->setVisible(false);
        $this->caller->{$this->callback}();
    }

    public function cancelOnClick($sender)
    {
        $this->setVisible(false);
        $this->caller->{$this->callback}(true);
    }

    /**
     * передает  данные для  редактирования
     * 
     * @param mixed $item
     */
    public function open(\ZippyERP\ERP\Entity\Item $item = null)
    {
        if ($item == null)
            $item = new \ZippyERP\ERP\Entity\Item();
        $this->item = $item;
        $this->itemdetail->editname->setText($this->item->itemname);
        $this->itemdetail->editpriceret->setText(number_format($this->item->priceret / 100, 2, '.', ""));
        $this->itemdetail->editpriceopt->setText(number_format($this->item->priceopt / 100, 2, '.', ""));
        $this->itemdetail->editdescription->setText($this->item->description);
        $this->itemdetail->editbarcode->setText($this->item->barcode);
        $this->itemdetail->editmeasure->setValue($this->item->measure_id);
        $this->itemdetail->editgroup->setValue($this->item->group_id);

        $this->setVisible(true);
    }

    /**
     * возвращает отредактированные  данные
     * 
     */
    public function getData()
    {
        return $this->item;
    }

}
