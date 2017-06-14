<?php



require_once 'init.php';

try {



    $app = new \Zippy\WebApplication('\ZippyERP\ERP\Pages\Main');
  
  
    
    
    
      $app->setTemplate("\\ZippyERP\\System\\getTemplate");
      $app->setTemplate("\\ZippyERP\\ERP\\getTemplate");
      
      $app->setRoute("\\ZippyERP\\System\\Route");
      
    
    
    
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
} catch (Exception $e) {
    if($e  instanceof ADODB_Exception){

       \ZDB\DB::getConnect()->CompleteTrans(false); // откат транзакции
    }
    $msg =    $e->getMessage() ;
    $logger->error($e);
   // \ZippyERP\System\Application::Redirect('\\ZippyERP\\System\\Pages\\Error', $e->getMessage());
}

