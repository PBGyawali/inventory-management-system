<?php

class ims
{
	public $base_url = BASE_URL;
	public $login=LOGIN_URL;	
	public $dashboard=DASHBOARD_URL;
	public $connect;
	public $query;
	public $statement;

	function __construct()
	{
		try {
			$this->connect = new PDO("mysql:host=".DB_SERVER.";dbname=".DB_NAME.";", DB_USERNAME, DB_PASSWORD);
			$this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->query = " SET NAMES utf8 ";
			if (session_status() === PHP_SESSION_NONE){session_start();}			
		}		
		catch(PDOException $e){
			$msg = date("Y-m-d h:i:s A")." Connection, PDO: ".$e->getMessage()."\r\n";
			error_log($msg, 3, ERROR_LOG);
		} catch(Exception $e){
			$msg = date("Y-m-d h:i:s A")." Connection, General: ".$e->getMessage()."\r\n";
			error_log($msg, 3, ERROR_LOG);
		}
        return $this->connect;		
	}
	function execute($data = null){
		$this->statement = $this->connect->prepare($this->query);
		$data=$this->check_array($data);
		return $this->statement->execute($data);		
	}

	function row_count(){
		return $this->statement->fetchColumn();
	}

	function statement_result()	{
		return $this->statement->fetchAll();
	}
	function get_array()	{		
		return $this->statement->fetch();
    }
	function get_result()	{
		return $this->connect->query($this->query, PDO::FETCH_ASSOC);
	}

	function close() {
        $this->statement=NULL;
    }
	function id() {
		return $this->connect->lastInsertId();
	}
	function row() {
		return $this->statement->rowCount();
    }

	function is_login(){
		if(isset($_SESSION['login']) && !empty($_SESSION['login']))	{			
			return true;	
		}
		return false;
	}
	function is_user(){
		if(isset($_SESSION['type']))
		{
			if(($_SESSION["type"] == 'user'))
			{
				return true;
			}
			return false;
		}
		return false;
	}

	function is_admin()	{
		if(isset($_SESSION['type']))
		{
			if(($_SESSION["type"] == 'master'))
			{
				return true;
			}
			return false;
		}
		return false;
	}

	function clean_input($string,$html=null)	{
		  $string = trim($string);		 
		  $string = stripslashes($string);
		  if($html){
				$string = htmlspecialchars($string);			
		  }		  
		  return $string;
	}

	function get_datetime(){
		return date("Y-m-d H:i:s",  STRTOTIME(date('h:i:sa')));
	}

	function make_avatar($character)	{
		$image_name=time() . ".png";
		$path = 'userimages/'. $image_name;
		$image = imagecreate(200, 200);
		$red = rand(0, 255);
		$green = rand(0, 255);
		$blue = rand(0, 255);
		imagecolorallocate($image, $red, $green, $blue);  
		$textcolor = imagecolorallocate($image, 255,255,255); 	
		imagettftext($image, 100, 0, 55, 150, $textcolor,'fonts/arial.ttf', $character);
		imagepng($image, $path);
		imagedestroy($image);
		return $image_name;
	}

	function UsersArray($placeholder=null,$value=null){
		if(isset($_SESSION['user_id'])||($placeholder!=null && $value!=null)){
			$placeholderset=(($placeholder)?$placeholder:'user.user_id');
			$setvalue=(($value)?$value:$_SESSION['id']);
			return $this->getArray('user',$placeholderset,$setvalue,'AND',1);			
		}
			return null;		
	}
	function website_name(){
			return $this->get_data('company_name','company_table');	
	}
	function website_currency(){
		return $this->get_data('company_currency','company_table');	
	}
	function website_currency_symbol(){
		return $this->get_data('currency_symbol','company_table');	
	}

	function check_array($value)	{
		if (is_array($value))  
			return $value;
		else 
			return array($value);		
	}
	function create_placeholder($value)	{
		if (is_array($value))
		{	$marker=array();
			foreach($value as $values)
			{	$values='?'; 
			array_push($marker,$values);      
			}				
		  return $marker;
		}
		else	
			return array('?');	
	}
	
	function insert($table,$column,$value)	{	
		$column=$this->check_array($column);
		$column_condition=$this->implode_array($column,'',',');
		$value=$this->check_array($value);   		
		$marker= implode(', ', array_fill(0, sizeof($column), '?'));
		$this->query = "INSERT INTO $table ($column_condition) VALUES($marker)";		
		return $this->execute($value);
	}

	
	function UpdateDataColumn($table,$column,$value,$placeholder,$condition,$combine='AND')	{	
		$column_condition=$this->implode_array($column,'?',',');
		$value=$this->check_array($value);
		$condition=$this->check_array($condition);
		$placeholder_condition=$this->implode_array($placeholder,'?',$combine);
		$finalvalue=array_merge($value,$condition);
		$this->query = "UPDATE $table SET $column_condition WHERE $placeholder_condition ";		
		return $this->execute($finalvalue);
	}
	

