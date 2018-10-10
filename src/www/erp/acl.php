<?php

namespace ZippyERP\ERP;

use \ZippyERP\System\System;
use \Zippy\WebApplication as App;

/**
 * Класс  для  упрвления доступом к метаобьектам
 */
class ACL
{

    private static $_metas = array();

    private static function load() {
        if (count(self::$_metas) > 0)
            return;

        $conn = \ZDB\DB::getConnect();
        $rows = $conn->Execute("select * from erp_metadata ");
        foreach ($rows as $row) {
            self::$_metas[$row['meta_type'] . '_' . $row['meta_name']] = $row['meta_id'];
        }
    }

   
    //проверка  на  доступ  к  отчету
    public static function checkShowReport($rep) {
        if (System::getUser()->erpacl != 2)
             return  true;

        self::load();

        $meta_id = self::$_metas['2_' . $rep];
        $aclview = explode(',', System::getUser()->aclview);
       
         

        if(in_array($meta_id, $aclview)){
             return true ;
        }
        
        System::setErrorMsg('Нема права  на доступ до звіту') ;
        App::RedirectHome();
        return false;
    }
    
   //проверка  на  доступ  к  справочнику 
    public static function checkShowRef($ref) {
        if (System::getUser()->erpacl != 2)
             return  true;

        self::load();

        $meta_id = self::$_metas['4_' . $ref];
        $aclview = explode(',', System::getUser()->aclview);
  
        if(in_array($meta_id, $aclview)){
             return  true;
        }
        
        System::setErrorMsg('Нема права  доступу до  довідника') ;
        App::RedirectHome();
        return false;
    }
    //проверка  на  доступ  к   редактированю справочника
    public static function checkEditRef($ref) {
        if (System::getUser()->erpacl != 2)
             return   true;

        self::load();

        $meta_id = self::$_metas['4_' . $ref];
        $acledit = explode(',', System::getUser()->acledit);
  
        if(in_array($meta_id, $acledit)){
             return true ;
        }
        
        System::setErrorMsg('Нема права редагування довідника') ;
        App::RedirectHome();
        return false;
    }
    
     //проверка  на  доступ  к  журналу 
    public static function checkShowReg($reg) {
        if (System::getUser()->erpacl != 2)
             return  true;

        self::load();

        $meta_id = self::$_metas['3_' . $reg];
        $aclview = explode(',', System::getUser()->aclview);
  
        if(in_array($meta_id, $aclview)){
             return  true;
        }
        
        System::setErrorMsg('Нема доступа до журналу') ;
        App::RedirectHome();
        return false;
    }
    
     //проверка  на  доступ  к  пользовательским страницам
    public static function checkShowCat($page) {
        if (System::getUser()->erpacl != 2)
             return  true;

        self::load();

        $meta_id = self::$_metas['5_' . $page];
        $aclview = explode(',', System::getUser()->aclview);
  
        if(in_array($meta_id, $aclview)){
             return  true;
        }
        
        System::setErrorMsg('Нема права доступу  до сторінки') ;
        App::RedirectHome();
        return false;
    }
     
   
     //проверка  на  доступ  к  документу 
    public static function checkShowDoc($doc,$inreg=false) {
        $user = System::getUser();
        if ($user->erpacl != 2)
             return  true;

        self::load();

        
        
        //для существующих документов
        if($user->onlymy ==1 && $doc->document_id >0){
           
           if($user->user_id != $doc->user_id ){
            System::setErrorMsg('Нема права переглядудокументу') ;
            if($inreg==false)App::RedirectHome();
            return false;               
           }
        }
        
       
        $aclview = explode(',', $user->aclview);
  
        if(in_array($doc->meta_id, $aclview)){
             return  true;
        }
        
        System::setErrorMsg('Нема права переглядудокументу') ;
        if($inreg==false)App::RedirectHome();
        return false;
    }   
     //проверка  на  доступ  к   редактированию документа
    public static function checkEditDoc($doc,$inreg=false) {
        $user = System::getUser();
        if ($user->erpacl != 2)
             return  true;

        self::load();

    
        
        if($user->onlymy ==1 && $doc->document_id >0){
            if($user->user_id != $doc->user_id ){
            System::setErrorMsg('Нема права редагування') ;
            if($inreg==false)App::RedirectHome();
            return false;               
           }       }
        
     
        $acledit = explode(',', $user->acledit);
  
        if(in_array($doc->meta_id, $acledit)){
             return  true;
        }
        
        System::setErrorMsg('Нема права редагування') ;
        if($inreg==false)App::RedirectHome();
        return false;
    }   
        
    
}
