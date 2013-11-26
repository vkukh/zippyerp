<?php
require_once 'init.php';  

session_start();
try {
  $conn =  \ZCL\DB\DB::getConnect();
  
 // $conn->Execute("OPTIMIZE TABLE system_session");        
     
} catch (\ZippyERP\Core\Exception $e) {
        Logger::getLogger("cron")->error($e->getMessage(), e);
        echo $e->getMessage();
} catch (\Zippy\Exception $e) {
        echo $e->getMessage();
        Logger::getLogger("cron")->error($e->getMessage(), e);
} catch (ADODB_Exception $e) {
        echo $e->getMessage();
        Logger::getLogger("cron")->error($e->getMessage(), e);
} catch (Exception $e) {
        echo $e->getMessage();
        Logger::getLogger("cron")->error($e->getMessage(), e);
}

