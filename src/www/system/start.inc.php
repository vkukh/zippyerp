<?php

namespace ZippyERP\System;

use Zippy\WebApplication as App;

define("SYSTEM_DIR", __DIR__ . '/');

const REG_EMAIL = "/^([a-zA-Z0-9_\.-]+)@([a-zA-Z0-9_\.-]+)\.([a-z\.]{2,6})$/";

function autoload($className)
{
    $className = str_replace("\\", "/", ltrim($className, '\\'));

 

    if (strpos($className, 'ZippyERP/System/') === 0) {
        $file = __DIR__ . DIRECTORY_SEPARATOR . strtolower(str_replace("ZippyERP/System/", "", $className)) . ".php";
        if (file_exists($file)) {
            require_once($file);
        } else {
            \Zippy\WebApplication::Redirect('\\ZippyERP\\System\\Pages\\Error', 'Неверный URL ' . $className);
        }
    }
}

spl_autoload_register('\ZippyERP\System\autoload');

 
function getTemplate ($className)
{
    $templatepath = _ROOT . 'templates/';
    $className = str_replace("\\", "/", ltrim($className, '\\'));

    $path = "";
    if (strpos($className, 'ZippyERP/System/') === 0) {
        $path = $templatepath . (str_replace("ZippyERP/", "", $className)) . ".html";
    }
    return   @file_get_contents(strtolower($path));
} ;

function Route($uri)
{
     
    if($uri =="toerp"){
       App::Redirect('\ZippyERP\ERP\Pages\Main');
       return;
 
    }
    if($uri =="tosystem"){
       App::Redirect('\ZippyERP\System\Pages\Main');
       return;
 
    }
    if($uri =="toshop"){
       App::Redirect('\ZippyERP\Shop\Pages\Main');
       return;
 
    }
    
}