<?php  
$index_active=$dashboard_active=$report_active=$graph_active=$user_active=$supplier_active=$sales_active=$purchase_active=$product_active=$brand_active=$tax_active=$unit_active=$category_active='inactive_page';					
${$page."_active"} = 'active_page';
$user_type= (isset($_SESSION['type']))?$_SESSION['type']:'';
$username=(isset($_SESSION['user_name']))?ucwords($_SESSION['user_name']):'';
$user_id=(isset($_SESSION['user_id']))?$_SESSION['user_id']:'';
$row=$ims->getArray('user','user_id',$user_id);
$profile_image=(!empty($_SESSION['profile_image']))?$_SESSION['profile_image']:'user_profile.png';
?>
</head>
<body class="p-0" id="page-top">
<div class="container-fluid bg-dark d-inline p-0 " style="z-index:10;">
		<div class="col-12 text-center pl-0 ">
		<?php if($ims->is_login()):?>
			<div class="d-flex flex-column" >
                <nav class="navbar navbar-light bg-dark navbar-expand topbar static-top p-0 m-0">
					<ul class="nav navbar-nav flex-nowrap ml-auto text-left ">
					<li ><a href="index.php"><span class="btn mr-1 <?php echo $dashboard_active ?> "> <i class="fas fa-tachometer-alt"></i> Dashboard</span></a></li>
					<?php if($ims-> is_admin()):?>	
					<li ><a href="report.php"><span class="btn mr-1 <?php echo $report_active ?> "> <i class="fas fa-file-invoice"></i> Report</span></a></li>
					<li ><a href="graph.php"><span class="btn mr-1 <?php echo $graph_active ?> "> <i class="fas fa-chart-line"></i> Graph</span></a></li>
					<?php endif	?>
					<li ><a  href="sales.php"><span class="btn mr-1 <?php echo $sales_active ?>"><i class="fas fa-shopping-cart"></i> Sales</span></a></li>
					<li ><a  href="purchase.php"><span class="btn mr-1 <?php echo $purchase_active ?>"><i class="fas fa-cart-arrow-down"></i> Purchase</span></a></li>		
					<?php if($ims-> is_admin()):?>		
						<li ><a  href="user.php"><span class="btn mr-1 <?php echo $user_active ?>"><i class="fas fa-users"></i> Users</span></a></li>	
						<li ><a  href="supplier.php"><span class="btn mr-1 <?php echo $supplier_active ?>"><i class="fas fa-id-card"></i> Supplier</span></a></li>	
					<li ><a  href="product.php"><span class="btn mr-1 <?php echo $product_active ?>"><i class="fas fa-warehouse"></i> Product</span></a></li>		
					<li ><a  href="category.php"><span class="btn mr-1 <?php echo $category_active ?>"><i class="fas fa-sitemap"></i> Category</span></a></li>
					<li ><a  href="brand"><span class="btn mr-1 <?php echo $brand_active ?>"><i class="fas fa-list"></i> Brand</span></a></li>
					<li ><a  href="tax.php"><span class="btn mr-1 <?php echo $tax_active ?>"><i class="fas fa-hand-holding-usd"></i> Tax</span></a></li>
					<li ><a  href="unit.php"><span class="btn mr-1 <?php echo $unit_active ?>"><i class="fas fa-percentage"></i> Unit</span></a></li>		
					<?php endif	?>					                
						<li class="dropdown " role="presentation">							
							<a data-toggle="dropdown" class="position-relative" aria-expanded="false"><span id="user_uploaded_image_small" class=" btn text-white ml-1 pl-2"><?php echo $username?> <img src="<?php echo IMAGES_URL.$profile_image?>" class="img-fluid rounded-circle" width="30" height="30"/></a></span>
								<div class="dropdown-menu shadow dropdown-menu-right animated--grow-in" role="menu">
									<a class="dropdown-item" role="presentation" href="<?php echo BASE_URL.'profile.php'?>"><i class="fas fa-user fa-sm mr-2 "></i>&nbsp;Profile</a>
									<?php if($ims-> is_admin()):?>
									<a class="dropdown-item" role="presentation" href="<?php echo BASE_URL.'setting.php'?>"><i class="fas fa-cog fa-sm mr-2 "></i>&nbsp;Settings</a>
									<?php endif?>
									<div class="dropdown-divider"></div><form action="<?php echo BASE_URL.'logout.php'?>" method="post" class="logout_form"><button class="dropdown-item logout" role="presentation" type="submit" title="Clicking this button will log you out."><i class="fas fa-sign-out-alt mr-2"></i>&nbsp;Logout</button></form></div>
								</div>
						</li>
					</div>			
		<?php else:?>
				<nav class=" bg-dark topbar  text-center ">
					<ul class="flex-nowrap  text-center">	
						<h2 class=" text-center text-white"> WELCOME TO <?php echo strtoupper($ims->website_name());?></h2>
					</ul>
				</nav>						
		<?php endif?>
	</div>
</div>
<link rel="stylesheet" href="<?php echo CSS_URL.'bootstrap_style.css'?>" >
    <script src="<?php echo JS_URL.'confirmdefaults.js'?>"></script>
    <script src="<?php echo JS_URL.'confirm.js'?>"></script>
   
	