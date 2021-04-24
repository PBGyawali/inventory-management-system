<?php

//tax_action.php

include_once('../config.php');
include_once(INC.'init.php');
$error = '';
$success = '';
$table='tax';                                                                                                                                            
if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('tax_name', 'tax_percentage', 'tax_status');
		$output = $column=$combine=$attr=$value=$output = array();
		if(isset($_POST["search"]["value"]) )
		{
			$column = array('tax_name', 'tax_percentage', 'tax_status');
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
			$orderby='tax_id';
		}
		
		if($_POST['length'] != -1)
			$limit=$_POST['start'] . ', ' . $_POST['length'];
			
		
		$result = $ims->getAllArray($table,$column,$value,$combine,$limit,$orderby,$order,'',$attr);
		$data = array();
		$filtered_rows = $ims->row();	

		foreach($result as $row)
		{
			$sub_array = array();
			$sub_array[] = html_entity_decode($row["tax_name"]);
			$sub_array[] = $row["tax_percentage"] . '%';
			$status = '';
			if($row["tax_status"] == 'active')			
				$status = '<button type="button" name="status_button" class="btn btn-success btn-sm status_button" data-id="'.$row["tax_id"].'" data-status="'.$row["tax_status"].'">Active</button>';
			else			
				$status = '<button type="button" name="status_button" class="btn btn-danger btn-sm status_button" data-id="'.$row["tax_id"].'" data-status="'.$row["tax_status"].'">Inactive</button>';
			$sub_array[] = $status;
			$button = '
	<div class="btn-group">
	  <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    Menu 
	  </button>
	  <ul class="dropdown-menu" >
	  	
	    <li><button type="button" name="update" data-id="'.$row["tax_id"].'"  class="w-100 mb-1 text-center btn btn-info btn-sm edit_button"><i class="fas fa-edit"></i> Update</button></li>
	    <li><button type="button" name="delete" data-id="'.$row["tax_id"].'" class="w-100 btn btn-danger btn-sm delete_button" ><i class="fas fa-times"></i> Delete</button></li>       
	  </ul>
	</div>';
	$sub_array[] = $button;		
	$data[] = $sub_array;
		}
		$output = array(
			"draw"    			=> 	intval($_POST["draw"]),
			"recordsTotal"  	=>  $command->count_total($table),
			"recordsFiltered" 	=> 	$filtered_rows,
			"data"    			=> 	$data
		);			
		echo json_encode($output);
	}

	if($_POST["action"] == 'Add')
	{		
		$tax_name		=	$ims->clean_input($_POST["tax_name"]);
		$tax_percentage	=	$ims->clean_input($_POST["tax_percentage"]);	
		$count =$ims-> CountTable('tax',array('tax_name'),array($tax_name));			
		if($count)	
			$error = '<div class="alert alert-danger">Tax Already Exists</div>';		
		else
		{
			$column = array('tax_name'	,'tax_percentage');
			$data = array($tax_name,$tax_percentage);

			$ims->insert('tax',$column,$data);			
			$success = '<div class="alert alert-success">Tax Added</div>';
		}

		$output = array('error'		=>	$error,	'success'	=>	$success);
		echo json_encode($output);
	}

	if($_POST["action"] == 'fetch_single')
	{	
		$data =$ims->getArray('tax','tax_id',$_POST["tax_id"]);
		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{		
		$tax_id = $ims->clean_input($_POST['hidden_id']);
		$tax_name=$ims->clean_input($_POST["tax_name"]);
		$tax_percentage=$ims->clean_input($_POST["tax_percentage"]);		
		$count =$ims-> CountTable('tax',array('tax_name,tax_id !'),array($tax_name,$tax_id));			
		if($count)			
			$error = '<div class="alert alert-danger">Tax Already Exists</div>';		
		else
		{
			$column =array('tax_name','tax_percentage');
			$data =array($tax_name,$tax_percentage);
			$ims->UpdateDataColumn('tax',$column,$data,'tax_id',$tax_id);			
			$success = '<div class="alert alert-success">Tax Updated</div>';
		}
		$output = array('error'	=>	$error,	'success'=>	$success);
		echo json_encode($output);
	}

	if($_POST["action"] == 'change_status'){
		$tax_id = $ims->clean_input($_POST['id']);
		$column = array('tax_status');
		$data = array($_POST['next_status']);
		$ims->UpdateDataColumn('tax',$column,$data,'tax_id',$tax_id);		
		echo json_encode('<div class="alert alert-success">Tax Status was made '.$_POST['next_status'].'</div>');
	}

	if($_POST["action"] == 'delete'){	
		$ims->Delete('tax','tax_id',$_POST["id"]);		
		echo json_encode('<div class="alert alert-success">Tax Deleted</div>');
	}
}

?>