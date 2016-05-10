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
        $file = __DIR__ . DIRECTORY_SEPARATOR . strtolower(str_replace("ZippyERP/System/", "", $className)) . ".php";
        if (file_exists($file)) {
            require_once($file);
        } else {
            \ZippyERP\System\Application::Redirect('\\ZippyERP\\System\\Pages\\Error', 'Неверный URL');
        }
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

function Route($uri)
{
    global  $logger;
    $api = explode('/', $uri);

    if ($api[0] == 'api' && count($api) > 2) {

        $class = $api[1];
        $params = array_slice($api, 3);

        if ($class == 'echo') {  //для  теста  /api/echo/параметр
            $response = "<echo>" . $api[2] . "</echo>";
        } else {

            try {


                require_once(_ROOT . DIRECTORY_SEPARATOR . strtolower("api" . DIRECTORY_SEPARATOR . $class . ".php"));

                $class = "\\ZippyERP\\API\\" . $class;

                $page = new $class;
                $response = call_user_func_array(array($page, $api[2]), $params);
            } catch (Exception $e) {
         
        $logger->error($e->getMessage(), e);
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
