<?php

namespace ZippyERP\Shop\Pages;

use \Zippy\Html\Form\AutocompleteTextInput;
use \Zippy\Html\Form\CheckBox;
use \Zippy\Html\Form\Date;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\SubmitButton;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\TextArea;
use \ZippyERP\System\System;
use \Zippy\WebApplication as App;

class Options extends \ZippyERP\Shop\Pages\Base
{

    public $op =array();
    
    public function __construct() {
        parent::__construct();
        if (System::getUser()->userlogin !== 'admin') {
            App::Redirect('\ZippyERP\System\Pages\Error', 'Ви не адмын');
        }

        $this->op = System::getOptions("shop");
        if (!is_array($this->op))
            $this->op = array();

        $this->add(new Form('optionform'))->onSubmit($this, 'saveOnClick');
        $this->optionform->add(new DropDownChoice('store', \ZippyERP\ERP\Entity\Store::findArray("storename", "store_type=" . \ZippyERP\ERP\Entity\Store::STORE_TYPE_OPT),$this->op['store']));
        $this->optionform->add(new TextInput('emailorder'))->setText($this->op['emailorder']);

        $this->add(new Form('auform'))->onSubmit($this, 'aboutusOnClick');
        $this->auform->add(new TextArea('aboutus'))->setText($this->op['aboutus']);
        

        
    }

    public function saveOnClick($sender) {


  
        $this->op['store'] = $this->optionform->store->getValue();
        $this->op['emailorder'] = $this->optionform->emailorder->getText();


        System::setOptions("shop", $this->op);
        $this->setSuccess('Збережено');
    }
    public function aboutusOnClick($sender) {


  
       $this->op['aboutus'] = $this->auform->aboutus->getText();


        System::setOptions("shop", $this->op);
        $this->setSuccess('Збережено');
    }

}
