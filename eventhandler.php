<?php
include_once('config.php');
include_once(INC.'init.php');
include_once(CLASS_DIR.'graphs.php');
$graphs=new graphs;

if (isset($_POST['get_full_data'])) {
      $table=$_POST['table'];
      $type=$_POST['type'];
      if($table=='product')
      {
        $data=$graphs->categoryvaluehtml();
        $labels=$graphs->categoryhtml();
      }
      else
      {
        $data=$graphs->getfullmonthvaluehtml($table,$type);
        $labels=$graphs->getfullmonthhtml();
      }
      $finalresponse=array('labels'=>$labels,'data'=>$data);
      echo json_encode($finalresponse);	
    }

    if (isset($_POST['get_product_list'])) {
      $status=""; 
      if(isset($_POST['status']))
      $status=$_POST['status'];
      echo json_encode($command->fill_product_list('',$status));	
    }

    if (isset($_POST['get_tax_list'])) { 
      echo json_encode($command->fill_tax_list());	
    }
    if(isset($_POST['get_brand_list'])){
      echo json_encode($command->fill_brand_list($_POST['category_id']));
    }
    if(isset($_POST['max_available_quantity'])){
      $id=$_POST['product_id'];
      echo json_encode($command->available_product_quantity($id));
    }

    ?>