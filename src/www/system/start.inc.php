<?php

namespace ZippyERP\System;

define("SYSTEM_DIR", __DIR__ . '/');

const REG_EMAIL = "/^([a-zA-Z0-9_\.-]+)@([a-zA-Z0-9_\.-]+)\.([a-z\.]{2,6})$/";

function autoload($className)
{
    $className = str_replace("\\", "/", ltrim($className, '\\'));

    /*
      if (strpos($className, 'ZippyERP/System/Pages/') === 0) {
      require_once(__DIR__ . "/" . strtolower(str_replace("ZippyERP/", "", $className)) . ".php");
      } else
      if (strpos($className, 'ZippyERP/System/Blocks/') === 0) {
      require_once(__DIR__ . "/" . strtolower(str_replace("ZippyERP/", "", $className)) . ".php");
      } else
     */

    if (strpos($className, 'ZippyERP/System/') === 0) {
        require_once(__DIR__ . DIRECTORY_SEPARATOR . strtolower(str_replace("ZippyERP/System/", "", $className)) . ".php");
    }
}

spl_autoload_register('\ZippyERP\System\autoload');

function getTemplate($templatepath, $className, $layout = '')
{
    $className = str_replace("\\", "/", ltrim($className, '\\'));

    $path = "";
    if (strpos($className, 'ZippyERP/System/') === 0) {
        $path = $templatepath . (str_replace("ZippyERP/", "", $className)) . ".html";
    }
    return $path;
}

//require_once SYSTEM_DIR . 'lang/' . \ZippyERP\System\System::getLang() . '.php';

// \ZippyERP\Core\System::init();


