<?php

//unit_action.php

include_once('config.php');
include_once(INC.'init.php');
$error = '';
$success = '';
if(isset($_POST['btn_action']))
{

	if($_POST["btn_action"] == 'Add')
	{	
		$unit_name=	$ims->clean_input($_POST["unit_name"]);		
		$data = array(':unit_name'=>$unit_name);				
		$count =$ims-> CountTable('unit','unit_name',$unit_name);			
		if($count)				
			$error = '<div class="alert alert-danger">Unit Already Exists</div>';		
		else
		{			
			$ims->query = "	INSERT INTO unit (unit_name) VALUES (:unit_name)	";
			$ims->execute($data);
			if($ims->row()>0)	
			$success = '<div class="alert alert-success">Unit Added</div>';
		}
		$output = array('error'	=>$error,'success'	=>	$success);
		echo json_encode($output);
	}	
	
	if($_POST['btn_action'] == 'fetch_single')
	{
		$ims->query = "SELECT unit_name FROM unit WHERE unit_id = :unit_id";		
		$ims->execute(array(':unit_id'=>$_POST["unit_id"]));
		$row =$ims->get_array();		
		echo json_encode($row['unit_name']);
	}

	if($_POST['btn_action'] == 'Edit')
	{		
		$unit_id = $ims->clean_input($_POST['unit_id']);
		$unit_name=$ims->clean_input($_POST["unit_name"]);		
		$data = array(
			':unit_name'		=>	$unit_name,
			':unit_id'			=>	$unit_id
		);
		$count =$ims-> CountTable('unit',array('unit_name,unit_id !'),array($unit_name,$unit_id));			
		if($count)				
			$error = '<div class="alert alert-danger">Unit Already Exists</div>';		
		else
		{			
			$ims->query = "UPDATE unit SET unit_name = :unit_name,  WHERE unit_id = :unit_id";
			$ims->execute($data);
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
		$ims->query = "UPDATE unit 	SET unit_status = :unit_status 	WHERE unit_id = :unit_id ";		
		$ims->execute(
			array(
				':unit_status'	=>	$status,
				':unit_id'		=>	$_POST["unit_id"]
			)
		);		
		if($ims->row())		
			echo json_encode('<div class="alert alert-info">Unit status changed to ' . $status.'</div>');		
	}
}

?>


<?php

if(isset($_POST['action'])&& $_POST['action'] == 'fetch')
{
$query = '';

$output = array();

$query .= "SELECT * FROM unit ";

if(isset($_POST["search"]["value"]))
{
	$query .= 'WHERE unit_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR unit_status LIKE "%'.$_POST["search"]["value"].'%" ';
}

if(isset($_POST['order']))
{
	$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
	$query .= 'ORDER BY unit_id DESC ';
}

if($_POST['length'] != -1)
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