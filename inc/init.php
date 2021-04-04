<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once (CLASS_DIR.'ims.php');
include_once (CLASS_DIR.'command.php');
if (session_status() === PHP_SESSION_NONE)
    session_start();
$ims=new ims();
$command=new command();

?>