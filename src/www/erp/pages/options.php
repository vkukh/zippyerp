<?php

namespace ZippyERP\ERP\Pages;

use \Zippy\Html\Form\Form;
use \Zippy\Html\DataList\DataView;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\Date;
use \Zippy\Html\Form\CheckBox;
use \Zippy\Html\Form\TextArea;
use \Zippy\Html\Form\SubmitButton;
use \Zippy\Html\Form\Button;
use \Zippy\Html\Form\DropDownChoice;

class Options extends \ZippyERP\System\Pages\AdminBase
{

    public function __construct()
    {
        parent::__construct();

        $this->add(new Form('detail'));
        $this->detail->add(new TextInput('name'));
        $this->detail->add(new TextInput('edrpou'));
        $this->detail->add(new TextInput('koatuu'));
        $this->detail->add(new TextInput('kopfg'));
        $this->detail->add(new TextInput('kodu'));
        $this->detail->add(new TextInput('kved'));
        $this->detail->add(new TextInput('gni'));
        $this->detail->add(new TextInput('inn'));
        $this->detail->add(new TextInput('city'));
        $this->detail->add(new TextInput('street'));
        $this->detail->add(new TextInput('phone'));
        $this->detail->add(new TextInput('manager'));
        $this->detail->add(new TextInput('accounter'));
        $this->detail->add(new TextInput('email'));
        $this->detail->add(new DropDownChoice('bank', \ZippyERP\ERP\Entity\Bank::findArray('bank_name', '', 'bank_name')));
        $this->detail->add(new TextInput('bankaccount'));
        $this->detail->add(new SubmitButton('detailsave'))->setClickHandler($this, 'saveDetailOnClick');




        $this->add(new Form('common'));
        $this->common->add(new Date('closeddate'));
        $this->common->add(new TextInput('nds'));
        $this->common->add(new CheckBox('hasnds'));
        $this->common->add(new CheckBox('simpletax'));
        $this->common->add(new CheckBox('juridical'));
        $this->common->add(new SubmitButton('commonsave'))->setClickHandler($this, 'saveCommonOnClick');


        $detail = \ZippyERP\System\System::getOptions("firmdetail");

        if (!is_array($detail))
            $detail = array();

        $this->detail->name->setText($detail['name']);
        $this->detail->edrpou->setText($detail['edrpou']);
        $this->detail->koatuu->setText($detail['koatuu']);
        $this->detail->kopfg->setText($detail['kopfg']);
        $this->detail->kodu->setText($detail['kodu']);
        $this->detail->kved->setText($detail['kved']);
        $this->detail->gni->setText($detail['gni']);
        $this->detail->inn->setText($detail['inn']);
        $this->detail->city->setText($detail['city']);
        $this->detail->street->setText($detail['street']);
        $this->detail->manager->setText($detail['manager']);
        $this->detail->accounter->setText($detail['accounter']);
        $this->detail->phone->setText($detail['phone']);
        $this->detail->email->setText($detail['email']);

        $f = \ZippyERP\ERP\Entity\MoneyFund::findOne('ftype = 1');

        if ($f != null) {
            $this->detail->bank->setValue($f->bank);
            $this->detail->bankaccount->setText($f->bankaccount);
        }

        $common = \ZippyERP\System\System::getOptions("common");
        if (!is_array($common))
            $common = array();
        $this->common->closeddate->setDate($common['closeddate']);
        $this->common->nds->setText($common['nds']);
        $this->common->hasnds->setChecked($common['hasnds']);
        $this->common->simpletax->setChecked($common['simpletax']);
        $this->common->juridical->setChecked($common['juridical']);
    }

    public function saveDetailOnClick($sender)
    {

        if ($this->detail->name->getText() == '') {
            $this->setError("Введите имя");
            return;
        }
        $detail = array();
        $detail['name'] = $this->detail->name->getText();
        $detail['edrpou'] = $this->detail->edrpou->getText();
        $detail['koatuu'] = $this->detail->koatuu->getText();
        $detail['kopfg'] = $this->detail->kopfg->getText();
        $detail['kodu'] = $this->detail->kodu->getText();
        $detail['kved'] = $this->detail->kved->getText();
        $detail['gni'] = $this->detail->gni->getText();
        $detail['inn'] = $this->detail->inn->getText();
        $detail['city'] = $this->detail->city->getText();
        $detail['street'] = $this->detail->street->getText();
        $detail['manager'] = $this->detail->manager->getText();
        $detail['accounter'] = $this->detail->accounter->getText();
        $detail['phone'] = $this->detail->phone->getText();
        $detail['email'] = $this->detail->email->getText();

        $f = \ZippyERP\ERP\Entity\MoneyFund::findOne('ftype = 1');
        if ($f != null) {  // обноваляем  основной   счет
            $f->bank = $this->detail->bank->getValue();
            $f->bankaccount = $this->detail->bankaccount->getText();
            $f->save();
        }

        \ZippyERP\System\System::setOptions("firmdetail", $detail);
    }

    public function saveCommonOnClick($sender)
    {
        $common = array();
        $common['closeddate'] = $this->common->closeddate->getDate();
        $common['nds'] = $this->common->nds->getText();
        $common['hasnds'] = $this->common->hasnds->isChecked();
        $common['simpletax'] = $this->common->simpletax->isChecked();
        $common['juridical'] = $this->common->juridical->isChecked();
        \ZippyERP\System\System::setOptions("common", $common);
    }

}
