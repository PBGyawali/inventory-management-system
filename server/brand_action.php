<?php

//brand_action.php

include_once('../config.php');
include_once(INC.'init.php');
$error = '';
$success = '';
$table='brand';
if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
			$brand_name=$ims->clean_input($_POST["brand_name"]);		
			$data = array($brand_name,$_POST["category_id"]);
			$count =$ims->CountTable('brand',array('brand_name'),array($brand_name));			
			if($count)			
				$error = '<div class="alert alert-danger">Brand Already Exists</div>';		
			else
			{	$ims->insert('brand',array('category_id', 'brand_name'),$data);				
				if($ims->row()>0)	
				$success = '<div class="alert alert-success">Brand Name Added</div>';
			}
			$output = array('error'	=>$error,'success'	=>	$success);
			echo json_encode($output);	
	}

	if($_POST['btn_action'] == 'fetch_single')
	{	$result =$ims->getArray('brand ','brand_id',$_POST["brand_id"]);		
		echo json_encode($result);
	}
	if($_POST['btn_action'] == 'Edit')
	{
			$category_id = $ims->clean_input($_POST['category_id']);
			$brand_id = $ims->clean_input($_POST['brand_id']);
			$brand_name=$ims->clean_input($_POST["brand_name"]);
			$count =$ims-> CountTable('brand',array('brand_name','brand_id!'),array($brand_name,$brand_id));			
			if($count)						
				$error = '<div class="alert alert-danger">Brand Already Exists</div>';		
			else
			{	
				$ims->UpdateDataColumn('brand',array('category_id','brand_name'),array($category_id,$brand_name),'brand_id',$_POST["brand_id"]);			
				if($ims->row())	
				$success = '<div class="alert alert-success">Brand Name Updated</div>';
			}
			$output = array('error'	=>	$error,	'success'=>	$success);
			echo json_encode($output);
	}
	if($_POST['btn_action'] == 'delete')
	{
		$status = 'active';
		if($_POST['status'] == 'active')		
			$status = 'inactive';		
		$ims->UpdateDataColumn('brand','brand_status',$status,'brand_id',$_POST["brand_id"]);
		if($ims->row())		
			echo json_encode('<div class="alert alert-info">Brand status changed to ' . $status.'</div>');
	}
}

if(isset($_POST['action'])&& $_POST['action'] == 'fetch')
{
	$output = $column=$combine=$attr=$value=$output = array();
	if(isset($_POST["search"]["value"]) )
	{
		$join = array("INNER JOIN"=> array("category"=>" category.category_id = brand.brand_id"));
		$column = array('brand.brand_name', 'category.category_name', 'brand.brand_status');
		$combine='OR';
		$attr['compare']=' LIKE  ';
		$value=array('%'.$_POST["search"]["value"]."%",
					'%'.$_POST["search"]["value"]."%",					
					'%'.$_POST["search"]["value"]."%");
	}
	if(isset($_POST['order']))
	{
		$order=$_POST['order']['0']['dir'];
		$orderby=$_POST['order']['0']['column'];
	}
	else
	{
		$order=' DESC';
		$orderby='brand.brand_id';
	}	
	if($_POST['length'] != -1)
		$limit=$_POST['start'] . ', ' . $_POST['length'];		
	
	$result = $ims->getAllArray($table,$column,$value,$combine,$limit,$orderby,$order,$join,$attr);
	$data = array();
	$filtered_rows = $ims->row();
foreach($result as $row)
{
	$status = '<span class="badge badge-danger">Inactive</span>';
	if($row['brand_status'] == 'active')
		$status = '<span class="badge badge-success">Active</span>';	
	
	$sub_array = array();
	$sub_array[] = $row['brand_id'];
	$sub_array[] = $row['category_name'];
	$sub_array[] = $row['brand_name'];
	$sub_array[] = $status;
	$button = '
	<div class="btn-group">
	  <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    Menu 
	  </button>
	  <ul class="dropdown-menu" >
	    <li><button type="button" name="update" id="'.$row["brand_id"].'"  class="w-100 mb-1 text-center btn btn-info btn-sm update"><i class="fas fa-edit"></i> Update</button></li>
	    <li><button type="button" name="delete" id="'.$row["brand_id"].'"  class="w-100 btn btn-primary btn-sm delete" data-status="'.$row["brand_status"].'">Change Status</button></li>       
	  </ul>
	</div>';
	$sub_array[] = $button;
	$data[] = $sub_array;
}

$output = array(
	"draw"				=>	intval($_POST["draw"]),
	"recordsTotal"		=>	$command->count_total($table),
	"recordsFiltered"	=>	$filtered_rows,
	"data"				=>	$data
);

echo json_encode($output);
}
?>