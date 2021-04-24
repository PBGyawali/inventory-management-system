<?php

//product_action.php

include_once('../config.php');
include_once(INC.'init.php');

$table='product';
if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
		$total_tax =0;
		for($count = 0; $count<count($_POST["tax_id"]); $count++){	
			$tax = $_POST["tax_id"][$count]	;
			$total_tax += $tax;
		}
		$column=array('category_id','brand_id','product_name','product_description',
			'product_quantity',	'product_unit'	,'product_base_price','product_tax',
			'product_enter_by', 'product_date'		
		);
		$value=array($_POST['category_id'],	$_POST['brand_id'],
			ucwords($ims->clean_input($_POST['product_name'])),
			$ims->clean_input($_POST['product_description']),
			$ims->clean_input($_POST['product_quantity']),
			$ims->clean_input($_POST['product_unit']),
			$ims->clean_input($_POST['product_base_price']),
			$total_tax,	$_SESSION["user_id"], 	date("Y-m-d")
		);
		$ims->insert($table,$column,$value);
		if($ims->row()>0){
			echo json_encode('<div class=" alert alert-success">Product Details Added</div>');
		}
	}
	if($_POST['btn_action'] == 'product_details')
	{
		$join=array('LEFT JOIN'=>array(
								'category'=> 'category.category_id = product.category_id',
								'brand'=>'brand.brand_id = product.brand_id',
								'user'=>'user.user_id = product.product_enter_by'
		));
		$result = $ims->getAllArray('product','product.product_id',$_POST["product_id"],'','','','',$join);		
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
		$result = $ims->getAllArray('product','product_id',$_POST["product_id"]);		
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
		$column=array('category_id','brand_id','product_name','product_description',
			'opening_stock','product_unit',	'product_base_price','product_tax');
		$value=array($_POST['category_id'],	$_POST['brand_id'],	$ims->clean_input($_POST['product_name']),
				$ims->clean_input($_POST['product_description']),$ims->clean_input($_POST['product_quantity']),
				$ims->clean_input($_POST['product_unit']),	$ims->clean_input($_POST['product_base_price']),
				$ims->clean_input($_POST['product_tax'])		);
		$ims->UpdateDataColumn($table,$column,$value,'product_id',$_POST['product_id']);
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
		$ims->UpdateDataColumn('product','product_status',$status,'product_id',$_POST['product_id']);		
		if($ims->row()>0)		
			echo json_encode('<div class="alert alert-info">Product status changed to '.$status.'</div>');		
	}
}


if(isset($_POST['action'])&& $_POST['action'] == 'fetch')
{		
	$output = $column=$combine=$attr=$value=$output = array();
	if(isset($_POST["search"]["value"]) )
	{

		$join = array("LEFT JOIN"=> array("brand "=>" brand.brand_id = product.brand_id",
										"category"=>" category.category_id = product.category_id",
										"user"=> " user.user_id = product.product_enter_by ")
										);
		$column = array('brand.brand_name', 'category.category_name', 'product.product_name',
		'product.product_quantity','user.user_name','product.product_id');
		$combine='OR';
		$attr['compare']=' LIKE  ';
		$value=array('%'.$_POST["search"]["value"]."%",
					'%'.$_POST["search"]["value"]."%",
					'%'.$_POST["search"]["value"]."%",
					'%'.$_POST["search"]["value"]."%",
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
		$orderby='product_id';
	}
	
	if($_POST['length'] != -1)
		$limit=$_POST['start'] . ', ' . $_POST['length'];		
	
	$result = $ims->getAllArray($table,$column,$value,$combine,$limit,$orderby,$order,$join,$attr);
	$data = array();
	$filtered_rows = $ims->row();
		foreach($result as $row)
		{
			$status = '';
			if($row['product_status'] == 'active')			
				$status = '<span class="badge badge-success">Active</span>';			
			else			
				$status = '<span class="badge badge-danger">Inactive</span>';			
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
			"recordsTotal"  	=>  $command-> count_total($table),
			"recordsFiltered" 	=> 	$filtered_rows,
			"data"    			=> 	$data
		);

		echo json_encode($output);
}

?>