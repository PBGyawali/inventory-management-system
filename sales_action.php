<?php

//sales_action.php

include_once('config.php');
include_once(INC.'init.php');

function Productlist($count=null,$row=null){
	global $command; 	
	$product_details = '
	<span class="product_details" id="row'.$count.'">
	<input type="hidden" name="hidden_product_id[]" id="hidden_product_id'.$count.'" value="'.(($row)?$row["product_id"]:'').'" />
	<input type="hidden" name="hidden_quantity[]" id="hidden_quantity'.$count.'"  value="'.(($row)?$row["quantity"]:'').'"  />
		<div class="row" id="product_details_row'.$count.'">
			<div class="col-md-8">
				<select name="product_id[]" id="product_id'.$count.'" class="form-control selectpicker" 
					data-live-search="true" required>'.$command->fill_product_list(($row)?$row["product_id"]:'').'
				</select>
			</div>
			<div class="col-md-3 px-0">
				<input type="number" name="quantity[]" id="quantity'.$count.'" min="1" class="form-control" value="'.(($row)?$row["quantity"]:'').'"
				max="'.(($row)?$command->available_product_quantity($row["product_id"])+$row["quantity"]:'').'"  required />
			</div>
			<div class="col-md-1 pl-0">'.
			(($count== '')?'<button type="button" name="add_more" id="add_more" class="btn btn-success">+</button>'	
			:'<button type="button" name="remove" id="'.$count.'" class="btn btn-danger remove">-</button>').'			
				</div>
			</div>
		</div>
	</span>	';
	return 	$product_details;						
}
if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
		$query = "	INSERT INTO inventory_sales 
		(user_id, inventory_sales_date, inventory_sales_name, inventory_sales_address, payment_status, inventory_sales_created_date) 
		VALUES (:user_id, :sales_date, :sales_name, :sales_address, :payment_status, :created_date)	";
		$ims->query=$query;
		$ims->execute(array(
							':user_id'				=>	$_SESSION["user_id"],				
							':sales_date'			=>	$_POST['inventory_sales_date'],
							':sales_name'			=>	$_POST['inventory_sales_name'],
							':sales_address'		=>	$_POST['inventory_sales_address'],
							':payment_status'		=>	$_POST['payment_status'],				
							':created_date'			=>	date("Y-m-d")
						)
					);
		$inventory_sales_id = $ims->id();

		if($inventory_sales_id)
		{
			$total_amount = 0;
			for($count = 0; $count<count($_POST["product_id"]); $count++)
			{
				$product_details =$command-> fetch_product_details($_POST["product_id"][$count]);
				$ims->query= "INSERT INTO inventory_sales_product (inventory_sales_id, product_id, quantity, price, tax)
				 VALUES (:sales_id, :product_id, :quantity, :price, :tax)"; 				
				$ims->execute(
					array(
						':sales_id'				=>	$inventory_sales_id,
						':product_id'			=>	$_POST["product_id"][$count],
						':quantity'				=>	$_POST["quantity"][$count],
						':price'				=>	$product_details['price'],
						':tax'					=>	$product_details['tax']
					)
				);			
				
				$base_price = $product_details['price'] * $_POST["quantity"][$count];
				$tax = ($base_price/100)*$product_details['tax'];
				$total_amount = $total_amount + ($base_price + $tax);
			}
			$subupdate_query = "UPDATE inventory_sales SET inventory_sales_total = ? WHERE inventory_sales_id = ?	";
			$ims->query=$subupdate_query;
			$ims->execute(array($total_amount,$inventory_sales_id));
			$result = $ims->row();
			if($result)
				echo  json_encode('<div class="alert alert-success">Sales order Created...</div>');	
			else
				echo json_encode('<div class="alert alert-warning">There was some error in adding the sales order...</div>');
		}
	}

	if($_POST['btn_action'] == 'Edit')
	{	
		$delete_query = "DELETE FROM inventory_sales_product WHERE inventory_sales_id = ? ";
		$ims->query=$delete_query;
		$ims->execute(array($_POST["inventory_sales_id"]));
		$delete_result = $ims->row();		
		if($delete_result)
		{		
			
			for($count = 0; $count < count($_POST["hidden_product_id"]); $count++)
			{
				$previous_quantity=$_POST["hidden_quantity"][$count];
				$product_details = $command->fetch_product_details($_POST["hidden_product_id"][$count]);
				$real_quantity=$product_details['quantity']+$previous_quantity;
				$update_query = "UPDATE product SET product_quantity = :product_quantity WHERE product_id = :product_id	";
				$ims->query=$update_query;
				$ims->execute(array(
									':product_quantity'				=>	$real_quantity,
									':product_id'					=>	$_POST["hidden_product_id"]
								)
				);
				
			}
			$total_amount = 0;
			for($count = 0; $count < count($_POST["product_id"]); $count++)
			{
				$product_details = $command->fetch_product_details($_POST["product_id"][$count]);
				$ims->query= "	INSERT INTO inventory_sales_product 
				SET inventory_sales_id=:inventory_sales_id, product_id=:product_id, quantity=:quantity, price=:price, tax=:tax";				
				$ims->execute(
					array(
						':inventory_sales_id'	=>	$_POST["inventory_sales_id"],
						':product_id'			=>	$_POST["product_id"][$count],
						':quantity'				=>	$_POST["quantity"][$count],
						':price'				=>	$product_details['price'],
						':tax'					=>	$product_details['tax']
					)
				);				
				$base_price = $product_details['price'] * $_POST["quantity"][$count];
				$tax = ($base_price/100)*$product_details['tax'];
				$total_amount = $total_amount + ($base_price + $tax);
			}
			$update_query = "UPDATE inventory_sales SET inventory_sales_name = :inventory_sales_name, 
			inventory_sales_date = :inventory_sales_date,	inventory_sales_address = :inventory_sales_address, 
			inventory_sales_total = :inventory_sales_total, payment_status = :payment_status WHERE inventory_sales_id = :inventory_sales_id	";
			$ims->query=$update_query;
			$ims->execute(
				array(
					':inventory_sales_name'			=>	$_POST["inventory_sales_name"],
					':inventory_sales_date'			=>	$_POST["inventory_sales_date"],
					':inventory_sales_address'		=>	$_POST["inventory_sales_address"],
					':inventory_sales_total'		=>	$total_amount,
					':payment_status'				=>	$_POST["payment_status"],
					':inventory_sales_id'			=>	$_POST["inventory_sales_id"]
				)
			);			
			if($ims->row()>0)
				echo json_encode('<div class="alert alert-success">Sales Order Edited...</div>');
			else
				echo json_encode('<div class="alert alert-warning">No change was made...</div>');	
		}
	}
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "SELECT * FROM inventory_sales WHERE inventory_sales_id = :inventory_sales_id	";
		$ims->query=$query;
		$ims->execute(	array(':inventory_sales_id'	=>	$_POST["inventory_sales_id"])	);
		$result = $ims->statement_result();
		$output = array();
		foreach($result as $row)
		{
			$output['inventory_sales_name'] = $row['inventory_sales_name'];
			$output['inventory_sales_date'] = $row['inventory_sales_date'];
			$output['inventory_sales_address'] = $row['inventory_sales_address'];
			$output['payment_status'] = $row['payment_status'];
		}
		$sub_query = "SELECT * FROM inventory_sales_product WHERE inventory_sales_id = ?	";
		$ims->query=$sub_query;
		$ims->execute(array($_POST["inventory_sales_id"]));
		$sub_result = $ims->statement_result();
		$product_details = '';
		$count = '';
		if ($sub_result)
			{		
				foreach($sub_result as $sub_row)
					{
						$product_details.=Productlist($count,$sub_row);						
						if ($count=='')
							$count=1;
						else
							$count = $count++;
					}
			}
		else{
				$product_details = Productlist();		
			}
		$output['product_details'] = $product_details;
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'delete')
	{
		$status = 'active';
		$salesstatus='Unpaid';
		if($_POST['status'] == 'active')
		{
			$status = 'inactive';
			$salesstatus='Paid';
		}
		$query = "UPDATE inventory_sales SET inventory_sales_status = :inventory_sales_status WHERE inventory_sales_id = :inventory_sales_id ";
		$ims->query=$query;
		$ims->execute(
			array(
					':inventory_sales_status'	=>	$status,
					':inventory_sales_id'		=>	$_POST["inventory_sales_id"]
				)
		);		
		if($ims->row())
		{
			echo json_encode('<div class="alert alert-info">Sales order status changed to ' .$salesstatus.'</div>');			
		}
	}
}


