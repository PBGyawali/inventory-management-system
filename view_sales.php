<?php

//view_sales.php
if(empty($_GET['sales_id'])||empty($_GET["pdf"])){
	echo '<h1 style="position:absolute; width: 100%;top: 50%;
    left: 50%; transform: translate(-50%, -50%);text-align:center;">Not a valid PDF request</h1>';
}
elseif(isset($_GET["pdf"]) && isset($_GET['sales_id']))
{
	
	include_once('config.php');
	require_once (CLASS_DIR.'pdf.php');//this must alway be above the normal connection to the database
	include_once(INC.'init.php');
	
	if(!$ims->is_login())
	{
		header('location:'.$ims->login);
	}
	$output = '';
	$ims->query = "	SELECT * FROM company_table	";
	$ims->execute();
	$data=$ims->get_array();
	$ims->query ="SELECT * FROM inventory_sales WHERE inventory_sales_id = ? LIMIT 1	";
	$ims->execute(array( $_GET["sales_id"]));
	$row = $ims->get_array();
	$output .='
	<table width="100%" border="1"  cellpadding="5" cellspacing="0">
		<tr>
			<td colspan="2" align="center" style="font-size:18px">
				<b>Invoice</b>
			</td>
		</tr>
		<tr  >
			<td align="center" style="border-bottom:none;" colspan="2">
				<b style="font-size:32px;color:blue;">'.ucwords($data["company_name"]).'</b>
				<br />
				<span style="font-size:16px;color:blue;">'.$data["company_address"].'</span>
				<br />
				<span style="font-size:16px;color:blue;"><b>Contact No. - </b>'.$data["company_contact_no"].'</span>
				<br />
				<span style="font-size:16px;color:blue;"><b>Email - </b>'.$data["company_email"].'</span>
				<br /><br />
			</td>
		</tr>	
		<tr>
			<td colspan="2" style="border-top:none;border-bottom:none;">
				<table width="100%" cellpadding="5">
					<tr>
						<td width="65%">
							To,<br />
							<b>RECEIVER (BILL TO)</b><br />
							Name : '.$row["inventory_sales_name"].'<br />	
							Billing Address : '.$row["inventory_sales_address"].'<br />
						</td>
						<td width="35%">
							Reverse Charge<br />
							Invoice No. : '.$row["inventory_sales_id"].'<br />
							Invoice Date : '.$row["inventory_sales_date"].'<br />
						</td>
					</tr>
				</table>
				<br />
				<table width="100%" border="1" cellpadding="5" cellspacing="0">
					<tr>
						<th rowspan="2">Sr No.</th>
						<th rowspan="2">Product</th>
						<th rowspan="2">Quantity</th>
						<th rowspan="2">Price</th>
						<th rowspan="2">Actual Amt.</th>
						<th colspan="2">Tax (%)</th>
						<th rowspan="2">Total'.' ('.$data["company_currency"].')'.'</th>
					</tr>
					<tr>
						<th>Rate</th>
						<th>Amt.</th>
					</tr>
		';

		
		$ims->query ="SELECT * FROM inventory_sales_product WHERE inventory_sales_id = :inventory_sales_id";
		$ims->execute(	array(':inventory_sales_id' =>  $_GET["sales_id"]));
		$product_result = $ims->statement_result();
		$count = 0;
		$total = 0;
		$total_actual_amount = 0;
		$total_tax_amount = 0;
		foreach($product_result as $sub_row)
		{
			$count = $count + 1;
			$product_data = $command->fetch_product_details($sub_row['product_id']);
			$actual_amount = $sub_row["quantity"] * $sub_row["price"];
			$tax_amount = ($actual_amount * $sub_row["tax"])/100;
			$total_product_amount = $actual_amount + $tax_amount;
			$total_actual_amount = $total_actual_amount + $actual_amount;
			$total_tax_amount = $total_tax_amount + $tax_amount;
			$total = $total + $total_product_amount;
			$output .= '
					<tr>
						<td>'.$count.'</td>
						<td>'.$product_data['product_name'].'</td>
						<td>'.$sub_row["quantity"].'</td>
						<td align="right">'.$data["currency_symbol"].' '.$sub_row["price"].'</td>
						<td align="right">'.$data["currency_symbol"].' '.number_format($actual_amount, 2).'</td>
						<td>'.$sub_row["tax"].' '.'%</td>
						<td align="right">'.$data["currency_symbol"].' '.number_format($tax_amount, 2).'</td>
						<td align="right">'.$data["currency_symbol"].' '.number_format($total_product_amount, 2).'</td>
					</tr>
					';
		}
		$output .= '
					<tr>
						<td align="right" colspan="4"><b>Total</b></td>
						<td align="right"><b>'.$data["currency_symbol"].' '.number_format($total_actual_amount, 2).'</b></td>
						<td>&nbsp;</td>
						<td align="right"><b>'.$data["currency_symbol"].' '.number_format($total_tax_amount, 2).'</b></td>
						<td align="right"><b>'.$data["currency_symbol"].' '.number_format($total, 2).'</b></td>
					</tr>
					';
		$output .= '
				</table>
						<br />
						<br />
						<br />
						<br />
						<br />
						<br />
						<br />
						<br />
						<br />
						
				<table width="100%" border="0" cellpadding="0">			
					<tr>
						<td width="50%">
						<b style="color:blue; transform: rotate(-11deg);	
								font-size: 2rem;
								border: 3rem solid blue;
								padding: 0.25rem 1rem;
								text-transform: uppercase;
								mix-blend-mode: multiply;
						">'.ucwords($data["company_name"]).'</b>
							----------------------------------------<br />
							Company Stamp/Signature				
						</td>
						<td width="20%">						
						</td>
						<td width="30%" align="center">
							----------------------------------------<br />
							Receiver'.'&apos;'.'s Signature
						</td>
					</tr>			
				</table>
					
				<br />
				<br />
				<br />
			</td>
		</tr>
		<tr >
			<td colspan="2" align="center" style="font-size:18px;border-top:none">
				<b>We are glad to transact with you</b>
			</td>
		</tr>
	</table>
		';
	
	$pdf = new Pdf();
	$file_name = 'Sales-'.$row["inventory_sales_id"].'.pdf';
	$pdf->loadHtml($output);
	$pdf->set_option('isHtml5ParserEnabled', true);
	$pdf->render();
	$pdf->stream($file_name, array("Attachment" => false));
}

?>