<?php

//setting_action.php

include_once('../config.php');
include_once(INC.'init.php');

if(isset($_POST["company_name"]))
{
	$setup=false;
	if(isset($_SESSION['setup']) &&!empty($_SESSION['setup']))
	{
		$setup=true;
	}
	$selectvalue=explode('***',$_POST["company_currency"]);
	$company_currency=	$selectvalue[0];
	$currency_symbol=	$selectvalue[1];
	$company_name=$ims->clean_input($_POST["company_name"]);
	$sql="UPDATE ";
	if($setup)
		$sql="INSERT INTO ";
	$data = array(
		':company_name'		 	 =>	$company_name,
		':company_address'	  	 =>	$ims->clean_input(	$_POST["company_address"]),
		':company_contact_no'	 =>	$ims->clean_input($_POST["company_contact_no"]),
		':company_email'	 	 =>	$ims->clean_input($_POST["company_email"]),
		':company_sales_target'	 =>	$ims->clean_input($_POST["company_sales_target"]),
		':company_revenue_target'=>	$ims->clean_input($_POST["company_revenue_target"]),
		':company_currency'	 	 =>	$ims->clean_input($company_currency),
		':currency_symbol'	 	 =>	$ims->clean_input($currency_symbol),
		':company_timezone'	     =>	$ims->clean_input($_POST["company_timezone"]),
		
	);
	$ims->query = $sql." company_table 	SET company_name = :company_name, company_address = :company_address,
	 company_contact_no = :company_contact_no,company_email = :company_email,company_sales_target = :company_sales_target,
	 company_revenue_target = :company_revenue_target,company_currency = :company_currency, 
	 currency_symbol= :currency_symbol, company_timezone = :company_timezone ";
	$ims->execute($data);
	if($ims->row()>0)
			{
				$_SESSION['setup']='';
				$_SESSION['website']=$company_name;
				$message='<div class="alert alert-success">Details Updated Successfully</div>';
			}
	else
	{
		$message='<div class="alert alert-warning">No change was made from this update</div>';
	}

	if(isset($_POST["admin_email"]))
	{	$password=$ims->clean_input($_POST["admin_password"]);
		$master_user_column = array('user_name','user_email','user_password','user_type');
		$master_user_data = array(
				$ims->clean_input($_POST["admin_email"]),
				$ims->clean_input($_POST["admin_email"]),
				password_hash($password, PASSWORD_DEFAULT),
				'master',
		);			
		$ims->insert('user',$master_user_column,$master_user_data);		
		if($ims->row()>0)
			{
				$_SESSION['setup']='';
				
				$message = '<div class="alert alert-success">Your Account is Created, Now you can Login</div>';
			}
	}

	
	$data = array();
	$data['success'] = $message;

	echo json_encode($data);
}



?>