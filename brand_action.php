<?php

//brand_action.php

include_once('config.php');
include_once(INC.'init.php');
$error = '';
$success = '';
if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
			$brand_name=$ims->clean_input($_POST["brand_name"]);		
			$data = array(':brand_name'=>$brand_name);
			$count =$ims-> CountTable('brand',array('brand_name'),array($brand_name));			
			if($count)			
				$error = '<div class="alert alert-danger">Brand Already Exists</div>';		
			else
			{	
				$newdata=array(':category_id'	=>$_POST["category_id"]);
				$data = array_merge($data, $newdata);		
				$ims->query = "	INSERT INTO brand (category_id, brand_name) VALUES (:category_id, :brand_name)	";
				$ims->execute($data);
				if($ims->row()>0)	
				$success = '<div class="alert alert-success">Brand Name Added</div>';
			}
			$output = array('error'	=>$error,'success'	=>	$success);
			echo json_encode($output);	
	}

	if($_POST['btn_action'] == 'fetch_single')
	{
		$ims->query = "	SELECT * FROM brand WHERE brand_id = :brand_id	";
		
		$ims->execute(
			array(
				':brand_id'	=>	$_POST["brand_id"]
			)
		);
		$result = $ims->statement_result();
		foreach($result as $row)
		{
			$output['category_id'] = $row['category_id'];
			$output['brand_name'] = $row['brand_name'];
		}
		echo json_encode($output);
	}
	if($_POST['btn_action'] == 'Edit')
	{
			$category_id = $ims->clean_input($_POST['category_id']);
			$brand_id = $ims->clean_input($_POST['brand_id']);
			$brand_name=$ims->clean_input($_POST["brand_name"]);		
			$data = array(				
				':brand_name'	=>	$_POST["brand_name"],
				':brand_id'		=>	$_POST["brand_id"]
			);
			$count =$ims-> CountTable('brand',array('brand_name,brand_id !'),array($brand_name,$brand_id));			
			if($count)	
			$ims->query = "	SELECT Count(*) FROM brand WHERE brand_name = :brand_name AND brand_id != :brand_id";
			$ims->execute($data);
			if($ims->get_array())		
				$error = '<div class="alert alert-danger">Brand Already Exists</div>';		
			else
			{	
				$newdata=array(':category_id'	=>$_POST["category_id"]);
				$data = array_merge($data, $newdata);		
				$ims->query = $ims->query = "UPDATE brand set category_id = :category_id, brand_name = :brand_name WHERE brand_id = :brand_id	";;
				$ims->execute($data);
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
		$ims->query = "UPDATE brand SET brand_status = :brand_status WHERE brand_id = :brand_id	";		
		$ims->execute(
			array(
				':brand_status'	=>	$status,
				':brand_id'		=>	$_POST["brand_id"]
			)
		);		
		if($ims->row())		
			echo json_encode('<div class="alert alert-info">Brand status changed to ' . $status.'</div>');
		
	}
}

?>
<?php

if(isset($_POST['action'])&& $_POST['action'] == 'fetch')
{
$query = '';

$output = array();
$query .= "SELECT * FROM brand INNER JOIN category ON category.category_id = brand.category_id ";

if(isset($_POST["search"]["value"]))
{
	$query .= 'WHERE brand.brand_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR category.category_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR brand.brand_status LIKE "%'.$_POST["search"]["value"].'%" ';
}

if(isset($_POST["order"]))
{
	$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
	$query .= 'ORDER BY brand.brand_id DESC ';
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
	"recordsTotal"		=>	$filtered_rows,
	"recordsFiltered"	=>	$command->count_total('brand'),
	"data"				=>	$data
);

echo json_encode($output);
}
?>