<?php



require_once 'init.php';

try {



    $app = new \ZippyERP\System\Application('\ZippyERP\ERP\Pages\Main', $modules);
  

    
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

