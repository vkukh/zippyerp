<?php

namespace ZippyERP\Shop\Pages;

use \Zippy\Html\Form\AutocompleteTextInput;
use \Zippy\Html\Form\CheckBox;
use \Zippy\Html\Form\Date;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\SubmitButton;
use \Zippy\Html\Form\TextInput;
use \ZippyERP\System\System;
use \Zippy\WebApplication as App;

class Options extends \ZippyERP\Shop\Pages\Base
{

    public function __construct() {
        parent::__construct();
        if (System::getUser()->userlogin !== 'admin') {
            App::Redirect('\ZippyERP\System\Pages\Error', 'Вы не админ');
        }

        $op = System::getOptions("shop");
        if (!is_array($op))
            $op = array();

        $this->add(new Form('optionform'))->onSubmit($this, 'saveOnClick');
        $this->optionform->add(new DropDownChoice('store', \ZippyERP\ERP\Entity\Store::findArray("storename", "store_type=" . \ZippyERP\ERP\Entity\Store::STORE_TYPE_INET)));
        $this->optionform->add(new TextInput('emailorder'))->setText($op['emailorder']);



        $this->optionform->store->setValue($op['store']);
    }

    public function saveOnClick($sender) {


        $op = array();
        $op['store'] = $this->optionform->store->getValue();
        $op['emailorder'] = $this->optionform->emailorder->getText();


        System::setOptions("shop", $op);
        $this->setSuccess('Настройки сохранены');
    }

}
