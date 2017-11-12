<?php

namespace ZippyERP\System\Pages;

use Zippy\Html\DataList\DataView;
use ZippyERP\System\User;
use ZippyERP\System\System;
use Zippy\WebApplication as App;
use Zippy\Html\Form\CheckBox;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\TextArea;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Panel;

class Users extends \ZippyERP\System\Pages\Base
{

    public $user=null;

    public function __construct()
    {
        parent::__construct();
        if (System::getUser()->userlogin !== 'admin') {
            App::Redirect('\ZippyERP\System\Pages\Error', 'Вы не админ');
        }

        $this->add(new Panel("listpan"));
         $this->listpan->add(new ClickLink('addnew',$this,"onAdd"));
        $this->listpan->add(new DataView("userrow", new UserDataSource(), $this, 'OnAddUserRow'))->Reload();

        $this->add(new Panel("editpan"))->setVisible(false);       
        $this->editpan->add(new Form('editform'));
        $this->editpan->editform->add(new TextInput('editlogin'));
        $this->editpan->editform->add(new TextInput('editemail'));
        $this->editpan->editform->add(new DropDownChoice('editerpacl'));
        $this->editpan->editform->add(new CheckBox('editshopcontent'));
        $this->editpan->editform->add(new CheckBox('editshoporders'));
        $this->editpan->editform->onSubmit($this, 'saveOnClick');
        $this->editpan->editform->add(new Button('cancel'))->onClick($this, 'cancelOnClick');
       
        
    }
 

     public function onAdd($sender){
        $this->listpan->setVisible(false);
        $this->editpan->setVisible(true);
        // Очищаем  форму
        $this->editpan->editform->clean();

        $this->user = new User();  
            
     }
     
      public function onEdit($sender){
        $this->listpan->setVisible(false);
        $this->editpan->setVisible(true);
 

        $this->user = $sender->getOwner()->getDataItem();
        $this->editpan->editform->editemail->setText($this->user->email);  
        $this->editpan->editform->editlogin->setText($this->user->userlogin);  
        $this->editpan->editform->editerpacl->setValue($this->user->erpacl);  
        $this->editpan->editform->editshopcontent->setChecked($this->user->shopcontent);  
        $this->editpan->editform->editshoporders->setChecked($this->user->shoporders);  
            
     }
    
     
     public function saveOnClick($sender){
         
       $this->user->email =  $this->editpan->editform->editemail->getText();  
       $this->user->userlogin = $this->editpan->editform->editlogin->getText();  
       $user = User::getByLogin($this->user->userlogin);
       if($user instanceof User){
           if($user->user_id != $this->user->user_id){
               $this->setError('Неуникальный логин');
               return;
           }
       }
       if($this->user->email != ""){
           $user = User::getByEmail($this->user->email);
           if($user instanceof User ){
               if($user->user_id != $this->user->user_id){
                   $this->setError('Неуникальный email');
                   return;
               }
           }
       }
       $this->user->erpacl = $this->editpan->editform->editerpacl->getValue(); 
       $this->user->shopcontent = $this->editpan->editform->editshopcontent->isChecked(); 
       $this->user->shoporders = $this->editpan->editform->editshoporders->isChecked(); 
       
       $this->user->save();        
       $this->listpan->userrow->Reload();  
       $this->listpan->setVisible(true);
       $this->editpan->setVisible(false);        
     }
     public function cancelOnClick($sender){
        $this->listpan->setVisible(true);
        $this->editpan->setVisible(false);
     }
 
    //удаление  юзера
    public function OnRemove($sender)
    {
        $user = $sender->getOwner()->getDataItem();
        User::delete($user->user_id);
        $this->listpan->userrow->Reload();
    }

    public function OnAddUserRow($datarow)
    {
        $item = $datarow->getDataItem();
        $datarow->add(new \Zippy\Html\Link\RedirectLink("userlogin", '\\ZippyERP\\System\\Pages\\UserInfo', $item->user_id))->setValue($item->userlogin);

        $datarow->add(new \Zippy\Html\Label("created", date('d.m.Y', $item->createdon)));
        $datarow->add(new \Zippy\Html\Label("email",   $item->email));
        $datarow->add(new \Zippy\Html\Link\ClickLink("edit", $this, "OnEdit"))->setVisible($item->userlogin != 'admin');
        $datarow->add(new \Zippy\Html\Link\ClickLink("remove", $this, "OnRemove"))->setVisible($item->userlogin != 'admin');
        return $datarow;
    }

  

}

class UserDataSource implements \Zippy\Interfaces\DataSource
{

    //private $model, $db;

    public function getItemCount()
    {
        return User::findCnt();
    }

    public function getItems($start, $count, $orderbyfield = null,$desc=true )
    {
        return User::find('', $orderbyfield  , $count, $start);
    }

    public function getItem($id)
    {
        return User::load($id);
    }

}
