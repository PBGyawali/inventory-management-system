<?php 

include_once('../config.php');
include_once(INC.'init.php');

if($_POST) {
	$attr=$column=$value=array();
	$startDate = $_POST['startDate'];
	$table=isset($_POST['table'])?$_POST['table']:'sales';
	$endDate = $_POST['endDate'];
	$results='';
	$attr['compare']=array('>=','<=','=');
	if(!empty($startDate)&&!empty($endDate))
	$results = $ims->getAllArray('inventory_'.$table,array('inventory_'.$table.'_created_date','inventory_'.$table.'_created_date','inventory_'.$table.'_status' ),array($startDate,$endDate,'active'),'AND','','','','',$attr);
	
	
	$printtable = '<h2 class="text-center">'.ucwords($table).' Order Report Between '.(!empty($startDate)?$startDate:'(No start date given)') .' and '. (!empty($endDate)?$endDate:'(No end date given)').'</h2>
	<table border="1" cellspacing="0" cellpadding="0" style="width:100%;">
		<tr class="text-center">
			<th>Order Date</th>
			<th>Client Name</th>
			<th>Delivery Address</th>
			<th>Payment method</th>
			<th> Total before tax</th>
			<th> Tax</th>
		</tr>

		<tr>';
		$totalAmount = 0;
		if($results){
			foreach ($results as $result) {
				$printtable .= '<tr>
					<td><center>'.$result['inventory_'.$table.'_date'].'</center></td>
					<td><center>'.$result['inventory_'.$table.'_name'].'</center></td>
					<td><center>'.$result['inventory_'.$table.'_address'].'</center></td>
					<td><center>'.$result['payment_status'].'</center></td>
					<td><center>'.$result['inventory_'.$table.'_sub_total'].'</center></td>
					<td><center>'.$result['inventory_'.$table.'_tax'].'</center></td>
				</tr>';	
				$totalAmount += $result['inventory_'.$table.'_sub_total']+$result['inventory_'.$table.'_tax']-$result['inventory_'.$table.'_discount'];
			}
			$printtable .= '
			</tr>
	
			<tr>
				<td colspan="4"><center>Total amount together with tax</center></td>
				<td  colspan="2"><center>'.$totalAmount.'</center></td>
			</tr>

		
		
	</table>
	';	}
	else{
	

$printtable .= '
</tr>

<tr>
	<td colspan="5" class="text-center h1">No Data was found</td>
	
</tr>



</table>
';

	}

	

}

?>

<!DOCTYPE html>
		<html>
		<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta content='IE=edge,chrome=1' http-equiv='X-UA-Compatible' />
		<link rel="stylesheet" href="<?php echo CSS_URL?>bootstrap.min.css">
		<title><?=  ucwords($table);?> Order Report</title>
</head>
<body>
<?=  $printtable;?>
</body>