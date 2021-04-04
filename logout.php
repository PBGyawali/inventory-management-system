<?php
//logout.php
include_once('config.php');
include_once(INC.'init.php');
$_SESSION=array();
session_destroy();


header("location:".LOGIN_URL);

?>