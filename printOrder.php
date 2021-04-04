<?php
//$orderId = $_POST['orderId'];
//$table=$_POST['table'];
include_once('config.php');
include_once(INC.'init.php');
$orderId = 1;
$table='sales';

$ims->query = "	SELECT * FROM company_table	";
	$ims->execute();
	$data=$ims->get_array();
	$ims->query ="SELECT * FROM inventory_".$table." WHERE inventory_".$table."_id = ? LIMIT 1	";
	$ims->execute(array($orderId));
	$row = $ims->get_array();


$orderDate = $row['inventory_'.$table.'_date'];
$clientName = $row['inventory_'.$table.'_name'];
$clientContact =$row['inventory_'.$table.'_address'];
$subTotal = $row['inventory_'.$table.'_sub_total'];
$vat =$row['inventory_'.$table.'_tax'];
$totalAmount = $row['inventory_'.$table.'_value'];
$discount = $row['inventory_'.$table.'_discount'];
$grandTotal = $row['inventory_'.$table.'_total'];

$ims->query ="SELECT * FROM inventory_sales_product WHERE inventory_sales_id = :inventory_sales_id";
		$ims->execute(	array(':inventory_sales_id' =>  $orderId));
		$product_result = $ims->statement_result();
		$count = 0;
		$total = 0;
 $table = '<style>
.star img {
    visibility: visible;
}</style>
<table align="center" cellpadding="0" cellspacing="0" class="w-100" style="border:1px solid black;margin-bottom: 10px;">
   <tbody>
      <tr>
         <td colspan="5" class="text-center text-danger" style="text-decoration: underline; font-size: 25px;">TAX INVOICE</td>
      </tr>
      <tr>
         <td rowspan="5" colspan="2" style="border-left:1px solid black;" ><img src="'.$data["company_logo"].'" alt="logo" width="250px;"></td>
         <td colspan="3" class="text-right" >ORIGINAL</td>
      </tr>
     
      <tr>
         <td colspan="3"  class="text-right text-danger" style="font-weight: 600;text-decoration: underline;font-size: 25px;">'.ucwords($data["company_name"]).'</td>
      </tr>
      <tr>
         <td colspan="3" class="text-right">'.$data["company_address"].'</td>
      </tr>     
      <tr>
         <td colspan="3" class="text-right">Tele: '.$data["company_contact_no"].'</td>
      </tr>
      <tr>
         <td colspan="3" class="text-right text-primary" style="text-decoration: underline;">'.$data["company_email"].'</td>
      </tr>
      <tr>
         <td colspan="2" class=" p-0 align-top" style="border-right:1px solid black;">
            <table align="left" class="w-100 border-dark" cellpadding="0" cellspacing="0" style="border: thin solid">
               <tbody>
                  <tr>
                     <td class="align-top text-danger" style="width: 74px;" rowspan="3">TO, </td>
                     <td  class="border-danger" style="border-bottom-style: solid; border-bottom-width: thin; ">&nbsp;'.$clientName.'</td>
                  </tr>
                  <tr class="border-dark" style="border-style: solid;border-left:none; border-width: thin;">
                     <td>&nbsp;</td>
                  </tr>
                  <tr>
                     <td >&nbsp;</td>
                  </tr>                  
               </tbody>
            </table>           
         </td>
         <td class="p-0" class="align-top" colspan="3">
            <table align="left" cellpadding="0" cellspacing="0" class="w-100" >
               <tbody>
                  <tr class="border-dark" style="border-style: solid;border-width: thin;" >
                     <td>Bill No : '.$row["inventory_sales_id"].'</td>
                  </tr>
                  <tr>
                     <td>Date: '.$orderDate.'</td>
                  </tr>
                  <tr>
                     <td style="height: 52px;"> Address: '.$clientContact.'</td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
      <tr class="text-center bg-dark text-white">
         <td  style="width: 123px; border: 1px solid black;border-right-color: white;-webkit-print-color-adjust: exact;">D.C.NO </td>
         <td class=" w-50" style="border-style: solid;border-width: thin;border-right-color: white;-webkit-print-color-adjust: exact;">Description Of Goods</td>
         <td  style="width: 150px;border-style: solid;border-width: thin;-webkit-print-color-adjust: exact;">Qty.</td>
         <td  style="width: 150px;border-style: solid;border-width: thin;-webkit-print-color-adjust: exact;">Rate&nbsp; '.$data["company_currency"].'</td>
         <td  style="width: 150px;border-style: solid;border-width: thin;-webkit-print-color-adjust: exact;">Amount&nbsp; '.$data["company_currency"].'</td>
      </tr>';
      $count = 1;
      $total = $subTotal;
      $total_tax_amount=0;
		$total_actual_amount = 0;
