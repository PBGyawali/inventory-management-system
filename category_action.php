<?php

//category_action.php

include_once('config.php');
include_once(INC.'init.php');
$error = '';
$success = '';
if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{		
			$category_name=	$ims->clean_input($_POST["category_name"]);		
			$data = array(':category_name'=>$category_name);
			$count =$ims-> CountTable('category',array('category_name'),array($category_name));			
			if($count)			
				$error = '<div class="alert alert-danger">Category Already Exists</div>';		
			else
			{			
				$ims->query = "	INSERT INTO category (category_name) VALUES (:category_name)	";
				$ims->execute($data);
				if($ims->row()>0)	
				$success = '<div class="alert alert-success">Category Name Added</div>';
			}
			$output = array('error'	=>$error,'success'	=>	$success);
			echo json_encode($output);	
	}
	
	if($_POST['btn_action'] == 'fetch_single')
	{
		$ims->query = "SELECT * FROM category WHERE category_id = :category_id";		
		$ims->execute(
			array(
				':category_id'	=>	$_POST["category_id"]
			)
		);
		$result =$ims->statement_result();
		foreach($result as $row)
		{
			$output['category_name'] = $row['category_name'];
		}
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
	{
			$category_id = $ims->clean_input($_POST['category_id']);
			$category_name=$ims->clean_input($_POST["category_name"]);		
			$data = array(
				':category_name'		=>	$category_name,
				':category_id'			=>	$category_id
			);
			$count =$ims-> CountTable('category',array('category_name,category_id !'),array($category_name,$category_id));			
			if($count)		
				$error = '<div class="alert alert-danger">Category Already Exists</div>';		
			else
			{			
				$ims->query = "UPDATE category SET category_name = :category_name,  WHERE category_id = :category_id";
				$ims->execute($data);
				if($ims->row())	
				$success = '<div class="alert alert-success">Category Name Updated</div>';
			}
			$output = array('error'	=>	$error,	'success'=>	$success);
			echo json_encode($output);
	}
	if($_POST['btn_action'] == 'delete')
	{
		$status = 'active';
		if($_POST['status'] == 'active')		
			$status = 'inactive';	
		
		$ims->query = "UPDATE category 	SET category_status = :category_status 	WHERE category_id = :category_id ";		
		$ims->execute(
			array(
				':category_status'	=>	$status,
				':category_id'		=>	$_POST["category_id"]
			)
		);		
		if($ims->row())		
		echo json_encode('<div class="alert alert-info">Category status changed to ' . $status.'</div>');
	}		
}



if(isset($_POST['action'])&& $_POST['action'] == 'fetch')
{
		$output = array();
		$query = "SELECT * FROM category ";
		if(isset($_POST["search"]["value"]))
		{
			$query .= 'WHERE category_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$query .= 'OR category_status LIKE "%'.$_POST["search"]["value"].'%" ';
		}
		if(isset($_POST['order']))		
			$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';		
		else		
			$query .= 'ORDER BY category_id DESC ';		

		if($_POST['length'] != -1)		
			$query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];		

		$ims->query=$query;
		$ims->execute();
		$result = $ims->statement_result();
		$data = array();
		$filtered_rows = $ims->row();
		foreach($result as $row)
		{
			$status = '';
			if($row['category_status'] == 'active')			
				$status = '<span class="badge badge-success">Active</span>';			
			else			
				$status = '<span class="badge badge-danger">Inactive</span>';			
			$sub_array = array();
			$sub_array[] = $row['category_id'];
			$sub_array[] = $row['category_name'];
			$sub_array[] = $status;
			$button = '
	<div class="btn-group">
	  <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    Menu 
	  </button>
	  <ul class="dropdown-menu" >
	    <li><button type="button" name="update" id="'.$row["category_id"].'"  class="w-100 mb-1 text-center btn btn-info btn-sm update"><i class="fas fa-edit"></i> Update</button></li>
	    <li><button type="button" name="delete" id="'.$row["category_id"].'"  class="w-100 btn btn-primary btn-sm delete" data-status="'.$row["category_status"].'">Change Status</button></li>       
	  </ul>
	</div>';
	$sub_array[] = $button;
			$data[] = $sub_array;
		}

		$output = array(
			"draw"			=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  $filtered_rows,
			"recordsFiltered" 	=> $command->count_total('category'),
			"data"				=>	$data
		);

		echo json_encode($output);
	}
		
?>