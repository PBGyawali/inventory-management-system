<?php

//product_action.php

include_once('config.php');
include_once(INC.'init.php');


if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
		$total_tax =0;
		for($count = 0; $count<count($_POST["tax_id"]); $count++){	
			$tax = $_POST["tax_id"][$count]	;
			$total_tax += $tax;
		}
		$ims->query= "INSERT INTO product 	(category_id, brand_id, product_name, product_description, product_quantity, 
		product_unit, product_base_price, product_tax, product_enter_by, product_date) 
		VALUES (:category_id, :brand_id, :name, :description, :quantity, :unit, :base_price, :tax, :enter_by, :date)";		
		$ims->execute(
			array(
				':category_id'	=>	$_POST['category_id'],
				':brand_id'		=>	$_POST['brand_id'],
				':name'			=>	ucwords($ims->clean_input($_POST['product_name'])),
				':description'	=>	$ims->clean_input($_POST['product_description']),
				':quantity'		=>	$ims->clean_input($_POST['product_quantity']),
				':unit'			=>	$ims->clean_input($_POST['product_unit']),
				':base_price'	=>	$ims->clean_input($_POST['product_base_price']),
				':tax'			=>	$total_tax,
				':enter_by'		=>	$_SESSION["user_id"], 				
				':date'			=>	date("Y-m-d")
			)
		);		
		if($ims->row()>0){
			echo json_encode('<div class=" alert alert-success">Product Details Added</div>');
		}
	}
	if($_POST['btn_action'] == 'product_details')
	{
		$query = "SELECT * FROM product 
		INNER JOIN category ON category.category_id = product.category_id 
		INNER JOIN brand ON brand.brand_id = product.brand_id 
		INNER JOIN user ON user.user_id = product.product_enter_by 
		WHERE product.product_id = ?	";
		$ims->query=$query;
		$ims->execute(array($_POST["product_id"]));
		$result = $ims->statement_result();
		$output = '
		<div class="table-responsive">
			<table class="table table-boredered">
		';
		foreach($result as $row)
		{
			$status = '';
			if($row['product_status'] == 'active')
				$status = '<span class="badge badge-success">Active</span>';
			else			
				$status = '<span class="badge badge-danger">Inactive</span>';
			
			$output .= '
			<tr>
				<td>Product Name</td>
				<td>'.htmlspecialchars(ucwords($row["product_name"])).'</td>
			</tr>
			<tr>
				<td>Product Description</td>
				<td>'.htmlspecialchars($row["product_description"]).'</td>
			</tr>
			<tr>
				<td>Category</td>
				<td>'.htmlspecialchars(ucwords($row["category_name"])).'</td>
			</tr>
			<tr>
				<td>Brand</td>
				<td>'.htmlspecialchars(ucwords($row["brand_name"])).'</td>
			</tr>
			<tr>
				<td>Available Quantity</td>
				<td>'.$command->available_product_quantity($_POST["product_id"]).' '.$row["product_unit"].'</td>
			</tr>
			<tr>
				<td>Base Price</td>
				<td>'.$row["product_base_price"].'</td>
			</tr>
			<tr>
				<td>Tax (%)</td>
				<td>'.$row["product_tax"].'</td>
			</tr>
			<tr>
				<td>Enter By</td>
				<td>'.htmlspecialchars(ucwords($row["user_name"])).'</td>
			</tr>
			<tr>
				<td>Status</td>
				<td>'.$status.'</td>
			</tr>
			';
		}
		$output .= '
			</table>
		</div>
		';
		echo json_encode($output);
	}
	if($_POST['btn_action'] == 'fetch_single')
	{
		$ims->query = "SELECT * FROM product WHERE product_id = :product_id	";
		$ims->execute(array(':product_id'=>	$_POST["product_id"]));
		$result = $ims->statement_result();
		foreach($result as $row)
		{
			$output['category_id'] = $row['category_id'];
			$output['brand_id'] = $row['brand_id'];
			$output["brand_select_box"] = $command->fill_brand_list($row["category_id"]);
			$output['product_name'] = ucwords($row['product_name']);
			$output['product_description'] = $row['product_description'];
			$output['product_quantity'] = $row['opening_stock'];
			$output['product_unit'] = $row['product_unit'];
			$output['product_base_price'] = $row['product_base_price'];
			$output['product_tax'] = $row['product_tax'];
		}
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
	{
		$ims->query= "UPDATE product set category_id = :category_id, brand_id = :brand_id,	product_name = :product_name,
		product_description = :product_description, opening_stock = :opening_stock,	product_unit = :product_unit, 
		product_base_price = :product_base_price, product_tax = :product_tax WHERE product_id = :product_id		";		
		$ims->execute(
			array(
				':category_id'			=>	$_POST['category_id'],
				':brand_id'				=>	$_POST['brand_id'],
				':product_name'			=>	$ims->clean_input($_POST['product_name']),
				':product_description'	=>	$ims->clean_input($_POST['product_description']),
				':opening_stock'		=>	$ims->clean_input($_POST['product_quantity']),
				':product_unit'			=>	$ims->clean_input($_POST['product_unit']),
				':product_base_price'	=>	$ims->clean_input($_POST['product_base_price']),
				':product_tax'			=>	$ims->clean_input($_POST['product_tax']),
				':product_id'			=>	$_POST['product_id']
			)
		);	
		if($ims->row()>0)		
			$message='<div class="alert alert-success">Product Details Edited</div>';		
		else
			$message='<div class="alert alert-warning">No Product Details change was made</div>';
		echo json_encode($message);
	}
	if($_POST['btn_action'] == 'delete')
	{
		$status = 'active';
		if($_POST['status'] == 'active')		
			$status = 'inactive';
		
		$query = "UPDATE product SET product_status = :product_status 	WHERE product_id = :product_id		";
		$ims->query=$query;
		$ims->execute(array(':product_status'=>	$status,':product_id'=>	$_POST["product_id"])
		);		
		if($ims->row()>0)		
			echo json_encode('<div class="alert alert-info">Product status changed to '.$status.'</div>');		
	}
}


