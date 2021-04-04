<?php
//report.php
include_once('config.php');
include_once(INC.'init.php');
include_once (CLASS_DIR.'stats.php');
$stats= new stats();
if(!$ims->is_login())
{
    header("location:".$ims->login);
}
elseif(!$ims->is_admin())
{
    header("location:".$ims->dashboard);
}
include_once(INC.'header.php');
$currency=$ims->website_currency_symbol();
$sales_target=$stats-> get_sales_target();
$revenue_target=$stats-> get_revenue_target();
?>
	<br />
		
	<?php if($ims->is_admin()):?>
               
			   <div class="row "> 
				   <div class="col mb-4">
					   <div class="card shadow border-left-info py-2">
						   <div class="card-body">
							   <div class="row align-items-center no-gutters">
								   <div class="col mr-2">
									   <div class="text-uppercase text-info font-weight-bold text-xs mb-1"><span>Total sales Target</span></div>
									   <div class="row no-gutters align-items-center">                                           
										   <div class="col value-indicator">
											   <div class="progress progress-sm">
												   <div class="progress-bar bg-info progress-bar-striped progress-bar-animated" id ="progressbar_1" aria-valuenow="<?php echo $sales_target;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $sales_target;?>%;"><span class="sr-only"><?php echo $sales_target;?>%</span></div>
											   </div>
										   </div>
										   <div class="col-auto value-indicator-text">
											   <div class="text-dark text-right font-weight-bold h5 mb-0 ml-3"><span class="progress-value"><?php echo $sales_target;?>%</span></div>
										   </div>
									   </div>
								   </div>
								   <div class="col-auto"><i class="fas fa-clipboard-list fa-2x text-gray-300"></i></div>
							   </div>
						   </div>
					   </div></div> 
				   

			   </div>
			   <div class="row "> 
				   <div class="col mb-4">
					   <div class="card shadow border-left-info py-2">
						   <div class="card-body">
							   <div class="row align-items-center no-gutters">
								   <div class="col mr-2">
									   <div class="text-uppercase text-info font-weight-bold text-xs mb-1"><span>Total revenue Target</span></div>
									   <div class="row no-gutters align-items-center">                                           
										   <div class="col value-indicator">
											   <div class="progress progress-sm">
												   <div class="progress-bar bg-info progress-bar-striped progress-bar-animated" id ="progressbar_1" aria-valuenow="<?php echo $revenue_target;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $revenue_target;?>%;"><span class="sr-only"><?php echo $revenue_target;?>%</span></div>
											   </div>
										   </div>
										   <div class="col-auto value-indicator-text">
											   <div class="text-dark text-right font-weight-bold h5 mb-0 ml-3"><span class="progress-value"><?php echo $revenue_target;?>%</span></div>
										   </div>
									   </div>
								   </div>
								   <div class="col-auto"><i class="fas fa-clipboard-list fa-2x text-gray-300"></i></div>
							   </div>
						   </div>
					   </div></div> 
				   

			   </div>
			   <?php endif?>
		<?php if($ims->is_admin()):	?>
		<div class="row "> 
                <div class="col-md-12">
                    <div class="card shadow border-left-info py-2">
                        <div class="card-header text-center text-uppercase text-info"><strong>Total sales transaction Value achieved by each User (As on <?= '<span class="text-lowercase">'.date('jS').'</span>' .', '.date('F').', '.date('Y'); ?>)</strong></div>
                        <div class="card-body text-center" >
                            <?php echo $stats->get_user_wise_total_sales(); ?>
                        </div>
                    </div>
                </div>
		</div>
		<?php endif	?>
		<?php if($ims->is_admin()):	?>
		<div class="row "> 
                <div class="col-md-12">
                    <div class="card shadow border-left-info py-2">
                    <div class="card-header text-center text-uppercase text-info"><strong>Total purchase transaction Value achieved by each User (As on <?= '<span class="text-lowercase">'.date('jS').'</span>' .', '.date('F').', '.date('Y'); ?>)</strong></div>
                        <div class="card-body text-center" >
                            <?php echo $stats->get_user_wise_total_purchase(); ?>
                        </div>
                    </div>
                </div>
		</div>
		<?php endif	?>
		<footer class="bg-white sticky-footer mt-3">
			<div class="container my-auto">		
				<div class="text-center my-auto copyright">
					<span>Copyright © <?php echo $stats->website_name()?> 
						<script>
							document.write(new Date().getFullYear())
						</script>
					</span>			
				</div>			
				<a class="no-border fixed-bottom text-right size-30 scroll-to-top" data-href="#page-top"><i class="fas  fa-4x fa-angle-up"></i></a>
			</div>
        </footer>
        	
