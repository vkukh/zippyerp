<?php

namespace ZippyERP\ERP;

define("ERP_DIR", __DIR__ . '/');
define("ERP_TPL", _ROOT . '/templates/erp/templates');  //шаблоны печатных форм

function autoload($className)
{
        $className = str_replace("\\", "/", ltrim($className, '\\'));


        if (strpos($className, 'ZippyERP/ERP/') === 0) {
                require_once(__DIR__ . DIRECTORY_SEPARATOR . strtolower(str_replace("ZippyERP/ERP/", "", $className)) . ".php");
        }
}

spl_autoload_register('\ZippyERP\ERP\autoload');

function getTemplate($templatepath, $className, $layout = '')
{
        $className = str_replace("\\", "/", ltrim($className, '\\'));

        $path = "";
        if (strpos($className, 'ZippyERP/ERP/') === 0) {
                $path = $templatepath . (str_replace("ZippyERP/", "", $className)) . ".html";
        }
        return $path;
}

