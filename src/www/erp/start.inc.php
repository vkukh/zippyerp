<?php

namespace ZippyERP\ERP;

define("ERP_DIR", __DIR__ . '/');
define("ERP_TPL", _ROOT . '/templates/erp/templates');  //шаблоны печатных форм

function autoload($className)
{
    $className = str_replace("\\", "/", ltrim($className, '\\'));


    if (strpos($className, 'ZippyERP/ERP/') === 0) {
        $file = __DIR__ . DIRECTORY_SEPARATOR . strtolower(str_replace("ZippyERP/ERP/", "", $className)) . ".php";
        if (file_exists($file)) {
            require_once($file);
        } else {
            \Zippy\WebApplication::Redirect('\\ZippyERP\\System\\Pages\\Error', "Класс {$className} не  найден");
        }
    }
}

spl_autoload_register('\ZippyERP\ERP\autoload');

function getTemplate( $className)
{
    $className = str_replace("\\", "/", ltrim($className, '\\'));
          $templatepath = _ROOT . 'templates/';

    $path = "";
    if (strpos($className, 'ZippyERP/ERP/') === 0) {
        $path = $templatepath . (str_replace("ZippyERP/", "", $className)) . ".html";
    }
    return  @file_get_contents(strtolower($path));
}
