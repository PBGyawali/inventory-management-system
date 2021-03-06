<?php

class stats extends ims{

	function get_user_wise_total_sales(){	
		return $this->get_user_wise_total('sales');
	}
	function get_user_wise_total_purchase(){	
		return $this->get_user_wise_total('purchase');
	}
	function get_user_wise_total($table,$debug=false)
	{	
		$currency=$this->website_currency_symbol();				
		$sum=array(
			'SUM('=>array(
				"inventory_".$table.".inventory_".$table."_sub_total"=>$table."_total",
			"CASE WHEN inventory_".$table.".payment_status = 'cash' 
			THEN inventory_".$table.".inventory_".$table."_sub_total ELSE 0 END"=>"cash_".$table."_total",
			"CASE WHEN inventory_".$table.".payment_status = 'credit' 
			THEN inventory_".$table.".inventory_".$table."_sub_total ELSE 0 END"=>"credit_".$table."_total"
		),
			'(user.'=>array('user_name'=>'user_name','user_id'=>'user_id')	
		);
		$join=array(
			'INNER JOIN'=>array('user'=>'user.user_id=inventory_'.$table.'.user_id')
		);
		$attr['groupby']="inventory_".$table.".user_id	";
		$result =$this->total('inventory_'.$table,$sum,'inventory_'.$table.'.inventory_'.$table.'_status','active','',$join,$attr);
			
		 $output = '
		<div class="table-responsive">
			<table class="table table-bordered table-striped">
				<tr>
					<th class="text-center">Name</th>
					<th class="text-center">Total  Deals</th>
					<th class="text-center">Total '.$table.' Value</th>
					<th class="text-center">Total Cash '.$table.'</th>
					<th class="text-center">Total Credit '.$table.'</th>
				</tr>
		';		
		${"total_".$table} = 0;
		${"total_cash_".$table} = 0;
		${"total_credit_".$table} = 0;
		$total_user_transaction=$totaltransaction=0;
		foreach($result as $row)
		{ 	
			$total_user_transaction=$this->CountTable("inventory_".$table,'user_id',$row['user_id']);
			$totaltransaction+=$total_user_transaction;
			$output .= '
			<tr>
				<td class="text-left">'.ucwords($row['user_name']).'</td>
				<td class="text-right">'.$total_user_transaction.'</td>
				<td class="text-right">'.$currency.' '.$row[$table."_total"].'</td>
				<td class="text-right"> '.$currency.' '.$row["cash_".$table."_total"].'</td>
				<td class="text-right"> '.$currency.' '.$row["credit_".$table."_total"].'</td>
			</tr>
			';	
			${"total_".$table} = ${"total_".$table} + $row[$table."_total"];			
			${"total_cash_".$table} = ${"total_cash_".$table} + $row["cash_".$table."_total"];
			${"total_credit_".$table} = ${"total_credit_".$table} + $row["credit_".$table."_total"];
		}
		$output .= '
		<tr>
			<td class="text-right"><b>Total</b></td>
			<td class="text-right"><b> '.$totaltransaction.'</b></td>
			<td class="text-right"><b> '.$currency.' '.${"total_".$table}.'</b></td>
			<td class="text-right"><b> '.$currency.' '.${"total_cash_".$table}.'</b></td>
			<td class="text-right"><b>'.$currency.' '.${"total_credit_".$table}.'</b></td>
		</tr></table></div>
		';
		return $output;
	}
	function get_sales_target(){ 
		if($this->is_user())
		return $this->get_target('sales','user_id',$_SESSION['user_id']);
		else
		return $this->get_target('sales');			
	}
	function get_revenue_target(){ 
		if($this->is_user())
		return $this->get_target('revenue','user_id',$_SESSION['user_id']);
		else
		return $this->get_target('revenue');			
	}
	function get_target($item,$column=null,$value=null){ 
		$target= $this->get_data('company_'.$item.'_target','company_table');
		if($target==0)		
			return 0;
		if ($item=='sales')
			$status=$this->CountTable('inventory_sales',$column,$value);
		else
			$status=$this->total('inventory_sales','inventory_sales_sub_total',$column,$value);
		$current_status=($status/$target)*100;		 
		 if($current_status>100)
		 	return 100;
		 else 
		 	return number_format($current_status);					
	}

	
}


?>