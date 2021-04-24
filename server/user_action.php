<?php
include_once('../config.php');
include_once(INC.'init.php');
$errors=$finalresponse = array();
$imagename=$message=$result=$status='';
$table="user";
if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{	
		$password=$ims->clean_input($_POST["user_password"]);
		$email=$ims->clean_input($_POST["user_email"]);
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) array_push($errors, '<div class="alert alert-danger">Invalid email address </div><br>');
		$column=array(	'user_email','user_password','user_name');
		$value=	array($email,password_hash($password, PASSWORD_DEFAULT),$ims->clean_input($_POST["user_name"]));
		if(count($errors)==0)
		$ims->insert($table,$column,$value);		
		if($ims->row()>0)		
			$message='<div class="alert alert-success">New User Added </div>';		
		elseif($errors)
			$message=$errors ;
		else
			$message='<div class="alert alert-warning">There was some error. Please try again later </div>';
		echo json_encode($message);
		
	}
	if($_POST['btn_action'] == 'fetch_single')
	{	
		$result =$ims->getArray($table ,'user_id',$_POST["user_id"]);		
		echo json_encode($result);
	}

	if($_POST['btn_action'] == 'Edit')
  {    
	  $userid=$ims->clean_input($_POST["user_id"]);  
	  $value= array(
		$ims->clean_input($_POST["user_name"]),
		$ims->clean_input($_POST["user_email"])
	);
	$column = array('user_name','user_email');	
	 if(isset($_POST["user_password"])&&!empty($_POST["user_password"]))
	 {	 
		 $password=$ims->clean_input($_POST["old_password"]);
		 $newpassword=$ims->clean_input($_POST["user_password"]);
		 $confirmpassword=$ims->clean_input($_POST["user_re_enter_password"]);
		 $errors[]=$ims->is_empty(array('Current password'=>$password,'Confirm password'=>$confirmpassword));		 
		 $password=password_hash($password, PASSWORD_DEFAULT);
		 $column[]="user_password 	";
		 $value[]=$password;
	 }
	 if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK  )
	 {		
		 $result=$ims->fileUpload($_FILES['profile_image'], IMAGES_DIR);
		 $imagename=$result[0];
		 $errors[]=$result[1];
		 $column[]="profile_image	";
		 $value[]=$imagename;		 
	 }	 
	$attr=array();
	 //$attr['debug']=true;
	 if(count($errors)==0)	
		 $result=$ims->UpdateDataColumn('user',$column,$value,'user_id',$userid,'AND',$attr);
		 if($result)
			{ if($imagename)
				 $_SESSION['user_image']=$imagename;
				  if($ims->row()>0)		
					$message='<div class="alert alert-success">User details Updated </div>';
					else
						$message='<div class="alert alert-warning">No new change was made </div>';
			}
		 elseif($errors)
			$message='<div class="alert alert-danger">'.implode('AND',$errors).' </div>';
		else
			$message='<div class="alert alert-warning">There was some error </div>';
		
		echo json_encode($message);	 
  }	 

	if($_POST['btn_action'] == 'disable')
	{
		$status = 'Active';
		if($_POST['status'] == 'Active')
		{
			$status = 'Inactive';
		}		
		$ims->UpdateDataColumn('user','user_status',$status,'user_id',$_POST["user_id"]);
		$result = $ims->row();	
		if($result)
		{
			echo json_encode('<div class="alert alert-success">User Status changed to ' . $status.'</div>');
		}
	}
}

if(isset($_POST['action'])&& $_POST['action'] == 'fetch')
{

		$output = $column=$combine=$attr=$value=$output = array();
		if(isset($_POST["search"]["value"]) )
		{
			$column = array('user_type','(user_email', 'user_name', 'user_status');
			$combine=array('AND','OR','OR',')');
			$attr['compare']=array('=',' LIKE  ',' LIKE  ',' LIKE  ');
			//$attr['debug']='true';
			$value=array('user',
						'%'.$_POST["search"]["value"]."%",
						'%'.$_POST["search"]["value"]."%",
						'%'.$_POST["search"]["value"]."%"		
						);
		}
		if(isset($_POST['order']))
		{
			$order=$_POST['order']['0']['dir'];
			$orderby=$_POST['order']['0']['column'];
		}
		else
		{
			$order=' DESC';
			$orderby='user_id';
		}
		
		if($_POST['length'] != -1)
			$limit=$_POST['start'] . ', ' . $_POST['length'];
			
		
		$result = $ims->getAllArray($table,$column,$value,$combine,$limit,$orderby,$order,'',$attr);
		$data = array();
		if(is_array($result)){
		$filtered_rows = $ims->row();
		foreach($result as $row)
		{
			if($row["user_status"] == 'Active')			
				$status = '<span class="badge badge-success">Active</span>';			
			else			
				$status = '<span class="badge badge-danger">Inactive</span>';
			
			$userstatus=$row["user_status"];
			$sub_array = array();
			$sub_array[] = $row['user_id'];
			$sub_array[] = ucwords($row['user_name']);
			$sub_array[] = $row['user_email'];	
			$sub_array[] = $status;
			$button = '
			<div class="btn-group">
			<button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				Menu 
			</button>
			<ul class="dropdown-menu dropdown-menu-right" >
				<li><button type="button" name="update" id="'.$row["user_id"].'"  class="w-100 mb-1 text-center btn btn-info btn-sm update"><i class="fas fa-edit"></i> Update</button></li>
				<li><button type="button" name="delete" id="'.$row["user_id"].'"  class="w-100 btn '.(($userstatus=="Active")?'btn-danger':' btn-success').' btn-sm delete" data-status="'.$userstatus.'">'.(($userstatus=="Active")?"Disable":"Enable").'</button></li>       
			</ul>
			</div>';
			$sub_array[] = $button;	
			$data[] = $sub_array;
		}
		}
		else 
		{
			$filtered_rows = 1;
			$sub_array[] = $result;	
			$data[] = $sub_array;	
		}

		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  $filtered_rows,
			"recordsFiltered" 	=> 	$ims->CountTable($table,'user_type','user'),
			"data"    			=> 	$data
		);
		echo json_encode($output);



}

if(isset($_POST["login"]))
{
	$value=	$_POST["user_email"];
	$row = $ims->getArray($table,array('user_email','user_name'),array($value,$value),'OR');	
	$count = $ims->row();
	if($count > 0)
	{			
			if($row['user_status'] == 'Active')
			{
				if(password_verify($_POST["user_password"], $row["user_password"]))
				{
					$_SESSION['login'] = true;
					$_SESSION['type'] = $row['user_type'];
					$_SESSION['user_id'] = $row['user_id'];
					$_SESSION['user_name'] = $row['user_name'];
					$_SESSION['user_image'] = $row['profile_image'];
					$_SESSION['website']=$ims->website_name();                       
                    $status ='success';                       
				}
				else				
					$message = 'Wrong Password';				
			}
			else			
				$message = 'Your account is disabled, Please contact Admin';
	}
	else
	{
		$message ='Wrong Username or Email Address';
    }
    
    $finalresponse = array( 'error' => '<div class="alert alert-danger">'.$message.'</div>', 
							'status' => $status, 
							'login'=>$ims->dashboard);
    echo json_encode( $finalresponse ); 
    $ims->close();
}

?>