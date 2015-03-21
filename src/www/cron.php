<?php

require_once 'init.php';

session_start();
try {
     $conn = \ZCL\DB\DB::getConnect();

     $conn->Execute("OPTIMIZE TABLE system_session");
     

$logger->info("Cron");
}  catch (Exception $e) {
    echo $e->getMessage();
    $logger->error($e);
}

