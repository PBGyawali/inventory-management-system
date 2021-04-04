<?php

//supplier_action.php

include_once('config.php');
include_once(INC.'init.php');
include_once(CLASS_DIR.'file.php');
$file=new file();
$errors=array();
if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{	
		
		$email=$ims->clean_input($_POST["supplier_email"]);
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { array_push($errors, "Invalid email address <br>"); }
		$ims->query = "INSERT INTO supplier (supplier_email, supplier_address, supplier_name) 
		VALUES (:supplier_email, :supplier_address, :supplier_name)	";
		if(count($errors)==0)
		$ims->execute(
			array(
				':supplier_email'		=>	$email,			
				':supplier_name'		=>	$ims->clean_input($_POST["supplier_name"]),	
				':supplier_address'		=>	$ims->clean_input($_POST["supplier_address"]),				
			)
		);
		if($ims->row()>0)		
			$message='<div class="alert alert-success">New Supplier Added </div>';		
		elseif($errors)
			$message='<div class="alert alert-danger">'.$errors .'</div>';
		else
			$message='<div class="alert alert-warning">There was some error. Please try again later </div>';
		echo json_encode($message);		
	}
	if($_POST['btn_action'] == 'fetch_single')
	{
		$ims->query = "SELECT * FROM supplier WHERE supplier_id = :supplier_id";	
		$ims->execute(array('supplier_id'	=>	$_POST["supplier_id"]));
		$result = $ims->statement_result();
		foreach($result as $row)
		{
			$output['supplier_email'] = $row['supplier_email'];
			$output['supplier_name'] = ucwords($row['supplier_name']);
			$output['supplier_address']	=$row['supplier_address'];	
		}
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
  { 
	$email=$ims->clean_input($_POST["supplier_email"]);
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { array_push($errors, "Invalid email address <br>"); }     
    $data = array(
      ':supplier_name'		=>	$ims->clean_input($_POST["supplier_name"]),
	  ':supplier_email'		=>	$email,
	  ':supplier_id'		=>	$ims->clean_input($_POST["supplier_id"]),
	  ':supplier_address'	=>	$ims->clean_input($_POST["supplier_address"]),	         
	);
	
    $ims->query = "UPDATE supplier  SET supplier_name = :supplier_name, supplier_email = :supplier_email,supplier_address = :supplier_address ";
	
	 if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK  )
	 {		
		 $result=$file->fileUpload($_FILES['profile_image'], IMAGES_DIR);
		 $imagename=$result[0];
		 $errors=$result[1];
		 $ims->query .= ",profile_image = :profile_image ";
		 $newdata=array(':profile_image'	=>$imagename);
		 $data = array_merge($data, $newdata);
	 }
	$ims->query .=" WHERE  supplier_id = :supplier_id";
	if(count($errors)==0)		
		$ims->execute($data);
		if($ims->row()>0)		
			$message='<div class="alert alert-success">New Supplier Added </div>';		
		elseif($errors)
			$message='<div class="alert alert-danger">'.$errors .'</div>';
		else
			$message='<div class="alert alert-warning">There was some error. Please try again later </div>';
		echo json_encode($message);		
  }	 

	if($_POST['btn_action'] == 'disable')
	{
		$status = 'Active';
		if($_POST['status'] == 'Active')
		{
			$status = 'Inactive';
		}
		$ims->query = "UPDATE supplier SET supplier_status = :supplier_status WHERE supplier_id = :supplier_id";		
		$ims->execute(array(':supplier_status'=>$status,':supplier_id'=>$_POST["supplier_id"]));	
		$result = $ims->row();	
		if($result)
		{
			echo json_encode('<div class="alert alert-success">Supplier Status changed to ' . $status.'</div>');
		}
	}
}


?>
<?php
if(isset($_POST['action'])&& $_POST['action'] == 'fetch')
{
$query = '';

$output = array();

$query .= "SELECT * FROM supplier WHERE  ";

if(isset($_POST["search"]["value"]))
{
	$query .= '(supplier_email LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR supplier_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR supplier_status LIKE "%'.$_POST["search"]["value"].'%") ';
}

if(isset($_POST["order"]))
{
	$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
	$query .= 'ORDER BY supplier_id DESC ';
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
	if($row["supplier_status"] == 'Active')
	{
		$status = '<span class="badge badge-success">Active</span>';
	}
	else
	{
		$status = '<span class="badge badge-danger">Inactive</span>';
	}
	$supplierstatus=$row["supplier_status"];
	$sub_array = array();
	$sub_array[] = $row['supplier_id'];
	$sub_array[] = ucwords($row['supplier_name']);
	$sub_array[] = $row['supplier_email'];	
	$sub_array[] = $status;
	$button = '
	<div class="btn-group">
	  <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    Menu 
	  </button>
	  <ul class="dropdown-menu" >
	  	<li><button type="button" name="update" id="'.$row["supplier_id"].'"  class="w-100  mb-1 text-center btn btn-info btn-sm update"><i class="fas fa-edit"></i> Update</button></li>
	    <li><button type="button" name="delete" id="'.$row["supplier_id"].'"  class="w-100 btn '.(($supplierstatus=="Active")?'btn-danger':' btn-success').' btn-sm delete" data-status="'.$supplierstatus.'">'.(($supplierstatus=="Active")?"Disable":"Enable").'</button></li>       
	  </ul>
	</div>';
	$sub_array[] = $button;
	
	$data[] = $sub_array;
}

$output = array(
	"draw"				=>	intval($_POST["draw"]),
	"recordsTotal"  	=>  $filtered_rows,
	"recordsFiltered" 	=> $command->count_total_supplier(),
	"data"    			=> 	$data
);
echo json_encode($output);
}

?>