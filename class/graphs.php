<?php

class graphs extends ims{
	
	function getfullmonthhtml(){
		return $this->loopmonth("","-9");
	}		
	function getfullmonth(){
		return $this->loopmonth("&quot;","-9");
	}
	function getmonthhtml()	{		
		return $this->loopmonth();
	}	
	function getmonth()	{	
		return $this->loopmonth('&quot;');			
	}
	function loopmonth($quote=null,$start=1){
		$months=array();
		for($i=$start;$i<=date('n');$i++)
			array_push($months,$quote.substr(date('F', mktime(0, 0, 0, ($i), 2, date('Y'))),0,3).$quote);
		if ($quote)
			return  implode(',',$months);
		return  $months;
	}
	function getmonthvalue($table='sales',$type='number'){	
		return  $this->loopmonthvalue($table,$type,'&quot;');
	}	
	function getmonthvaluehtml($table='sales',$type='number'){
		return $this-> loopmonthvalue($table,$type);
	}
	function loopmonthvalue($table='sales',$type='number',$quote=null)
	{	$value=array();
		for($i=1;$i<=date('n');$i++)
			array_push($value,$quote .$this->getValuePerMonth($i,$table,$type).$quote);
		if ($quote)
			return  implode(',',$value);
		return  $value;
	}
	function getfullmonthvalue($table='sales',$type='number'){
		return $this->loopfullmonthvalue($table,$type,"&quot;");		
	}
	function getfullmonthvaluehtml($table='sales',$type='number'){		
		return $this->loopfullmonthvalue($table,$type);
	}
	function loopfullmonthvalue($table='sales',$type='number',$quote=null)
	{	$value=array();
		$startpos = date('n');
		for($i=1;$i<=12;$i++)
			array_push($value,$quote .$this->getValuePerMonth($i,$table,$type).$quote); 
		$output = array_merge(array_slice($value,$startpos), array_slice($value, 0, $startpos)); 		
		if ($quote)
			return implode(',',$output);
		return $output;
	}

	function getValuePerMonth($value,$table='sales',$type='number')	{		
		if($type=='number')
			return $this->CountTable('inventory_'.$table,'MONTH(inventory_'.$table.'_created_date)',$value);
		else
			return $this->total('inventory_'.$table,'inventory_'.$table.'_sub_total','MONTH(inventory_'.$table.'_created_date)',$value);
	}


	function category()	{
		return $this->get_category('&quot;');
	}
	
	function categoryvalue(){	
		return $this->get_category_value('&quot;');			
	}

	function categoryhtml()	{
		return $this->get_category();
	}
	
	function categoryvaluehtml(){
		return $this->get_category_value();				
	}
	function get_category($quote=null,$value=null){
		$allcategory= $this->getAllArray('category');		
		if($value)
			return array_column($allcategory, 'category_id');
		elseif($quote)
			return $quote.implode($quote.','.$quote,array_column($allcategory, 'category_name')).$quote;
		else
		return 	array_column($allcategory, 'category_name');
	}
	
	function get_category_value($quote=null){
		$value=array();
		$category_ids=$this->get_category('','true');
		foreach($category_ids as $category_id){			
			$attr['implodehere']='true';
			$sum=$this->total('product',array('product_quantity','opening_stock'),'category_id',$category_id,'','',$attr);			
			$difference=$this->total('product','defective_quantity','category_id',$category_id,'','',$attr);
			array_push($value,$quote .($sum-$difference).$quote);
		}
		if ($quote)
			return implode(',',$value);
		return $value;				
	}	
}


?>