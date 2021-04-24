<?php

//supplier_action.php

include_once('../config.php');
include_once(INC.'init.php');
include_once(CLASS_DIR.'file.php');
$file=new file();
$errors=array();
$table='supplier';
if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{	
		
		$email=$ims->clean_input($_POST["supplier_email"]);
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { array_push($errors, "Invalid email address <br>"); }
		$column= array('supplier_email', 'supplier_address', 'supplier_name');
		$value=	array(
			$email,			
				$ims->clean_input($_POST["supplier_name"]),	
			$ims->clean_input($_POST["supplier_address"]),				
		)		;
		if(count($errors)==0)
		$ims->insert($table,$column,$value);
			
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
		$result = $ims->getAllArray('supplier','supplier_id',$_POST["supplier_id"]);
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
    $data = array(	$ims->clean_input($_POST["supplier_name"]),
					$email,
					$ims->clean_input($_POST["supplier_address"]),	         
				);
	$supplier_id	=$ims->clean_input($_POST["supplier_id"]);
    $column = array("supplier_name", "supplier_email","supplier_address ");
	
	 if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK  )
	 {	
		 $result=$file->fileUpload($_FILES['profile_image'], IMAGES_DIR);
		 $imagename=$result[0];
		 $errors=$result[1];
		 $column[]="profile_image	";
		 $value[]=$imagename;	
	 }
	if(count($errors)==0)			
		$ims->UpdateDataColumn($table,$column,$data,'supplier_id ',$supplier_id);
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
		$ims->UpdateDataColumn('supplier','supplier_status',$status,'supplier_id',$_POST["supplier_id"]);
		$result = $ims->row();	
		if($result)
		{
			echo json_encode('<div class="alert alert-success">Supplier Status changed to ' . $status.'</div>');
		}
	}
}

if(isset($_POST['action']) && $_POST['action'] == 'fetch')
{

		$output = $column=$combine=$attr=$value=$output = array();
		if(isset($_POST["search"]["value"]) )
		{
			$column = array('supplier_email', 'supplier_name', 'supplier_status');
			$combine='OR';
			$attr['compare']=' LIKE  ';
			$value=array('%'.$_POST["search"]["value"]."%",
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
			$orderby='supplier_id';
		}
		
		if($_POST['length'] != -1)
			$limit=$_POST['start'] . ', ' . $_POST['length'];
			
		
		$result = $ims->getAllArray($table,$column,$value,$combine,$limit,$orderby,$order,'',$attr);
		$data = array();
		$filtered_rows = $ims->row();	

foreach($result as $row)
{
	
	if($row["supplier_status"] == 'Active')	
		$status = '<span class="badge badge-success">Active</span>';	
	else	
		$status = '<span class="badge badge-danger">Inactive</span>';
	
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
	"recordsTotal"  	=>  $command->count_total($table),
	"recordsFiltered" 	=> $filtered_rows,
	"data"    			=> 	$data
);
echo json_encode($output);
}

?>