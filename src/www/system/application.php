<?php

namespace ZippyERP\System;

/**
 * Класс  приложения, выполняющий
 * жизненный  цикл  работы  сайта
 */
class Application extends \Zippy\WebApplication
{

    private $modules = array(); //список   модулей

    public function __construct($homepage, $modules)
    {
        parent::__construct($homepage);

        $this->set404('templates/404.html');
        $this->modules = $modules;
    }

    /**
     * Возвращает  шаблон  страницы
     *
     * @param mixed $name
     * @param mixed $layout
     */
    public function getTemplate($name, $layout = '')
    {

        $templatepath = _ROOT . 'templates/';
        $path = '';
        $name = ltrim($name, '\\');
        $arr = explode('\\', $name);


        $path = \ZippyERP\System\getTemplate($templatepath, $name, $layout);

        if (strlen($path) == 0) {
            $path = \ZippyERP\ERP\getTemplate($templatepath, $name, $layout);
        }


        //если не  системная страница  вызываем  соответствуюзие
        //загрузчики  шаблонов  для модулей
        /*
          if (strlen($path) == 0) { //modules
          if (in_array(strtolower($arr[1]), $this->modules)) {
          $func = '\ZippyERP\\' . $arr[1] . '\getTemplate';
          if (function_exists($func)) {
          $path = $func($templatepath . '/modules/' . strtolower($arr[1]) . '/', $name, $layout);
          }
          }
          } */

        if (file_exists(strtolower($path))== false) {
            throw new \ZippyERP\System\Exception('Invalid template path: ' . strtolower($path));
        }
        $template = @file_get_contents(strtolower($path));

        return $template;
    }

    /**
     * Роутер.  Вызывает  соответствующие  функции  для  модулей
     *
     * @param mixed $uri
     */
    public function Route($uri)
    {


        if (preg_match('/^[-#a-zA-Z0-9\/_]+$/', $uri) == 0) {
            new \Zippy\Exception('Invalid URI: ' . $uri);
        }


        //Системный  роутер
        $route = '\ZippyERP\\System\\Route';
        if (function_exists($route)) {

            $route($uri);
        }


        foreach ($this->modules as $module) {
            $route = '\ZippyERP\\' . $module . '\Route';
            if (function_exists($route)) {

                $route($uri);
            }
        }
    }

    

    /**
     * Редирект на  страницу с  ошибкой
     * 
     */
    public static function RedirectError($message)
    {
        self::$app->getResponse()->Redirect("\\ZippyERP\\Pages\\Error", $message);
    }

  

}
