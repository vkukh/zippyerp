<?php

namespace ZippyERP\ERP\Blocks;

use Zippy\Html\Form\Button;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextArea;
use Zippy\Html\Form\TextInput;

/**
 * Виджет для  редатирования  контакта
 */
class Contact extends \Zippy\Html\PageFragment
{

    private $caller, $callback, $item;

    /**
     * put your comment there...
     *
     * @param mixed $id id компонента
     * @param mixed $caller ссылка на  класс  вызвавшей  страницы
     * @param mixed $callback имя функции  к  вызвавшей странице  для возврата
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
        $this->contactdetail->add(new TextInput('editphone'));
        $this->contactdetail->add(new TextArea('editdescription'));
        $this->contactdetail->add(new SubmitButton('save'))->onClick($this, 'saveOnClick');
        $this->contactdetail->add(new Button('cancel'))->onClick($this, 'cancelOnClick');
    }

    public function saveOnClick($sender)
    {
        $this->setVisible(false);
        $this->item->lastname = trim($this->contactdetail->editlastname->getText());
        $this->item->firstname = trim($this->contactdetail->editfirstname->getText());
        $this->item->middlename = trim($this->contactdetail->editmiddlename->getText());
        $this->item->email = trim($this->contactdetail->editemail->getText());
        $this->item->phone = trim($this->contactdetail->editphone->getText());
        $this->item->description = $this->contactdetail->editdescription->getText();
        $isnew = $this->item->contact_id == 0;
        $this->item->Save();

        $this->caller->{$this->callback}(true, $isnew ? $this->item->contact_id : 0);
    }

    public function cancelOnClick($sender)
    {
        $this->setVisible(false);
        $this->caller->{$this->callback}(false);
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
        $this->contactdetail->editphone->setText($item->phone);
        $this->contactdetail->editdescription->setText($item->description);

        $this->setVisible(true);
    }

    /**
     * Возвращает  отредактированный  обьект
     */
    public function getItem()
    {
        return $this->item;
    }

}