<script type="text/javascript" src="<?php echo JS_URL?>chart.min.js"></script>
<script type="text/javascript" src="<?php echo JS_URL?>jquery.easing.min.js"></script>
<script type="text/javascript" src="<?php echo JS_URL?>theme.js"></script>
<?php include_once(INC.'footer.php');?>
<script type="text/javascript">


$(document).ready(function()
{
	$('[data-bs-chart]').each(function(index, elem) {
		window ['figure_'+index]=this.chart = new Chart($(elem), $(elem).data('bs-chart'));
	});

    function emptyspace(canvas){  		         
        const context = canvas.getContext('2d');
        // Store the current transformation matrix
            context.save();

            // Use the identity matrix while clearing the canvas
            context.setTransform(1, 0, 0, 1, 0, 0);
            context.clearRect(0, 0, canvas.width, canvas.height);

            // Restore the transform
            context.restore();
            return true;        
    } 

    $('.bargraph').on('click', function()
    {   var id=$(this).data('id');
		var type=$(this).data('type');
        var month=$('#'+type+'_'+id).data('month');
        var monthvalue=$('#'+type+'_'+id).data('monthvalue');        
        updateGraphmonths(month,monthvalue,id,'bar');
    });
    $('.permonth').on('click', function()
    {   var id=$(this).data('id'); 
		var month=$(this).data('month');
        var monthvalue=$(this).data('monthvalue');
        updateGraphmonths(month,monthvalue,id);       
        $('#fullmonths'+'_'+id).show();
        $('#permonth'+'_'+id).hide();
        $('#bargraph'+'_'+id).data('type','permonth');        
    });  

    $('.fullmonths').on('click', function()
    {   var id=$(this).data('id');
		var month=$(this).data('month');
        var monthvalue=$(this).data('monthvalue');  
        updateGraphmonths(month,monthvalue,id);      
       $('#fullmonths'+'_'+id).hide();
        $('#permonth'+'_'+id).show();
        $('#bargraph'+'_'+id).data('type','fullmonths'); 
    });

    $('.refresh').on('click', function()
    { 	var id=$(this).data('id');
		var url=$(this).data('url');
		var table=$('#label_'+id).text().toLowerCase();
		var type=$('#type_'+id).text().toLowerCase();
      $.ajax
      ({
            url: url,
            method: 'post',
            data:  {get_full_data:1,table:table,type:type},
           dataType:"JSON",
            success: function(data)
            {   $('.test').html(data);
                updateGraphmonths(data.labels,data.data,id);                
            }
        });
    });


    function updateGraphmonths($fullmonths,$fullmonthvalue,id=null,$type='line')
    {  
	   var label=$('#label_'+id).text(); 
	   var canvas = document.getElementById("graph_canvas_"+id); 
	   $space= emptyspace(canvas); 
	   if (window['figure_'+id].chart!=undefined) {
		window['figure_'+id].chart.destroy();
						}
        if ($space)       
        { 
             window.chart = new Chart(canvas, 
            {
                    type: $type,
                    data: {
                            labels: $fullmonths,
                            datasets: 
                            [{
                                    data: $fullmonthvalue,
                                    borderWidth: 2,
                                    label:label,
                                    fill:true,
                                    backgroundColor:'rgba(78, 78, 78, 0.3)',
                                    borderColor:'rgba(78, 115, 223, 1)',      
                            }]
                        },
                    options: {
                                responsive: true,
                                maintainAspectRatio:false,    
                                legend: 
                                {
                                    display: false
                                },
                                title:{},
                                scales:
                                {
                                    xAxes:
                                    [{
                                        gridLines:
                                        {
                                            color:'rgb(234, 236, 244)',zeroLineColor:'rgb(234, 236, 244)',
                                            drawBorder:false,drawTicks:false,borderDash:[2],zeroLineBorderDash:[2],
                                            drawOnChartArea:false
                                        },
                                        ticks:
                                        {
                                            fontColor:'#858796',padding:20
                                        }
                                    }],
                                    yAxes:
                                    [{
                                        gridLines:
                                        {   color:'rgb(234, 236, 244)',
                                            zeroLineColor:'rgb(234, 236, 244)',drawBorder:false,
                                            drawTicks:false,borderDash:[2],zeroLineBorderDash:[2]
                                        },
                                        ticks:
                                        {
                                            fontColor:'#858796',padding:20
                                        }
                                    }]
                                }
                            }
            });
        }
    } 

  
});

</script>
<script src= "<?php echo JS_URL .'progressbar_bootstrap.js'?>"></script>