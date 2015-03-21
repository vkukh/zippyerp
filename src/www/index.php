<?php


require_once 'init.php';

try {


    $app = new \ZippyERP\System\Application('\ZippyERP\ERP\Pages\Main', $modules);

    //  $app->getResponse()->setGzip($_config['common']['gzip']);
    //ZippyERP\System\System::setUser(ZippyERP\System\User::getByLogin("admin"));        ZippyERP\System\System::setUser(ZippyERP\Core\User::getByLogin("admin"));

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
       
       \ZCL\DB\DB::getConnect()->CompleteTrans(false); // откат транзакции
    }
    
    $logger->error($e);
    \ZippyERP\System\Application::Redirect('\\ZippyERP\\System\\Pages\\Error', $e->getMessage());
}

