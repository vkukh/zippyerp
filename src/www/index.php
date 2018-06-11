<?php



require_once 'init.php';

try {

       if ($_COOKIE['remember'] && \ZippyERP\System\System::getUser()->user_id == 0) {
            $arr = explode('_', $_COOKIE['remember']);
            $_config = parse_ini_file(_ROOT . 'config/config.ini', true);
            if ($arr[0] > 0 && $arr[1] === md5($arr[0] . $_config['common']['salt'])) {
                $user = \ZippyERP\System\User::load($arr[0]);
            }

            if ($user instanceof \ZippyERP\System\User) {


                \ZippyERP\System\System::setUser($user);

                $_SESSION['user_id'] = $user->user_id; //для  использования  вне  Application
                $_SESSION['userlogin'] = $user->userlogin; //для  использования  вне  Application
            }   

        }

      $app = new \Zippy\WebApplication('\ZippyERP\ERP\Pages\Main');
      //функции для  загрузки шаблонов страницы
      $app->setTemplate("\\ZippyERP\\System\\getTemplate");
      $app->setTemplate("\\ZippyERP\\ERP\\getTemplate");
      $app->setTemplate("\\ZippyERP\\Shop\\getTemplate");
      //функции дляроутинга 
      $app->setRoute("\\ZippyERP\\System\\Route");
      $app->setRoute("\\ZippyERP\\ERP\\Route");
      $app->setRoute("\\ZippyERP\\Shop\\Route");
      
    
    
    
    $app->Run();



    /* } catch (\ZippyERP\System\Exception $e) {
      Logger::getLogger("main")->error($e->getMessage(), e);
      \ZippyERP\System\Application::Redirect('\\ZippyERP\\System\\Pages\\Error', $e->getMessage());
      } catch (\Zippy\Exception $e) {
      Logger::getLogger("main")->error($e->getMessage(), e);
      \ZippyERP\System\Application::Redirect('\\ZippyERP\\System\\Pages\\Error', $e->getMessage());
      } catch (ADODB_Exception $e) {

      \ZippyERP\System\Application::Redirect('\\ZippyERP\\System\\Pages\\Error', $e->getMessage());
     */
} 
catch (Throwable $e) {
    if($e  instanceof ADODB_Exception){

       \ZDB\DB::getConnect()->CompleteTrans(false); // откат транзакции
    }
    $msg =    $e->getMessage() ;
    $logger->error($e);
    if($e  instanceof Error ){
        echo $e->getMessage().'<br>';
        echo $e->getLine().'<br>';
        echo $e->getFile().'<br>';
    }
}
catch (Excption $e) {    //для обратной совместимости
    if($e  instanceof ADODB_Exception){

       \ZDB\DB::getConnect()->CompleteTrans(false); // откат транзакции
    }
    $msg =    $e->getMessage() ;
    $logger->error($e);
   
}