	function CountTable($table,$condition=null,$value=null,$combine='AND')
	{    
	  if ($value!=null)  {		  
		  $marker=$this->create_placeholder($condition);	  
		  if(!is_array($condition)&&is_array($value))		  
				$marker=$this->create_placeholder($value);
		 $row_condition=$this->implode_array($condition,$marker,$combine);
	  }
	  else	
			$row_condition=$this->implode_array($condition);		  
	 	$this->query = "SELECT Count(*) AS total FROM $table ";	
		if(!empty($row_condition)&& is_string($row_condition))		
			$this->query .= " WHERE ".$row_condition;	
		$this->execute($value);
		$row= $this->get_array();	
		return $row['total'];
	}	


	function get_data($data=null,$table=null,$placeholder=null,$conditions=null,$combine='AND',$limit=null)
	{   
		if (empty($table)) 
			return null;
		if(!$data)
			$requireddata="*";
		else	
			$requireddata=$this->implode_array($data,'',',');
		if ($conditions!=null)
			$row_condition=$this->implode_array($placeholder,'?',$combine);
		else
			$row_condition=$this->implode_array($placeholder);
		$this->query ="SELECT $requireddata FROM $table";
		if(!empty($row_condition)&& is_string($row_condition))
				$this->query .= " WHERE ".$row_condition;			
		if($limit)
			$this->query .= " LIMIT ".$limit; 
		$this->execute($conditions);
		$row= $this->get_array();	
		if ($row)
		{	if(!$data || is_array($data))			
				return $row;	
			else
				return $row[$data];	
		}						
		return null;	
	}

	function getArray($table,$placeholder=null,$conditions=null,$combine='AND',$limit=null){//gives one array
		return $this->get_data('',$table,$placeholder,$conditions,$combine,$limit);	
	}

	function getAllArray($table,$column=null,$value=null,$combine='AND',$limit=null,$orderby=null,$order='DESC'){	//gives all array	
		if ($value!=null)		
			$row_condition=$this->implode_array($column,'?',$combine);		
		else		
			$row_condition=$this->implode_array($column);
		$this->query ="SELECT * FROM $table";
		if(!empty($row_condition)) 
			$this->query .= " WHERE ".$row_condition;	
		if(!empty($orderby)) 
			$this->query .= " ORDER BY ". $orderby ." ".$order;
		if(!empty($limit)) 
			$this->query .= " LIMIT ".$limit;
		$this->execute($value);
		return $this->statement_result();	
	}

	function Delete($table,$placeholder=NULL,$conditions=NULL,$combine='AND'){
		$row_condition=$this->implode_array($placeholder,'?',$combine); 
		$this->query ="DELETE FROM $table WHERE ". $row_condition;
		return $this->execute($conditions);	
	}

	function total($table,$column,$placeholder,$value,$combine='AND')
	{
			$value=$this->check_array($value);
				$row_condition=$this->implode_array($placeholder,'?',$combine);
				$this->query = "SELECT IFNULL(SUM($column), 0) AS total FROM $table ";
				if(!empty($row_condition)) 
					$this->query .= " WHERE ".$row_condition;				
				$this->execute($value);
				$row=$this->get_array();
				if ($row)
						return $row['total'];				
				return 0;
	}
	function implode_array($placeholder=null,$conditions=null,$combine='AND'){
		$finalarray=array();
		if ($placeholder==null)
				return null;
		elseif (!is_array($placeholder)&&!empty($placeholder) && empty($conditions))						
				return " ".$placeholder." "; 
		elseif (!is_array($placeholder) && !empty($conditions) && !is_array($conditions))		
				if	($conditions=='?')
					return " ".$placeholder."="."?"." ";
				else
					return " ".$placeholder."="."'".$conditions."'"." "; 		
		elseif (is_array($placeholder)&& empty($conditions))				
				return implode(' '.$combine.' ', $placeholder);
		elseif (is_array($placeholder)&& is_array($conditions) && !empty($conditions))							
				if ( in_array('?',$conditions))
					foreach($conditions as $key=> $condition)						 
						array_push($finalarray," ".$placeholder[$key]."=".$condition." ");
				else
					foreach($conditions as $key=> $condition)							
						array_push($finalarray," ".$placeholder[$key]."="."'".$condition."'"." ");		
		elseif (is_array($placeholder)&& !is_array($conditions) && !empty($conditions))					
				if ($conditions=='?')
					foreach($placeholder as $key=> $place)
						array_push($finalarray," ".$place."=".$conditions." ");
				else
					foreach($placeholder as $key=> $place)
						array_push($finalarray," ".$place."="."'".$conditions."'"." ");		
		else					
				if ( in_array('?',$conditions))						
					foreach($conditions as $key=> $condition)							
						array_push($finalarray," ".$placeholder."=".$condition." "); 
				else						
					foreach($conditions as $key=> $condition)
						array_push($finalarray," ".$placeholder."="."'".$condition."'"." "); 
		if ($combine)
				return implode($combine,$finalarray); 
		return  $finalarray;    
	}
}


?>