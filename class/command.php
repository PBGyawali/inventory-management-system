<?php

class command extends ims{

	function fill_category_list($value=null){
		return $this->fill_list('category',$value);
	}	
	function fill_brand_list($category_id,$value=null){	
		return $this->fill_list('brand',$value,'','active',$category_id);
	}	
	function fill_product_list($value=null,$status=null){
		return $this->fill_list('product',$value,'',$status);
	}
	function fill_supplier_list($value=null){
		return $this->fill_list('supplier',$value,'name');
	}
	function fill_tax_list($value=null){
		
		return $this->fill_list('tax',$value,'percentage');
	}
	function fill_unit_list($value=null){
		return $this->fill_list('unit',$value,'name');
	}
	function fill_list($category,$value,$columm=null,$status='active',$category_id=null)
	{ 	
		$this->query ="SELECT * FROM $category ";
		if ($status||$category_id)
		$this->query .= " WHERE ";
		if ($status)
		$this->query .= " $category"."_status= '$status'";
		if ($status && $category_id)
		$this->query .= " AND ";
		if($category_id)
			$this->query .= " category_id = ? ";		
		$this->query .= " ORDER BY $category"."_name"." ASC	";
		$this->execute(array($category_id));
		$result = $this->statement_result();
		$output = '';		
		$selected_value = $value;// value from database	
		$output .= '<option value="" selected hidden disabled> Select '.ucwords($category).'</option>';
		foreach($result as $row)
		{	
			if ($category=='product' && $status==NULL && $value==NULL)
			{
				$productquantity=$this->available_product_quantity($row['product_id']);
				if( $productquantity<=0)
				$disabled="disabled";
				else
				$disabled="";
			}
			else
			$disabled="";			
			$selected = ($selected_value == $row[$category."_id"]) ? "selected" : "";
			$output .= '<option value="'.$row[$category."_".($columm?$columm:"id")].'"'. $selected.' '.$disabled. '>'.$row[$category."_name"].
			 ' '.(($columm=='percentage'?"(".$row[$category."_".$columm]."%)":"")).'</option>';
		}
		return $output;
	}	
	
	function get_user_name($user_id){ 
		return $this->get_data('user_name','user','user_id',$user_id);			
	}
	
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
	function fetch_product_details($product_id){
		$result = $this->getAllArray('product','product_id',$product_id);
		foreach($result as $row)
		{
			$output['product_name'] = $row["product_name"];
			$output['quantity'] = $row["product_quantity"];
			$output['price'] = $row['product_base_price'];
			$output['tax'] = $row['product_tax'];
			$output['opening stock'] = $row["opening_stock"];
			$output['defective quantity'] = $row["defective_quantity"];
		}
		return $output;
	}	
	function available_product_quantity($product_id){	
		$product_data = $this->fetch_product_details($product_id);
		$salestotal =$this-> get_product_quantity($product_id,'sales');
		$purchasetotal =$this-> get_product_quantity($product_id,'purchase');
		$available_quantity = intval($product_data['opening stock'])+intval($product_data['quantity'])-intval($product_data['defective quantity']) - intval($salestotal)+intval($purchasetotal);	
		return $available_quantity;
	}		
	function get_product_quantity($product_id,$table){		
		$this->query="SELECT inventory_".$table."_product.quantity FROM inventory_".$table."_product 
		LEFT JOIN inventory_".$table." ON inventory_".$table.".inventory_".$table."_id = inventory_".$table."_product.inventory_".$table."_id
		WHERE inventory_".$table."_product.product_id = ? AND inventory_".$table.".inventory_".$table."_status = 'active'	";
		$this->execute(array($product_id));
		$result = $this->statement_result();
		$total = 0;
		foreach($result as $row){
			$total += $row['quantity'];
		}
		return $total;
	}
	
}
?>