if(isset($_POST['action'])&& $_POST['action'] == 'fetch')
{

$output = array();

$query = "	SELECT * FROM inventory_sales WHERE ";

if(isset($_POST["search"]["value"]))
{
	$query .= '(inventory_sales_id LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR inventory_sales_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR inventory_sales_total LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR inventory_sales_status LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR inventory_sales_date LIKE "%'.$_POST["search"]["value"].'%") ';
}

if(isset($_POST["order"]))
	$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
else
	$query .= 'ORDER BY inventory_sales_id DESC ';


if($_POST["length"] != -1)
	$query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];

$ims->query=$query;
$ims->execute();
$result = $ims->statement_result();
$data = array();
$filtered_rows = $ims->row();
foreach($result as $row)
{
	if($row['payment_status'] == 'cash')	
		$payment_status = '<span class="badge badge-primary">Cash</span>';	
	else	
		$payment_status = '<span class="badge badge-warning">Credit</span>';	

	if($row['inventory_sales_status'] == 'active')	
		$status = '<span class="badge badge-danger">Unpaid</span>';	
	else	
		$status = '<span class="badge badge-success">Paid</span>';
	
	$sub_array = array();
	$sub_array[] = $row['inventory_sales_id'];
	$sub_array[] = ucwords($row['inventory_sales_name']);
	$sub_array[] = $row['inventory_sales_total'];
	$sub_array[] = $payment_status;
	$sub_array[] = $status;
	$sub_array[] = $row['inventory_sales_date'];
	if($ims->is_admin())	
		$sub_array[] = ucwords($command->get_user_name($row['user_id']));
		$button = '
		<div class="btn-group">
		  <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			Menu 
		  </button>
		  <ul class="dropdown-menu dropdown-menu-right" >
			  <li><a href="view_sales.php?pdf=1&sales_id='.$row["inventory_sales_id"].'" id="'.$row["inventory_sales_id"].'" target="_blank"  class="w-100 mb-1 text-center btn btn-warning btn-sm view"><i class="fas fa-eye"></i> View PDF</a></li>
			<li><button type="button" name="update" id="'.$row["inventory_sales_id"].'" class="w-100 mb-1 text-center btn btn-info btn-sm update"><i class="fas fa-edit"></i> Update</button></li>
			<li><button type="button" name="delete" id="'.$row["inventory_sales_id"].'"  class="w-100 btn btn-primary btn-sm delete" data-status="'.$row["inventory_sales_status"].'">Change Status</button></li>       
		  </ul>
		</div>';
		$sub_array[] = $button;

	
	$data[] = $sub_array;
}

$output = array(
	"draw"    			=> 	intval($_POST["draw"]),
	"recordsTotal"  	=>  $filtered_rows,
	"recordsFiltered" 	=> $command->count_total('inventory_sales'),
	"data"    			=> 	$data
);	

echo json_encode($output);
}


?>