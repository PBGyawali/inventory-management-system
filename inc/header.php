<?php $website=(isset($_SESSION['website'])?$_SESSION['website']:'');
		$page=((isset($page)&&$page=='index')?'WELCOME TO':$page)
?>

<!DOCTYPE html>
		<html>
		<head>			
		<html class='no-js' lang='en'>		
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta content='IE=edge,chrome=1' http-equiv='X-UA-Compatible' />	
		<script type="text/javascript" src="<?php echo JS_URL.'jquery.min.js'?>"></script>	  
		<script type="text/javascript" src="<?php echo JS_URL.'popper.min.js'?>"></script>  	  	
		<script type="text/javascript" src="<?php echo JS_URL?>jquery-confirm.min.js"></script>
		<link rel="stylesheet" href="<?php echo CSS_URL?>bootstrap.min.css">			
		<link rel="stylesheet" href="<?php echo CSS_URL?>jquery-confirm.min.css">
		<script type="text/javascript" src="<?php echo JS_URL?>bootstrap.bundle.min.js"></script>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
		<script type="text/javascript" src="<?php echo JS_URL?>datatables.min.js"></script>		
		<script type="text/javascript" src="<?php echo JS_URL?>dataTables.responsive.min.js"></script>	
		<link rel="stylesheet" href="<?php echo CSS_URL?>datatables.min.css" >
		<link rel="stylesheet" href="<?php echo CSS_URL.'parsley.css'?>" >		
		<script type="text/javascript" src="<?php echo JS_URL.'parsley.min.js'?>"></script>		 		
		<title><?php echo ucwords(isset($page)?$page.' ':'').(isset($website)?$website.' ':'').SITE_NAME?></title>
		<?php include_once(INC.'sidebar.php');?>