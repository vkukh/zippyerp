<?php

namespace App\Pages;

use \Zippy\Binding\PropertyBinding as Bind;
use \Zippy\Html\DataList\DataView;
use \Zippy\Html\DataList\ArrayDataSource;
use \Zippy\Html\Form\CheckBox;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextArea;
use \Zippy\Html\Form\TextInput;
use \Zippy\Html\Form\SubmitButton;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Panel;
use App\System;
use App\Application as App;

class Options extends \App\Pages\Base
{

    public $pricelist = array();

    public function __construct() {
        parent::__construct();
        if (System::getUser()->acltype == 2) {
            App::Redirect('\App\Pages\Error', 'У вас нет доступа к  настройкам');
        }


        $this->add(new Form('common'))->onSubmit($this, 'saveCommonOnClick');

        $this->common->add(new DropDownChoice('defstore', \App\Entity\Store::getList()));
        $this->common->add(new DropDownChoice('qtydigits'));
        $this->common->add(new DropDownChoice('amdigits'));


        $this->common->add(new CheckBox('hasnds'));
        $this->common->add(new CheckBox('simpletax'));
        $this->common->add(new CheckBox('juridical'));


        $this->common->add(new TextInput('price1'));
        $this->common->add(new TextInput('price2'));
        $this->common->add(new TextInput('price3'));
        $this->common->add(new TextInput('price4'));
        $this->common->add(new TextInput('price5'));
        //  $this->common->add(new Date('closeddate'));

 
        $this->add(new Form('detail'));
        $this->detail->add(new TextInput('firmname'));
        $this->detail->add(new TextInput('edrpou'));
        $this->detail->add(new TextInput('koatuu'));
        $this->detail->add(new TextInput('kopfg'));
        $this->detail->add(new TextInput('kodu'));
        $this->detail->add(new TextInput('kved'));
        $this->detail->add(new TextInput('gni'));
        $this->detail->add(new TextInput('inn'));
        $this->detail->add(new TextInput('address'));

        $this->detail->add(new TextInput('phone'));

        $this->detail->add(new TextInput('email'));
        $this->detail->add(new DropDownChoice('bank', \App\Entity\Bank::findArray('bank_name', '', 'bank_name')));

        $this->detail->add(new TextInput('bankaccount'));

        $this->detail->add(new SubmitButton('detailsave'))->onClick($this, 'saveDetailOnClick');


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




        //выводим данные

        $common = System::getOptions("common");
        if (!is_array($common))
            $common = array();

        $this->common->hasnds->setChecked($common['hasnds']);
        $this->common->simpletax->setChecked($common['simpletax']);
        $this->common->juridical->setChecked($common['juridical']);

        $this->common->defstore->setValue($common['defstore']);
        $this->common->qtydigits->setValue($common['qtydigits']);
        $this->common->amdigits->setValue($common['amdigits']);
        $this->common->price1->setText($common['price1']);
        $this->common->price2->setText($common['price2']);
        $this->common->price3->setText($common['price3']);
        $this->common->price4->setText($common['price4']);
        $this->common->price5->setText($common['price5']);

        // $this->common->closeddate->setDate($common['closeddate']);





 
        $detail = System::getOptions("firmdetail");

        if (!is_array($detail))
            $detail = array();


        $this->detail->firmname->setText($detail['firmname']);
        $this->detail->edrpou->setText($detail['edrpou']);
        $this->detail->koatuu->setText($detail['koatuu']);
        $this->detail->kopfg->setText($detail['kopfg']);
        $this->detail->kodu->setText($detail['kodu']);
        $this->detail->kved->setText($detail['kved']);
        $this->detail->gni->setText($detail['gni']);
        $this->detail->inn->setText($detail['inn']);
        $this->detail->address->setText($detail['address']);

        $this->detail->phone->setText($detail['phone']);
        $this->detail->email->setText($detail['email']);
        $this->detail->bankaccount->setText($detail['bankaccount']);
        $this->detail->bank->setValue($detail['bank']);

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

    public function saveCommonOnClick($sender) {
        $common = array();

        $common['defstore'] = $this->common->defstore->getValue();
        $common['qtydigits'] = $this->common->qtydigits->getValue();
        $common['amdigits'] = $this->common->amdigits->getValue();
        $common['price1'] = $this->common->price1->getText();
        $common['price2'] = $this->common->price2->getText();
        $common['price3'] = $this->common->price3->getText();
        $common['price4'] = $this->common->price4->getText();
        $common['price5'] = $this->common->price5->getText();

        $common['hasnds'] = $this->common->hasnds->isChecked();
        $common['simpletax'] = $this->common->simpletax->isChecked();
        $common['juridical'] = $this->common->juridical->isChecked();

        // $common['closeddate'] = $this->common->closeddate->getDate();

        System::setOptions("common", $common);

       
        $this->setSuccess('Сохранено');
    }

  

    public function saveDetailOnClick($sender) {

        if ($this->detail->firmname->getText() == '') {
            $this->setError("Не задано имя фирмы");
            return;
        }
        $detail = array();
        $detail['firmname'] = $this->detail->firmname->getText();
        $detail['edrpou'] = $this->detail->edrpou->getText();
        $detail['koatuu'] = $this->detail->koatuu->getText();
        $detail['kopfg'] = $this->detail->kopfg->getText();
        $detail['kodu'] = $this->detail->kodu->getText();
        $detail['kved'] = $this->detail->kved->getText();
        $detail['gni'] = $this->detail->gni->getText();
        $detail['inn'] = $this->detail->inn->getText();
        $detail['address'] = $this->detail->address->getText();

        $detail['phone'] = $this->detail->phone->getText();
        $detail['email'] = $this->detail->email->getText();
        $detail['bank'] = $this->detail->bank->getValue();
        $detail['bankaccount'] = $this->detail->bankaccount->getText();

        System::setOptions("firmdetail", $detail);
        $this->setSuccess('Сохранено');
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
        $this->setSuccess('Сохранено');
    }

}
