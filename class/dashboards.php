<?php

class dashboards extends ims{	
	
	function count_total_user(){
		return $this->count_total('user','user_status');
	}
	function count_total_supplier(){
		return $this->count_total('supplier','supplier_status');
	}	
	function count_total_category(){
		return $this->count_total('category','category_status');
	}
	function count_total_unit(){
		return $this->count_total('unit','unit_status');
	}	
	function count_total_brand(){
		return $this->count_total('brand','brand_status');
	}	
	function count_total_product(){
		return $this->count_total('product','product_status');
	}	
	function count_total_tax(){
		return $this->count_total('tax','tax_status');
	}
	function count_total($table,$column=null,$active='active'){	
		return $this->CountTable($table,$column,$active);
	}	
	function count_total_sales_value(){
		return $this->count_transaction_value('sales');
	}
	function count_total_revenue_value(){
		return $this->count_transaction_value('sales','','active');
	}	
	function count_total_cash_sales_value(){
		return $this->count_transaction_value('sales','cash');
	}
	function count_total_cash_revenue_value(){
		return $this->count_transaction_value('sales','cash','active');
	}	
	function count_total_credit_sales_value(){
		return $this->count_transaction_value('sales','credit');	
	}
	function count_total_credit_revenue_value(){
		return $this->count_transaction_value('sales','credit','active');	
	}
	function count_total_purchase_value(){
		return $this->count_transaction_value('purchase');
	}
	function count_total_expense_value(){
		return $this->count_transaction_value('purchase','','active');
	}	
	function count_total_cash_purchase_value(){
		return $this->count_transaction_value('purchase','cash');
	}
	function count_total_cash_expense_value(){
		return $this->count_transaction_value('purchase','cash','active');
	}	
	function count_total_credit_purchase_value(){
		return $this->count_transaction_value('purchase','credit');	
	}
	function count_total_credit_expense_value(){
		return $this->count_transaction_value('purchase','credit','active');	
	}	
	function count_transaction_value($table,$type=null,$active=null){ 	
		$this->query ="SELECT IFNULL(SUM(inventory_".$table."_total), 0) AS total FROM inventory_".$table;
		if ($active||$type||!$this->is_admin())
			$this->query .= " WHERE ";
		if($active)		
			$this->query .= " inventory_".$table."_status='active' ";		
		if($type)
		{
			if($active)
				$this->query .= " AND  ";
			$this->query .= "  payment_status =? ";
		}
		if(!$this->is_admin())
		{
			if ($active||$type)
				$this->query .= " AND  ";			
			$this->query .= ' user_id = "'.$_SESSION["user_id"].'"';
		}	
		$this->execute(array($type));
		return number_format($this->row_count(),2);
	}	
		
			
	function Get_total_today_sales()
	{
		return $this->Get_sales_value('inventory_sales_created_date)');
	}

	function Get_total_yesterday_sales()
	{
		return $this->Get_sales_value('inventory_sales_created_date)',1);
	}

	function Get_last_seven_day_total_sales()
	{		
		return $this->Get_sales_value('inventory_sales_created_date)>',7);
	}

	function Get_total_sales()
	{		
		return $this->Get_sales_value();
	}

	function Get_sales_value($date=null,$interval=null)
	{
		$this->query = "SELECT Count(*) FROM inventory_sales " ;
		if ($date||$interval||!$this->is_admin() )
		$this->query .= " WHERE ";
		if ($date)		
		$this->query .= " DATE($date= DATE(NOW()) ";
		if($interval)
		$this->query .= " - INTERVAL $interval DAY ";
		if (!$this->is_admin()&& $date)		
		$this->query .= " AND ";
		if(!$this->is_admin())		
			$this->query .= "  user_id ='".$_SESSION["user_id"]."'";		
		$this->execute();
		return $this->row_count();
	}
	
	}


?>