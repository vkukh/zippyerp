<?php

define('_ROOT', __DIR__ . '/');
$http = $_SERVER["HTTPS"] == 'on' ? 'https' : 'http';
define('_BASEURL', $http . "://" . $_SERVER["HTTP_HOST"] . '/');

define('UPLOAD_USERS', 'uploads/users/');

error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Europe/Kiev');

require_once _ROOT . 'vendor/autoload.php';
include_once _ROOT . "vendor/adodb/adodb-php/adodb-exceptions.inc.php";


//чтение  конфигурации
$_config = parse_ini_file(_ROOT . 'config/config.ini', true);

//  phpQuery::$debug = true;

//Параметры   соединения  с  БД
\ZDB\DB::config($_config['db']['host'], $_config['db']['name'], $_config['db']['user'], $_config['db']['pass']);

 

//подключение  ядра системмы
require_once _ROOT . 'system/start.inc.php';
require_once _ROOT . 'erp/start.inc.php';


//загружаем  модули
$modules = array();
/*
  $modulespath = _ROOT . 'modules/';
  if ($handle = @opendir($modulespath)) {
  while (false !== ($file = readdir($handle))) {
  if (is_dir($modulespath . $file) && strlen($file) > 2) {
  $startfile = $modulespath . $file . '/start.inc.php';
  if(file_exists($startfile)){
  $modules[] = $file;
  require_once $startfile;
  }

  }
  }
  closedir($handle);
  }
 */

session_start();

//подключаем  локализацию
//$lang = \ZippyERP\System\System::getLang();
//require_once _ZIPPY ."lang/{$lang}.php";

/*
  foreach ($modules as $module) {
  $file = $modulespath .$module  ."/lang/{$lang}.php";
  if(file_exists($file)){
  require_once $file;
  }

  }
 */

// логгер
$logger = new \Monolog\Logger("main");
$dateFormat = "Y n j, g:i a";
//$output = "%datetime% > %level_name% > %message% %context% %extra%\n";
$output = "%datetime%  %level_name% : %message% \n";
$formatter = new \Monolog\Formatter\LineFormatter($output, $dateFormat);
$h1 = new \Monolog\Handler\RotatingFileHandler(_ROOT . "logs/app.log", 10, $_config['common']['loglevel']);
$h2 = new \Monolog\Handler\RotatingFileHandler(_ROOT . "logs/error.log", 10, 400);
$h1->setFormatter($formatter);
$h2->setFormatter($formatter);
$logger->pushHandler($h1);
$logger->pushHandler($h2);
$logger->pushProcessor(new \Monolog\Processor\IntrospectionProcessor());

@mkdir(_ROOT . "logs");



