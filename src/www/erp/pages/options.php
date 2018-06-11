<?php

namespace ZippyERP\ERP\Pages;

use Zippy\Html\Form\CheckBox;
use Zippy\Html\Form\Date;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextInput;
use ZippyERP\System\System;
use Zippy\WebApplication as App;

class Options extends \ZippyERP\ERP\Pages\Base
{

    public function __construct() {
        parent::__construct();
        if (System::getUser()->userlogin !== 'admin') {
            App::Redirect('\ZippyERP\System\Pages\Error', 'Вы не админ');
        }
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

        $this->detail->add(new TextInput('email'));
        $this->detail->add(new DropDownChoice('bank', \ZippyERP\ERP\Entity\Bank::findArray('bank_name', '', 'bank_name')));
        $this->detail->add(new DropDownChoice('bank2', \ZippyERP\ERP\Entity\Bank::findArray('bank_name', '', 'bank_name')));
        $this->detail->add(new TextInput('bankaccount'));
        $this->detail->add(new TextInput('bankaccount2'));
        $this->detail->add(new SubmitButton('detailsave'))->onClick($this, 'saveDetailOnClick');


        $this->add(new Form('common'));
        $this->common->add(new Date('closeddate'));

        $this->common->add(new CheckBox('hasnds'));
        $this->common->add(new CheckBox('simpletax'));
        $this->common->add(new CheckBox('juridical'))->onChange($this, "OnJFChange");
        $this->common->add(new SubmitButton('commonsave'))->onClick($this, 'saveCommonOnClick');
        $this->common->add(new DropDownChoice('basestore', \ZippyERP\ERP\Entity\Store::findArray('storename', '')));
        $this->common->add(new DropDownChoice('manager', \ZippyERP\ERP\Entity\Employee::findArray("fullname", " hiredate is not null ", "fullname")));
        $this->common->add(new DropDownChoice('accounter', \ZippyERP\ERP\Entity\Employee::findArray("fullname", " hiredate is not null ", "fullname")));
        $this->common->add(new DropDownChoice('ownerfiz', \ZippyERP\ERP\Entity\Employee::findArray("fullname", "", "fullname")));
        $this->common->ownerfiz->setVisible(true);

        $this->common->manager->setVisible(false);

        $this->add(new Form('tax'));
        $this->tax->add(new SubmitButton('taxsave'))->onClick($this, 'saveTaxOnClick');
        $this->tax->add(new TextInput('minsalary', 0));
        $this->tax->add(new TextInput('minnsl', 0));
        $this->tax->add(new TextInput('nsl', 0));
        $this->tax->add(new TextInput('nds', 0));
        $this->tax->add(new TextInput('onetax', 0));

        $this->tax->add(new TextInput('ecbfot', 0));
        $this->tax->add(new TextInput('ecbinv', 0));
        $this->tax->add(new TextInput('taxfl', 0));
        $this->tax->add(new TextInput('military', 0));


        $detail = System::getOptions("firmdetail");

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
        $this->detail->phone->setText($detail['phone']);
        $this->detail->email->setText($detail['email']);

        $f = \ZippyERP\ERP\Entity\MoneyFund::findOne('ftype = 1');

        if ($f != null) {
            $this->detail->bank->setValue($f->bank);
            $this->detail->bankaccount->setText($f->bankaccount);
        }
        $f = \ZippyERP\ERP\Entity\MoneyFund::findOne('ftype = 2');

        if ($f != null) {
            $this->detail->bank2->setValue($f->bank);
            $this->detail->bankaccount2->setText($f->bankaccount);
        }

        $common = System::getOptions("common");
        if (!is_array($common))
            $common = array();
        $this->common->closeddate->setDate($common['closeddate']);
        $this->common->hasnds->setChecked($common['hasnds']);
        $this->common->simpletax->setChecked($common['simpletax']);
        $this->common->juridical->setChecked($common['juridical']);
        $this->common->basestore->setValue($common['basestore']);
        $this->common->manager->setValue($common['manager']);
        $this->common->manager->setVisible($this->common->juridical->isChecked());
        $this->common->accounter->setValue($common['accounter']);
        $this->common->ownerfiz->setValue($common['owner']);
        $this->common->ownerfiz->setVisible(!$this->common->juridical->isChecked());


        $tax = System::getOptions("tax");
        if (!is_array($tax))
            $tax = array();

        $this->tax->minnsl->setText($tax['minnsl']);
        $this->tax->nsl->setText($tax['nsl']);
        $this->tax->minsalary->setText($tax['minsalary']);
        $this->tax->nds->setText($tax['nds']);
        $this->tax->onetax->setText($tax['onetax']);

        $this->tax->ecbfot->setText($tax['ecbfot']);
        $this->tax->ecbinv->setText($tax['ecbinv']);
        $this->tax->taxfl->setText($tax['taxfl']);
        $this->tax->military->setText($tax['military']);
    }

