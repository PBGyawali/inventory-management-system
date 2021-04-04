<?php

//user_action.php

include_once('config.php');
include_once(INC.'init.php');
include_once(CLASS_DIR.'file.php');
$file=new file();
$errors=array();
$message='';
if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{	
		$password=$ims->clean_input($_POST["user_password"]);
		$email=$ims->clean_input($_POST["user_email"]);
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) array_push($errors, '<div class="alert alert-danger">Invalid email address </div><br>');
		$ims->query = "INSERT INTO user (user_email, user_password, user_name) 
		VALUES (:user_email, :user_password, :user_name)	";
		if(count($errors)==0)
		$ims->execute(
			array(
				':user_email'		=>	$email,
				':user_password'	=>	password_hash($password, PASSWORD_DEFAULT),
				':user_name'		=>	$ims->clean_input($_POST["user_name"]),
			)
		);	
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
		$ims->query = "SELECT * FROM user WHERE user_id = :user_id";	
		$ims->execute(array('user_id'	=>	$_POST["user_id"]));
		$result = $ims->statement_result();
		foreach($result as $row)
		{
			$output['user_email'] = $row['user_email'];
			$output['user_name'] = $row['user_name'];			
		}
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
  {      
    $data = array(
      ':user_name'		=>	$ims->clean_input($_POST["user_name"]),
	  ':user_email'		=>	$ims->clean_input($_POST["user_email"]),
	  ':user_id'		=>	$ims->clean_input($_POST["user_id"]),	         
	);
	
    $ims->query = "UPDATE user  SET user_name = :user_name, user_email = :user_email ";
	 if(isset($_POST["user_password"])&& !empty($_POST["user_password"]))
	 {	 
		 $password=$ims->clean_input($_POST["user_password"]);
		 $ims->query .= " , user_password =:user_password	";
		 $newdata=array(':user_password'	=>password_hash($password, PASSWORD_DEFAULT));
		 $data = array_merge($data, $newdata);
	 }
	 if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK  )
	 {		
		 $result=$file->fileUpload($_FILES['profile_image'], IMAGES_DIR);
		 $imagename=$result[0];
		 $errors=$result[1];
		 $ims->query .= ",profile_image = :profile_image ";
		 $newdata=array(':profile_image'	=>$imagename);
		 $data = array_merge($data, $newdata);
	 }
	 $ims->query .=" WHERE  user_id = :user_id";
	 if(count($errors)==0)		
		$ims->execute($data);
		if($ims->row()>0)		
			$message='<div class="alert alert-success">User details Updated </div>';		
		elseif($errors)
			$message=$errors;
		else
			$message='<div class="alert alert-warning">No new change was made </div>';
		echo json_encode($message);	 
  }	 

	if($_POST['btn_action'] == 'disable')
	{
		$status = 'Active';
		if($_POST['status'] == 'Active')
		{
			$status = 'Inactive';
		}
		$ims->query = "UPDATE user SET user_status = :user_status WHERE user_id = :user_id";		
		$ims->execute(array(':user_status'=>$status,':user_id'=>$_POST["user_id"]));	
		$result = $ims->row();	
		if($result)
		{
			echo json_encode('<div class="alert alert-success">User Status changed to ' . $status.'</div>');
		}
	}
}


?>
<?php
if(isset($_POST['action'])&& $_POST['action'] == 'fetch')
{
$query = '';

$output = array();

$query .= "SELECT * FROM user WHERE user_type = 'user' AND ";

if(isset($_POST["search"]["value"]))
{
	$query .= '(user_email LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR user_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR user_status LIKE "%'.$_POST["search"]["value"].'%") ';
}

if(isset($_POST["order"]))
{
	$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
	$query .= 'ORDER BY user_id DESC ';
}

if($_POST["length"] != -1)
{
	$query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$ims->query=$query;

$ims->execute();

$result = $ims->statement_result();

$data = array();

$filtered_rows = $ims->row();

foreach($result as $row)
{
	$status = '';
	if($row["user_status"] == 'Active')
	{
		$status = '<span class="badge badge-success">Active</span>';
	}
	else
	{
		$status = '<span class="badge badge-danger">Inactive</span>';
	}
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

$output = array(
	"draw"				=>	intval($_POST["draw"]),
	"recordsTotal"  	=>  $filtered_rows,
	"recordsFiltered" 	=> 	$ims->CountTable('user','user_type','user'),
	"data"    			=> 	$data
);
echo json_encode($output);
}



?>