?>

<?php

if(isset($_POST['action'])&& $_POST['action'] == 'fetch')
{		
		$output = array();
		$query = "	SELECT * FROM product 
		LEFT JOIN brand ON brand.brand_id = product.brand_id
		LEFT JOIN category ON category.category_id = product.category_id 
		LEFT JOIN user ON user.user_id = product.product_enter_by ";

		if(isset($_POST["search"]["value"]))
		{
			$query .= 'WHERE brand.brand_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$query .= 'OR category.category_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$query .= 'OR product.product_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$query .= 'OR product.product_quantity LIKE "%'.$_POST["search"]["value"].'%" ';
			$query .= 'OR user.user_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$query .= 'OR product.product_id LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST['order']))
		{
			$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$query .= 'ORDER BY product_id DESC ';
		}

		if($_POST['length'] != -1)
		{
			$query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}
		$ims->query= $query;
		$ims->execute();
		$result = $ims->statement_result();
		$data = array();
		$filtered_rows = $ims->row();
		foreach($result as $row)
		{
			$status = '';
			if($row['product_status'] == 'active')
			{
				$status = '<span class="badge badge-success">Active</span>';
			}
			else
			{
				$status = '<span class="badge badge-danger">Inactive</span>';
			}
			$sub_array = array();
			$sub_array[] = $row['product_id'];
			$sub_array[] = ucwords($row['category_name']);
			$sub_array[] = ucwords($row['brand_name']);
			$sub_array[] = ucwords($row['product_name']);
			$sub_array[] = $command->available_product_quantity($row["product_id"]) . ' ' . $row["product_unit"];
			$sub_array[] = $status;
			$sub_array[] = ucwords($row['user_name']);
			$button = '	<div class="btn-group">
	  <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    Menu 
	  </button>
	  <ul class="dropdown-menu dropdown-menu-right" >
	  	<li><button type="button" name="view" id="'.$row["product_id"].'"  class="w-100 mb-1 text-center btn btn-warning btn-sm view"><i class="fas fa-eye"></i> View</button></li>
	    <li><button type="button" name="update" id="'.$row["product_id"].'" class="w-100 mb-1 text-center btn btn-info btn-sm update"><i class="fas fa-edit"></i> Update</button></li>
	    <li><button type="button" name="delete" id="'.$row["product_id"].'" class="w-100 btn btn-primary btn-sm delete" data-status="'.$row["product_status"].'">Change Status</button></li>       
	  </ul>
	</div>';
	$sub_array[] = $button;
			$data[] = $sub_array;
		}

		$output = array(
			"draw"    			=> 	intval($_POST["draw"]),
			"recordsTotal"  	=>  $filtered_rows,
			"recordsFiltered" 	=> 	$command-> count_total('product'),
			"data"    			=> 	$data
		);

		echo json_encode($output);
}

?>