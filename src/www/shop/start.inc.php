<?php

namespace ZippyERP\Shop;

use \Zippy\WebApplication as App;

define("SYSTEM_DIR", __DIR__ . '/');

const REG_EMAIL = "/^([a-zA-Z0-9_\.-]+)@([a-zA-Z0-9_\.-]+)\.([a-z\.]{2,6})$/";

function autoload($className) {
    $className = str_replace("\\", "/", ltrim($className, '\\'));



    if (strpos($className, 'ZippyERP/Shop/') === 0) {
        $file = __DIR__ . DIRECTORY_SEPARATOR . strtolower(str_replace("ZippyERP/Shop/", "", $className)) . ".php";
        if (file_exists($file)) {
            require_once($file);
        } else {
            \Zippy\WebApplication::Redirect('\\ZippyERP\\System\\Pages\\Error', 'Неверный URL ' . $className);
        }
    }
}

spl_autoload_register('\ZippyERP\Shop\autoload');

function getTemplate($className) {
    $templatepath = _ROOT . 'templates/';
    $className = str_replace("\\", "/", ltrim($className, '\\'));

    $path = "";
    if (strpos($className, 'ZippyERP/Shop/') === 0) {
        $path = $templatepath . (str_replace("ZippyERP/", "", $className)) . ".html";
    }
    return @file_get_contents(strtolower($path));
}

;

function Route($uri) {

    if (preg_match('/^[-#a-zA-Z0-9\/_]+$/', $uri) == 0) {
        App::Redirect404();
    }
    $arr = explode('/', $uri);

    $pages = array(
        "simage" => "\\ZippyERP\\Shop\\Pages\\LoadImage",
        "scat" => "\\ZippyERP\\Shop\\Pages\\Main",
        "sp" => "\\ZippyERP\\Shop\\Pages\\ProductView",
        "aboutus" => "\\ZippyERP\\Shop\\Pages\\AboutUs",
        "pcat" => "\\ZippyERP\\Shop\\Pages\\Catalog"
    );


    if (strlen($arr[2]) > 0) {
        App::$app->LoadPage($pages[$arr[0]], $arr[1], $arr[2]);
    } else
    if (strlen($arr[1]) > 0) {
        App::$app->LoadPage($pages[$arr[0]], $arr[1]);
    } else
    if ($pages[$uri] != null) {
        App::$app->LoadPage($pages[$uri]);
    } else {

        App::$app->getResponse()->to404Page();
    }
}
