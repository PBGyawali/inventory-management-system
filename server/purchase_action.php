<?php

//purchase_action.php

include_once('../config.php');
include_once(INC.'init.php');
$filtered_rows = 0;
$total_rows=0;
$table='inventory_purchase';
function Productlist($count=null,$row=null){
	global $command; 	
	$product_details = '
	<span class="product_details" id="row'.$count.'">
	<input type="hidden" name="hidden_product_id[]" id="hidden_product_id'.$count.'" value="'.(($row)?$row["product_id"]:'').'" />
	<input type="hidden" name="hidden_quantity[]"  id="hidden_quantity'.$count.'" value="'.(($row)?$row["quantity"]:'').'"  />
		<div class="row" id="product_details_row'.$count.'">
			<div class="col-md-8">
				<select name="product_id[]" id="product_id'.$count.'" class="form-control " data-live-search="true" required>'.
				$command->fill_product_list(($row)?$row["product_id"]:'').'
				</select>				
			</div>
			<div class="col-md-3 px-0">
				<input type="text" name="quantity[]" class="form-control" value="'.(($row)?$row["quantity"]:'').'" required />				
			</div>
			<div class=0"col-md-1 pl-0">'.
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
		$column=array('user_id', 'inventory_purchase_date','inventory_purchase_name', 
		'inventory_purchase_address',' payment_status', 'inventory_purchase_created_date');
		$value=array(
				$_SESSION["user_id"],$_POST['inventory_purchase_date'],
				$_POST['inventory_purchase_name'],$_POST['inventory_purchase_address'],
				$_POST['payment_status'],date("Y-m-d")
		);		
		$ims->insert($table,$column,$value);
		$inventory_purchase_id = $ims->id();

		if($inventory_purchase_id)
		{
			$total_amount = 0;
			for($count = 0; $count<count($_POST["product_id"]); $count++)
			{
				$product_details =$command-> fetch_product_details($_POST["product_id"][$count]);
				$column= array('inventory_purchase_id', 'product_id', 'quantity', 'price',' tax') ;
				$value=	array($inventory_purchase_id,$_POST["product_id"][$count],
							$_POST["quantity"][$count],	$product_details['price'],
							$product_details['tax']
					);
				$ims->insert('inventory_purchase_product',$column,$value);				
				$base_price = $product_details['price'] * $_POST["quantity"][$count];
				$tax = ($base_price/100)*$product_details['tax'];
				$total_amount = $total_amount + ($base_price + $tax);
			}
			$column = array(" inventory_purchase_sub_total ");
			$value=array($total_amount);
			$ims->UpdateDataColumn($table,$column,$value,'inventory_purchase_id',$inventory_purchase_id);
			$result = $ims->row();
			if($result)
				echo json_encode('<div class="alert alert-success">Purchase Order Created...<div>');
			else
				echo json_encode('<div class="alert alert-warning">There was some error in adding the purchase order data...</div>');
		}
	}

	if($_POST['btn_action'] == 'fetch_single')
	{		
		$result = $ims->getAllArray($table,'inventory_purchase_id',$_POST["inventory_purchase_id"]);
		$output = array();
		foreach($result as $row)
		{
			$output['inventory_purchase_name'] = $row['inventory_purchase_name'];
			$output['inventory_purchase_date'] = $row['inventory_purchase_date'];
			$output['inventory_purchase_address'] = $row['inventory_purchase_address'];
			$output['payment_status'] = $row['payment_status'];
		}
		$sub_result =$ims->getAllArray('inventory_purchase_product','inventory_purchase_id',$_POST["inventory_purchase_id"]);
		$product_details = '';
		$count = '';
		if ($sub_result){		
				foreach($sub_result as $sub_row){
						$product_details.=Productlist($count,$sub_row);						
						if ($count=='')
							$count=1;
						else
							$count = $count++;
				}
			}
		else
				$product_details = Productlist();
		$output['product_details'] = $product_details;
		echo json_encode($output);
	}
	

	if($_POST['btn_action'] == 'Edit')
	{		
		$ims->Delete('inventory_purchase_product','inventory_purchase_id',$_POST["inventory_purchase_id"]);		
		$delete_result = $ims->row();
		if($delete_result)
		{	
			for($count = 0; $count < count($_POST["hidden_product_id"]); $count++)
			{
				$previous_quantity=$_POST["hidden_quantity"][$count];
				$product_details = $command->fetch_product_details($_POST["hidden_product_id"][$count]);
				$real_quantity= $product_details['quantity']-$previous_quantity;				
				$ims->UpdateDataColumn('product','product_quantity',$real_quantity,'product_id',$_POST["hidden_product_id"]);
			}
			$total_amount = 0;
			for($count = 0; $count < count($_POST["product_id"]); $count++)
			{
				$product_details = $command->fetch_product_details($_POST["product_id"][$count]);
				$column= array('inventory_purchase_id', 'product_id', 'quantity', 'price',' tax') ;
				$value=	array($_POST["inventory_purchase_id"],$_POST["product_id"][$count],
							$_POST["quantity"][$count],	$product_details['price'],
							$product_details['tax']);
				$ims->insert('inventory_purchase_product',$column,$value);
				$base_price = $product_details['price'] * $_POST["quantity"][$count];
				$tax = ($base_price/100)*$product_details['tax'];
				$total_amount = $total_amount + ($base_price + $tax);
			}
			$column=array("inventory_purchase_name","inventory_purchase_date",
			"inventory_purchase_address","inventory_purchase_sub_total","payment_status");
			$value=array(	$_POST["inventory_purchase_name"],
							$_POST["inventory_purchase_date"],
							$_POST["inventory_purchase_address"],
							$total_amount,$_POST["payment_status"],
			);
			$ims->UpdateDataColumn($table,$column,$value,'inventory_purchase_id',$_POST["inventory_purchase_id"]);			
			if($ims->row()>0)			
				echo json_encode('<div class="alert alert-success">Purchase order Updated...</div>');
			else
			echo json_encode('<div class="alert alert-warning">No change was made...</div>');			
		}
	}

	if($_POST['btn_action'] == 'delete')
	{
		$status = 'active';
		$purchasestatus='Unpaid';
		if($_POST['status'] == 'active')
		{
			$status = 'inactive';
			$purchasestatus='Paid';
		}		
		$ims->UpdateDataColumn($table,'inventory_purchase_status',$status,'inventory_purchase_id',$_POST["inventory_purchase_id"]);
		if($ims->row()>0)
		{
			echo json_encode('<div class="alert alert-info">Purchase order status changed to '.$purchasestatus.'</div>');
		}
	}
}
if(isset($_POST['action'])&& $_POST['action'] == 'fetch')
{

	$output = $column=$combine=$attr=$value=$output = array();
	if(isset($_POST["search"]["value"]) )
	{
		$column = array('inventory_purchase_id', 'inventory_purchase_name', 'inventory_purchase_sub_total',
		'inventory_purchase_status','inventory_purchase_date');
		$combine='OR';
		$attr['compare']=' LIKE  ';
		$value=array('%'.$_POST["search"]["value"]."%",
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
		$orderby='inventory_purchase_id';
	}
	
	if($_POST['length'] != -1)
		$limit=$_POST['start'] . ', ' . $_POST['length'];
		
	
	$result = $ims->getAllArray($table,$column,$value,$combine,$limit,$orderby,$order,'',$attr);
	$data = array();
	$filtered_rows = $ims->row();
	foreach($result as $row)
	{
		if($row['payment_status'] == 'cash')	
			$payment_status = '<span class="badge badge-primary">Cash</span>';	
		else	
			$payment_status = '<span class="badge badge-warning">Credit</span>';	

		if($row['inventory_purchase_status'] == 'active')	
			$status = '<span class="badge badge-danger">Unpaid</span>';	
		else	
			$status = '<span class="badge badge-success">Paid</span>';
		
		$sub_array = array();
		$sub_array[] = $row['inventory_purchase_id'];
		$sub_array[] = ucwords($row['inventory_purchase_name']);
		$sub_array[] = $row['inventory_purchase_sub_total'];
		$sub_array[] = $payment_status;
		$sub_array[] = $status;
		$sub_array[] = $row['inventory_purchase_date'];
		if($ims->is_admin())	
			$sub_array[] = ucwords($command->get_user_name($row['user_id']));
			$button = '
			<div class="btn-group">
			<button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				Menu 
			</button>
			<ul class="dropdown-menu dropdown-menu-right" >
				<li><a href="view_purchase.php?pdf=1&purchase_id='.$row["inventory_purchase_id"].'" id="'.$row["inventory_purchase_id"].'" target="_blank"  class="w-100 mb-1 text-center btn btn-warning btn-sm view"><i class="fas fa-eye"></i> View PDF</a></li>
				<li><button type="button" name="update" id="'.$row["inventory_purchase_id"].'" class="w-100 mb-1 text-center btn btn-info btn-sm update"><i class="fas fa-edit"></i> Update</button></li>
				<li><button type="button" name="delete" id="'.$row["inventory_purchase_id"].'"  class="w-100 btn btn-primary btn-sm delete" data-status="'.$row["inventory_purchase_status"].'">Change Status</button></li>       
			</ul>
			</div>';
			$sub_array[] = $button;
		$data[] = $sub_array;
	}

$output = array(
	"draw"    			=> 	intval($_POST["draw"]),
	"recordsTotal"  	=>  $command->count_total($table),
	"recordsFiltered" 	=> $filtered_rows,
	"data"    			=> 	$data
);	

echo json_encode($output);
}


?>