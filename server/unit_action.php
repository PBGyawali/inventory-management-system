<?php

//unit_action.php

include_once('../config.php');
include_once(INC.'init.php');
$error = '';
$success = '';
$table='unit';
if(isset($_POST['btn_action']))
{

	if($_POST["btn_action"] == 'Add')
	{	
		$unit_name=	$ims->clean_input($_POST["unit_name"]);		
		$data = array('unit_name');				
		$count =$ims-> CountTable('unit','unit_name',$unit_name);			
		if($count)				
			$error = '<div class="alert alert-danger">Unit Already Exists</div>';		
		else
		{	$ims->insert('unit',$data,$unit_name);
			if($ims->row()>0)	
			$success = '<div class="alert alert-success">Unit Added</div>';
		}
		$output = array('error'	=>$error,'success'	=>	$success);
		echo json_encode($output);
	}	
	
	if($_POST['btn_action'] == 'fetch_single')
	{
		$result =$ims->getArray('unit ','unit_id',$_POST["unit_id"]);		
		echo json_encode($result['unit_name']);
	}

	if($_POST['btn_action'] == 'Edit')
	{		
		$unit_id = $ims->clean_input($_POST['unit_id']);
		$unit_name=$ims->clean_input($_POST["unit_name"]);
		$count =$ims-> CountTable('unit',array('unit_name,unit_id !'),array($unit_name,$unit_id));			
		if($count)				
			$error = '<div class="alert alert-danger">Unit Already Exists</div>';		
		else
		{	
			$ims->UpdateDataColumn('unit','unit_name',$unit_name,'unit_id',$unit_id);
			if($ims->row())	
			$success = '<div class="alert alert-success">Unit Name Updated</div>';
		}
		$output = array('error'	=>	$error,	'success'=>	$success);
		echo json_encode($output);
	}


	if($_POST['btn_action'] == 'delete')
	{
		$status = 'active';
		if($_POST['status'] == 'active')	
			$status = 'inactive';			
		$ims->UpdateDataColumn('unit','unit_status',$status,'unit_id',$_POST["unit_id"]);
		$result = $ims->row();	
		if($result)
		{
			echo json_encode('<div class="alert alert-success">Unit Status changed to ' . $status.'</div>');
		}
	}
}


if(isset($_POST['action'])&& $_POST['action'] == 'fetch')
{
$column=$combine=$attr=$value=$output = array();
if(isset($_POST["search"]["value"]) )
{
	$column=array('unit_name','unit_status');
	$combine='OR';
	$attr['compare']=' LIKE  ';
	$value=array('%'.$_POST["search"]["value"]."%",'%'.$_POST["search"]["value"]."%");
}
if(isset($_POST['order']))
{
	$order=$_POST['order']['0']['dir'];
	$orderby=$_POST['order']['0']['column'];
}
else
{
	$order=' DESC';
	$orderby='unit_id';
}

if($_POST['length'] != -1)
	$limit=$_POST['start'] . ', ' . $_POST['length'];
	

$result = $ims->getAllArray($table,$column,$value,$combine,$limit,$orderby,$order,'',$attr);
$data = array();
$filtered_rows =$ims->row();
foreach($result as $row)
{
	$status = '';
	if($row['unit_status'] == 'active')
	{
		$status = '<span class="badge badge-success">Active</span>';
	}
	else
	{
		$status = '<span class="badge badge-danger">Inactive</span>';
	}
	$sub_array = array();
	$sub_array[] = $row['unit_id'];
	$sub_array[] = $row['unit_name'];
	$sub_array[] = $status;
	$button = '
	<div class="btn-group">
	  <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    Menu 
	  </button>
	  <ul class="dropdown-menu" >
	    <li><button type="button" name="update" id="'.$row["unit_id"].'"  class="w-100 mb-1 text-center btn btn-info btn-sm update"><i class="fas fa-edit"></i> Update</button></li>
	    <li><button type="button" name="delete" id="'.$row["unit_id"].'"  class="w-100 btn btn-primary btn-sm delete" data-status="'.$row["unit_status"].'">Change Status</button></li>       
	  </ul>
	</div>';
	$sub_array[] = $button;	
	$data[] = $sub_array;
}

$output = array(
	"draw"			=>	intval($_POST["draw"]),
	"recordsTotal"  	=>  $filtered_rows,
	"recordsFiltered" 	=> $command->count_total('unit'),
	"data"				=>	$data
);
echo json_encode($output);
}
?>