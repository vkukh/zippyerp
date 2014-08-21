<?php

namespace ZippyERP\ERP\Blocks;

use \Zippy\Html\Form\Form;
use Zippy\Html\Form\TextInput;
use \Zippy\Html\Label;
use \Zippy\Html\Form\SubmitButton;
use \Zippy\Html\Form\Button;
use \Zippy\Binding\PropertyBinding as Bind;

/**
 * Виджет для  редатирования  контакта
 */
class Contact extends \Zippy\Html\PageFragment
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

        $this->add(new Form('contactdetail'));
        $this->contactdetail->add(new TextInput('editlastname'));
        $this->contactdetail->add(new TextInput('editfirstname'));
        $this->contactdetail->add(new TextInput('editmiddlename'));
        $this->contactdetail->add(new TextInput('editemail'));
        $this->contactdetail->add(new TextInput('editposition'));
        $this->contactdetail->add(new TextInput('editnotes'));
        $this->contactdetail->add(new SubmitButton('save'))->setClickHandler($this, 'saveOnClick');
        $this->contactdetail->add(new Button('cancel'))->setClickHandler($this, 'cancelOnClick');
    }

    public function saveOnClick($sender)
    {
        $this->setVisible(false);
        $this->item->lastname = $this->contactdetail->editlastname->getText();
        $this->item->firstname = $this->contactdetail->editfirstname->getText();
        $this->item->middlename = $this->contactdetail->editmiddlename->getText();
        $this->item->email = $this->contactdetail->editemail->getText();
        $this->item->position = $this->contactdetail->editposition->getText();
        $this->item->notes = $this->contactdetail->editnotes->getText();

        $this->item->Save();

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
    public function open(\ZippyERP\ERP\Entity\Contact $item = null)
    {
        if ($item == null)
            $item = new \ZippyERP\ERP\Entity\Contact();
        $this->item = $item;
        $this->contactdetail->editlastname->setText($item->lastname);
        $this->contactdetail->editfirstname->setText($item->firstname);
        $this->contactdetail->editmiddlename->setText($item->middlename);
        $this->contactdetail->editemail->setText($item->email);
        $this->contactdetail->editposition->setText($item->position);
        $this->contactdetail->editnotes->setText($item->notes);

        $this->setVisible(true);
    }

    /**
     * возвращает отредактированные  данные
     * 
     */
    public function getData()
    {
        
    }

}
