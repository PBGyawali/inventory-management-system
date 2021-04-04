<?php

//tax_action.php

include_once('config.php');
include_once(INC.'init.php');
$error = '';
$success = '';
if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('tax_name', 'tax_percentage', 'tax_status');

		$output = array();

		$main_query = "	SELECT * FROM tax ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE tax_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR tax_percentage LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR tax_status LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))		
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';		
		else		
			$order_query = 'ORDER BY tax_id DESC ';		

		$limit_query = '';

		if($_POST["length"] != -1)		
			$limit_query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];		

		$ims->query = $main_query . $search_query . $order_query;

		$ims->execute();

		$filtered_rows = $ims->row();

		$ims->query .= $limit_query;

		$result = $ims->get_result();

		$ims->query = $main_query;

		$ims->execute();

		$total_rows = $ims->row();

		$data = array();

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
			"recordsTotal"  	=>  $total_rows,
			"recordsFiltered" 	=> 	$filtered_rows,
			"data"    			=> 	$data
		);
			
		echo json_encode($output);

	}

	if($_POST["action"] == 'Add')
	{
		
		$tax_name		=	$ims->clean_input($_POST["tax_name"]);
		$tax_percentage	=	$ims->clean_input($_POST["tax_percentage"]);
		$data = array(':tax_name'=>	$tax_name);
		$count =$ims-> CountTable('tax',array('tax_name'),array($tax_name));			
		if($count)	
			$error = '<div class="alert alert-danger">Tax Already Exists</div>';		
		else
		{
			$data = array(
				':tax_name'			=>	$ims->clean_input($_POST["tax_name"]),
				':tax_percentage'	=>	$ims->clean_input($_POST["tax_percentage"]),
			);

			$ims->query = "	INSERT INTO tax (tax_name, tax_percentage) 	VALUES (:tax_name, :tax_percentage)	";
			$ims->execute($data);
			$success = '<div class="alert alert-success">Tax Added</div>';
		}

		$output = array('error'		=>	$error,	'success'	=>	$success);
		echo json_encode($output);
	}

	if($_POST["action"] == 'fetch_single')
	{
		$ims->query = "	SELECT * FROM tax 	WHERE tax_id = '".$_POST["tax_id"]."'	";
		$result = $ims->get_result();
		$data = array();
		foreach($result as $row)
		{
			$data['tax_name'] = $row['tax_name'];
			$data['tax_percentage'] = $row['tax_percentage'];
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{		
		$tax_id = $ims->clean_input($_POST['hidden_id']);
		$tax_name=$ims->clean_input($_POST["tax_name"]);
		$tax_percentage=$ims->clean_input($_POST["tax_percentage"]);
		$data = array(
			':tax_name'			=>	$tax_name,
			':tax_id'			=>	$tax_id
		);
		$count =$ims-> CountTable('tax',array('tax_name,tax_id !'),array($tax_name,$tax_id));			
		if($count)			
			$error = '<div class="alert alert-danger">Tax Already Exists</div>';		
		else
		{
			$data =array(':tax_name'=>$tax_name,':tax_percentage'=>$tax_percentage);
			$ims->query = "UPDATE tax SET tax_name = :tax_name, tax_percentage = :tax_percentage  WHERE tax_id = '$tax_id'";
			$ims->execute($data);
			$success = '<div class="alert alert-success">Tax Updated</div>';
		}
		$output = array('error'	=>	$error,	'success'=>	$success);
		echo json_encode($output);
	}

	if($_POST["action"] == 'change_status')
	{
		$data = array(':tax_status'=>$_POST['next_status']);

		$ims->query = "	UPDATE tax 	SET tax_status = :tax_status 	WHERE tax_id = '".$_POST["id"]."'	";
		$ims->execute($data);
		echo json_encode('<div class="alert alert-success">Tax Status was made '.$_POST['next_status'].'</div>');
	}

	if($_POST["action"] == 'delete')
	{
		$ims->query = "	DELETE FROM tax WHERE tax_id = '".$_POST["id"]."'	";

		$ims->execute();

		echo json_encode('<div class="alert alert-success">Tax Deleted</div>');
	}
}

?>