foreach($product_result as $sub_row) {       
   $product_data = $command->fetch_product_details($sub_row['product_id']);
   $actual_amount = $sub_row["quantity"] * $sub_row["price"];
			$tax_amount = ($actual_amount * $sub_row["tax"])/100;
			$total_product_amount = $actual_amount + $tax_amount;
			$total_actual_amount = $total_actual_amount + $actual_amount;
			$total_tax_amount = $total_tax_amount + $tax_amount;
			$total = $total + $total_product_amount;
   $table .= '<tr class="text-center border-dark" style="height: 27px;">
         <td >'.$count.'</td>
         <td >'.$product_data['product_name'].'</td>
         <td >'.$sub_row["quantity"] .'</td>
         <td >'.$sub_row["price"].'</td>
         <td >'. $actual_amount.'</td>
      </tr>
   ';
   $count++;
} // /while
      $table.= '
      <tr class="border-dark" style="border-bottom: 1px solid black;">
         <td style="height: 27px;"></td>
         <td style="height: 27px;"></td>
         <td style="height: 27px;"></td>
         <td class=" bg-dark text-white text-center border-dark "style="width: 149px;border-style: solid;border-width: thin;padding-left: 5px;-webkit-print-color-adjust: exact;">Total</td>
         <td  class="text-center border-dark" style="width: 218px; border-style: 1px solid; border-width: thin;  border-color: black; ">'.$row['inventory_sales_sub_total'].'</td>
      </tr>
      <tr>
         <td colspan="3" style="border: 1px solid black;padding: 5px;">Neft For:- Bank Name</td>
         <td rowspan="2" class=" bg-dark text-white text-center pl-1" style="border-style: solid;width: 199px;-webkit-print-color-adjust: exact;">Discount</td>
         <td rowspan="2" class="text-center border-dark" style="border: 1px solid black;width: 288px;">'.$row['inventory_sales_discount'].'</td>
      </tr>
      <tr>
         <td colspan="3" style="border: 1px solid black;width: 859px;padding: 5px;">Branch:- branch Address</td>
      </tr>
      <tr>
         <td colspan="3" style="border: 1px solid black;padding: 5px;"></td>
         <td rowspan="2" class=" bg-dark text-white text-center pl-1" style="border-style: solid;width: 149px;-webkit-print-color-adjust: exact;">Tax</td>
         <td rowspan="2"  class="text-center border-dark" style="width:218px;border: 1px solid black;">'.$row['inventory_sales_tax'].'
         </td>
      </tr>
      <tr>
         <td colspan="3" style="border-bottom: 1px solid black;border-left: 1px solid black;padding: 5px;">AC Name:- '.ucwords($data["company_name"]).'</td>
      </tr>
      <tr>
         <td colspan="3" class="pl-1"style="border-bottom: 1px solid black;border-left: 1px solid black;">Bank IBAN CODE:- 78945612301 </td>
         <td  class=" bg-dark text-white text-center pl-1"style="border: 1px solid #fff;-webkit-print-color-adjust: exact;">G. Total</td>
         <td  class="text-center border-dark" style="border: 1px solid black;">'.$row['inventory_sales_total'].'</td>
      </tr>
      <tr>
         <td colspan="3" class="pl-1" style="border-left: 1px solid black;border-bottom: 1px solid black;">Amount in words</td>         
      </tr>
      <tr>
         <td colspan="3" class="pl-1" style="border: 1px solid black;"> </td>
         <td rowspan="3" colspan="2" ><b style="color:blue;font-size: 2rem; border: 1rem solid black;
         padding: 0.1rem 0.5rem; text-transform: uppercase; ">'.ucwords($data["company_name"]).'</b></td>
      </tr>
      <tr>
         <td colspan="3" style="border: 1px solid black;padding-left: 5px;">
            * Intrest will be charged upon all acounts remaning unpaid after due date 
         </td>
      </tr>
   </tbody>
</table>';
?>
<!DOCTYPE html>
		<html>
		<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta content='IE=edge,chrome=1' http-equiv='X-UA-Compatible' />
		<link rel="stylesheet" href="<?php echo CSS_URL?>bootstrap.min.css">
</head>
<body>
<?=  $table;?>
</body>