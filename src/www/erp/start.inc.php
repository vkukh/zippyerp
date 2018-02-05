<?php

namespace ZippyERP\ERP;

define("ERP_DIR", __DIR__ . '/');
define("ERP_TPL", _ROOT . '/templates/erp/templates');  //шаблоны печатных форм

function autoload($className) {
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

function getTemplate($className) {
    $className = str_replace("\\", "/", ltrim($className, '\\'));
    $templatepath = _ROOT . 'templates/';

    $path = "";
    if (strpos($className, 'ZippyERP/ERP/') === 0) {
        $path = $templatepath . (str_replace("ZippyERP/", "", $className)) . ".html";
    }
    return @file_get_contents(strtolower($path));
}

function Route($uri) {

    $api = explode('/', $uri);

    if ($api[0] == 'erpapi' && count($api) > 2) {

        $class = $api[1];
        $params = array_slice($api, 3);

        if ($class == 'echo') {  //для  теста  /api/echo/параметр
            $response = "<echo>" . $api[2] . "</echo>";
        } else {

            try {


                require_once(_ROOT . DIRECTORY_SEPARATOR . strtolower("api" . DIRECTORY_SEPARATOR . $class . ".php"));

                $class = "\\ZippyERP\\ERP\\API\\" . $class;

                $page = new $class;

                //если RESTFul
                if ($page instanceof \ZippyERP\System\RestFul) {
                    $page->Execute($api[2]);
                    die;
                }


                $response = call_user_func_array(array($page, $api[2]), $params);
            } catch (Exception $e) {


                $response = "<error>" . $e->getMessage() . "</error>";
            }
        }
        $xml = '<?xml version="1.0" encoding="utf-8"?>' . $response;

        header(`Content-Type: text/xml; charset=utf-8`);
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        echo $xml;

        die;
    }
}