    public function saveDetailOnClick($sender) {

        if ($this->detail->name->getText() == '') {
            $this->setError("Введіть им'я");
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
        $detail['phone'] = $this->detail->phone->getText();
        $detail['email'] = $this->detail->email->getText();

        $f = \ZippyERP\ERP\Entity\MoneyFund::findOne('ftype = 1');
        if ($f != null) {  // обноваляем  основной   счет
            $f->bank = $this->detail->bank->getValue();
            $f->bankaccount = $this->detail->bankaccount->getText();
            $f->save();
        }
        $f = \ZippyERP\ERP\Entity\MoneyFund::findOne('ftype = 2');
        if ($f != null) {  // обноваляем  дополнительный   счет
            $f->bank = $this->detail->bank2->getValue();
            $f->bankaccount = trim($this->detail->bankaccount2->getText());
            $f->save();
        }

        System::setOptions("firmdetail", $detail);
        $this->setSuccess('Збережено');
    }

    public function saveCommonOnClick($sender) {
        $common = array();
        $common['closeddate'] = $this->common->closeddate->getDate();
        $common['hasnds'] = $this->common->hasnds->isChecked();
        $common['simpletax'] = $this->common->simpletax->isChecked();
        $common['juridical'] = $this->common->juridical->isChecked();
        $common['basestore'] = $this->common->basestore->getValue();
        $common['manager'] = $this->common->manager->getValue();
        $common['managername'] = $this->common->manager->getValueName();
        $common['accounter'] = $this->common->accounter->getValue();
        $common['accountername'] = $this->common->accounter->getValueName();
        $common['owner'] = $this->common->ownerfiz->getValue();
        $common['ownername'] = $this->common->ownerfiz->getValueName();

        System::setOptions("common", $common);
        $this->setSuccess('Збережено');
    }

    public function saveTaxOnClick($sender) {
        $tax = array();

        $tax['minsalary'] = $this->tax->minsalary->getText();
        $tax['nsl'] = $this->tax->nsl->getText();
        $tax['minnsl'] = $this->tax->minnsl->getText();
        $tax['nds'] = $this->tax->nds->getText();
        $tax['onetax'] = $this->tax->onetax->getText();
        $tax['ecbfot'] = $this->tax->ecbfot->getText();
        $tax['ecbinv'] = $this->tax->ecbinv->getText();
        $tax['taxfl'] = $this->tax->taxfl->getText();
        $tax['military'] = $this->tax->military->getText();

        System::setOptions("tax", $tax);
        $this->setSuccess('Збережено');
    }

    public function OnJFChange($sender) {
        if ($sender->isChecked()) {
            $this->common->ownerfiz->setVisible(false);
            ;
            $this->common->manager->setValue(0);
            $this->common->manager->setVisible(true);
            ;
        } else {
            $this->common->ownerfiz->setVisible(true);
            ;
            $this->common->ownerfiz->setValue(0);

            $this->common->manager->setVisible(false);
            ;
        }
    }

}
