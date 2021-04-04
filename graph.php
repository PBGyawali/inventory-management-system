<?php
//report.php
include_once('config.php');
include_once(INC.'init.php');

if(!$ims->is_login())
    header("location:".$ims->login);
elseif(!$ims->is_admin())
    header("location:".$ims->dashboard);

include_once (CLASS_DIR.'graphs.php');
$graphs= new graphs();
include_once(INC.'header.php');
$currency=$ims->website_currency_symbol();
?>
	<br />
		<div class="row "> 
			<div class="col">
				<div class="card shadow mb-4">
					<div class="card-header d-flex justify-content-between align-items-center">
					<h6 ></h6><!--needs to be kept as an element unless new element is added for css reasons-->
						<h6 class="text-primary font-weight-bold m-0"><span class="label" id="label_0">Sales</span> Overview (By <span id="type_0">number</span>)</h6>
						<div class="dropdown no-arrow"><button class="btn btn-link btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false" type="button"><i class="fas fa-ellipsis-v "></i></button>
							<div class="dropdown-menu shadow dropdown-menu-right animated--fade-in"	role="menu">
								<p class="text-center dropdown-header">Action:</p>
								<a role="presentation" id="permonth_0"  class="dropdown-item permonth"     data-id="0" data-month='[<?php echo $graphs->getmonth();?>]' data-monthvalue='[<?php echo $graphs->getmonthvalue();?>]' style="display:none"> Get only past months value</a>
								<a role="presentation" id="fullmonths_0" class="dropdown-item fullmonths"  data-id="0" data-month='[<?php echo $graphs->getfullmonth();?>]' data-monthvalue='[<?php echo $graphs->getfullmonthvalue();?>]'> Get all month data</a>
								<a role="presentation" id="refresh_0" class="dropdown-item refresh"        data-id="0" data-url="<?php echo BASE_URL?>eventhandler.php" > Refresh</a>
								<div class="dropdown-divider"></div>
								<a role="presentation" id="bargraph_0" class="dropdown-item bargraph" 	 data-id="0" data-type="permonth" > Show bar graphs</a></div>
						</div>
					</div>                                                                                                                                                                     
					<div class="card-body">
						<div class="chart-area" ><canvas id="graph_canvas_0" data-bs-chart="{&quot;type&quot;:&quot;line&quot;,&quot;data&quot;:{&quot;labels&quot;:[<?php echo $graphs->getmonth()?>],&quot;datasets&quot;:[{&quot;data&quot;:[<?php echo $graphs->getmonthvalue()?>],&quot;label&quot;:&quot;Sales&quot;,&quot;fill&quot;:true,&quot;backgroundColor&quot;:&quot;rgba(78, 78, 78, 0.3)&quot;,&quot;borderColor&quot;:&quot;rgba(78, 115, 223, 1)&quot;}]},&quot;options&quot;:{&quot;responsive&quot;:true,&quot;maintainAspectRatio&quot;:false,&quot;legend&quot;:{&quot;display&quot;:false},&quot;title&quot;:{},&quot;scales&quot;:{&quot;xAxes&quot;:[{&quot;gridLines&quot;:{&quot;color&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;zeroLineColor&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;drawBorder&quot;:false,&quot;drawTicks&quot;:false,&quot;borderDash&quot;:[&quot;2&quot;],&quot;zeroLineBorderDash&quot;:[&quot;2&quot;],&quot;drawOnChartArea&quot;:false},&quot;ticks&quot;:{&quot;fontColor&quot;:&quot;#858796&quot;,&quot;padding&quot;:20}}],&quot;yAxes&quot;:[{&quot;gridLines&quot;:{&quot;color&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;zeroLineColor&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;drawBorder&quot;:false,&quot;drawTicks&quot;:false,&quot;borderDash&quot;:[&quot;2&quot;],&quot;zeroLineBorderDash&quot;:[&quot;2&quot;]},&quot;ticks&quot;:{&quot;fontColor&quot;:&quot;#858796&quot;,&quot;padding&quot;:20}}]}}}"></canvas></div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row "> 
			<div class="col">
				<div class="card shadow mb-4">
					<div class="card-header d-flex justify-content-between align-items-center">
					<h6 ></h6><!--needs to be kept as an element unless new element is added for css reasons-->
						<h6 class="text-primary font-weight-bold m-0"><span class="label" id="label_1">Purchase</span> Overview (By <span id="type_1">number</span>)</h6>
						<div class="dropdown no-arrow"><button class="btn btn-link btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false" type="button"><i class="fas fa-ellipsis-v "></i></button>
							<div class="dropdown-menu shadow dropdown-menu-right animated--fade-in"	role="menu">
								<p class="text-center dropdown-header">Action:</p>
								<a role="presentation" id="permonth_1"  class="dropdown-item permonth"     data-id="1" data-month='[<?php echo $graphs->getmonth();?>]' data-monthvalue='[<?php echo $graphs->getmonthvalue('purchase');?>]' style="display:none"> Get only past months value</a>
								<a role="presentation" id="fullmonths_1" class="dropdown-item fullmonths"  data-id="1" data-month='[<?php echo $graphs->getfullmonth();?>]' data-monthvalue='[<?php echo $graphs->getfullmonthvalue('purchase');?>]'> Get all month data</a>
								<a role="presentation" id="refresh_1" class="dropdown-item refresh"        data-id="1" data-url="<?php echo BASE_URL?>eventhandler.php" > Refresh</a>
								<div class="dropdown-divider"></div>
								<a role="presentation" id="bargraph_1" class="dropdown-item bargraph" 	 data-id="1" data-type="permonth" > Show bar graphs</a></div>
						</div>
					</div>                                                                                                                                                                     
					<div class="card-body">
						<div class="chart-area" ><canvas id="graph_canvas_1" data-bs-chart="{&quot;type&quot;:&quot;line&quot;,&quot;data&quot;:{&quot;labels&quot;:[<?php echo $graphs->getmonth()?>],&quot;datasets&quot;:[{&quot;data&quot;:[<?php echo $graphs->getmonthvalue('purchase')?>],&quot;label&quot;:&quot;Sales&quot;,&quot;fill&quot;:true,&quot;backgroundColor&quot;:&quot;rgba(78, 78, 78, 0.3)&quot;,&quot;borderColor&quot;:&quot;rgba(78, 115, 223, 1)&quot;}]},&quot;options&quot;:{&quot;responsive&quot;:true,&quot;maintainAspectRatio&quot;:false,&quot;legend&quot;:{&quot;display&quot;:false},&quot;title&quot;:{},&quot;scales&quot;:{&quot;xAxes&quot;:[{&quot;gridLines&quot;:{&quot;color&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;zeroLineColor&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;drawBorder&quot;:false,&quot;drawTicks&quot;:false,&quot;borderDash&quot;:[&quot;2&quot;],&quot;zeroLineBorderDash&quot;:[&quot;2&quot;],&quot;drawOnChartArea&quot;:false},&quot;ticks&quot;:{&quot;fontColor&quot;:&quot;#858796&quot;,&quot;padding&quot;:20}}],&quot;yAxes&quot;:[{&quot;gridLines&quot;:{&quot;color&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;zeroLineColor&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;drawBorder&quot;:false,&quot;drawTicks&quot;:false,&quot;borderDash&quot;:[&quot;2&quot;],&quot;zeroLineBorderDash&quot;:[&quot;2&quot;]},&quot;ticks&quot;:{&quot;fontColor&quot;:&quot;#858796&quot;,&quot;padding&quot;:20}}]}}}"></canvas></div>
					</div>
				</div>
			</div>
		</div>
		<div class="row "> 
			<div class="col">
				<div class="card shadow mb-4">
					<div class="card-header d-flex justify-content-between align-items-center">
					<h6 ></h6><!--needs to be kept as an element unless new element is added for css reasons-->
						<h6 class="text-primary font-weight-bold m-0"><span class="label" id="label_2">Sales</span> Overview (By <span id="type_2">revenue</span>)</h6>
						<div class="dropdown no-arrow"><button class="btn btn-link btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false" type="button"><i class="fas fa-ellipsis-v "></i></button>
							<div class="dropdown-menu shadow dropdown-menu-right animated--fade-in"	role="menu">
								<p class="text-center dropdown-header">Action:</p>
								<a role="presentation" id="permonth_2"  class="dropdown-item permonth"     data-id="2" data-month='[<?php echo $graphs->getmonth();?>]' data-monthvalue='[<?php echo $graphs->getmonthvalue('sales','revenue');?>]' style="display:none"> Get only past months value</a>
								<a role="presentation" id="fullmonths_2" class="dropdown-item fullmonths"  data-id="2" data-month='[<?php echo $graphs->getfullmonth();?>]' data-monthvalue='[<?php echo $graphs->getfullmonthvalue('sales','revenue');?>]'> Get all month data</a>
								<a role="presentation" id="refresh_2" class="dropdown-item refresh"        data-id="2" data-url="<?php echo BASE_URL?>eventhandler.php" > Refresh</a>
								<div class="dropdown-divider"></div>
								<a role="presentation" id="bargraph_2" class="dropdown-item bargraph" 	 data-id="2" data-type="permonth" > Show bar graphs</a></div>
						</div>
					</div>                                                                                                                                                                     
					<div class="card-body">
						<div class="chart-area" ><canvas id="graph_canvas_2" data-bs-chart="{&quot;type&quot;:&quot;line&quot;,&quot;data&quot;:{&quot;labels&quot;:[<?php echo $graphs->getmonth()?>],&quot;datasets&quot;:[{&quot;data&quot;:[<?php echo $graphs->getmonthvalue('sales','revenue')?>],&quot;label&quot;:&quot;Sales&quot;,&quot;fill&quot;:true,&quot;backgroundColor&quot;:&quot;rgba(78, 78, 78, 0.3)&quot;,&quot;borderColor&quot;:&quot;rgba(78, 115, 223, 1)&quot;}]},&quot;options&quot;:{&quot;responsive&quot;:true,&quot;maintainAspectRatio&quot;:false,&quot;legend&quot;:{&quot;display&quot;:false},&quot;title&quot;:{},&quot;scales&quot;:{&quot;xAxes&quot;:[{&quot;gridLines&quot;:{&quot;color&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;zeroLineColor&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;drawBorder&quot;:false,&quot;drawTicks&quot;:false,&quot;borderDash&quot;:[&quot;2&quot;],&quot;zeroLineBorderDash&quot;:[&quot;2&quot;],&quot;drawOnChartArea&quot;:false},&quot;ticks&quot;:{&quot;fontColor&quot;:&quot;#858796&quot;,&quot;padding&quot;:20}}],&quot;yAxes&quot;:[{&quot;gridLines&quot;:{&quot;color&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;zeroLineColor&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;drawBorder&quot;:false,&quot;drawTicks&quot;:false,&quot;borderDash&quot;:[&quot;2&quot;],&quot;zeroLineBorderDash&quot;:[&quot;2&quot;]},&quot;ticks&quot;:{&quot;fontColor&quot;:&quot;#858796&quot;,&quot;padding&quot;:20}}]}}}"></canvas></div>
					</div>
				</div>
			</div>
		</div>
		<div class="row "> 
			<div class="col">
				<div class="card shadow mb-4">
					<div class="card-header d-flex justify-content-between align-items-center">
					<h6 class="test"></h6><!--needs to be kept as an element unless new element is added for css reasons-->
						<h6 class="text-primary font-weight-bold m-0"><span class="label" id="label_3">Purchase</span> Overview (By <span id="type_3">revenue</span>)</h6>
						<div class="dropdown no-arrow"><button class="btn btn-link btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false" type="button"><i class="fas fa-ellipsis-v "></i></button>
							<div class="dropdown-menu shadow dropdown-menu-right animated--fade-in"	role="menu">
								<p class="text-center dropdown-header">Action:</p>
								<a role="presentation" id="permonth_3"  class="dropdown-item permonth"     data-id="3" data-month='[<?php echo $graphs->getmonth();?>]' data-monthvalue='[<?php echo $graphs->getmonthvalue('purchase','revenue');?>]' style="display:none"> Get only past months value</a>
								<a role="presentation" id="fullmonths_3" class="dropdown-item fullmonths"  data-id="3" data-month='[<?php echo $graphs->getfullmonth();?>]' data-monthvalue='[<?php echo $graphs->getfullmonthvalue('purchase','revenue');?>]'> Get all month data</a>
								<a role="presentation" id="refresh_3" class="dropdown-item refresh"        data-id="3" data-url="<?php echo BASE_URL?>eventhandler.php" > Refresh</a>
								<div class="dropdown-divider"></div>
								<a role="presentation" id="bargraph_3" class="dropdown-item bargraph" 	 data-id="3" data-type="permonth" > Show bar graphs</a></div>
						</div>
					</div>                                                                                                                                                                     
					<div class="card-body">
						<div class="chart-area" ><canvas id="graph_canvas_3" data-bs-chart="{&quot;type&quot;:&quot;line&quot;,&quot;data&quot;:{&quot;labels&quot;:[<?php echo $graphs->getmonth()?>],&quot;datasets&quot;:[{&quot;data&quot;:[<?php echo $graphs->getmonthvalue('purchase','revenue')?>],&quot;label&quot;:&quot;Sales&quot;,&quot;fill&quot;:true,&quot;backgroundColor&quot;:&quot;rgba(78, 78, 78, 0.3)&quot;,&quot;borderColor&quot;:&quot;rgba(78, 115, 223, 1)&quot;}]},&quot;options&quot;:{&quot;responsive&quot;:true,&quot;maintainAspectRatio&quot;:false,&quot;legend&quot;:{&quot;display&quot;:false},&quot;title&quot;:{},&quot;scales&quot;:{&quot;xAxes&quot;:[{&quot;gridLines&quot;:{&quot;color&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;zeroLineColor&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;drawBorder&quot;:false,&quot;drawTicks&quot;:false,&quot;borderDash&quot;:[&quot;2&quot;],&quot;zeroLineBorderDash&quot;:[&quot;2&quot;],&quot;drawOnChartArea&quot;:false},&quot;ticks&quot;:{&quot;fontColor&quot;:&quot;#858796&quot;,&quot;padding&quot;:20}}],&quot;yAxes&quot;:[{&quot;gridLines&quot;:{&quot;color&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;zeroLineColor&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;drawBorder&quot;:false,&quot;drawTicks&quot;:false,&quot;borderDash&quot;:[&quot;2&quot;],&quot;zeroLineBorderDash&quot;:[&quot;2&quot;]},&quot;ticks&quot;:{&quot;fontColor&quot;:&quot;#858796&quot;,&quot;padding&quot;:20}}]}}}"></canvas></div>
					</div>
				</div>
			</div>
		</div>
        <div class="row "> 
			<div class="col">
				<div class="card shadow mb-4">
					<div class="card-header d-flex justify-content-between align-items-center">
					<h6 ><br>
					
				</h6><!--needs to be kept as an element unless new element is added for css reasons-->
						<h6 class="text-primary font-weight-bold m-0"><span class="label" id="label_4">Product</span> Overview (By <span id="type_4">number</span>)</h6>
						<div class="dropdown no-arrow"><button class="btn btn-link btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false" type="button"><i class="fas fa-ellipsis-v "></i></button>
							<div class="dropdown-menu shadow dropdown-menu-right animated--fade-in"	role="menu">
								<p class="text-center dropdown-header">Action:</p>
								<a role="presentation" id="permonth_4"  class="dropdown-item permonth"     data-id="4" data-month='[<?php echo $graphs->category();?>]' data-monthvalue='[<?php echo $graphs->categoryvalue();?>]' style="display:none"> Show only available product</a>
								<a role="presentation" id="fullmonths_4" class="dropdown-item fullmonths"  data-id="4" data-month='[<?php echo $graphs->category();?>]' data-monthvalue='[<?php echo $graphs->categoryvalue();?>]'> Show all product</a>
								<a role="presentation" id="refresh_4" class="dropdown-item refresh"        data-id="4" data-url="<?php echo BASE_URL?>eventhandler.php" > Refresh</a>
								<div class="dropdown-divider"></div>
								<a role="presentation" id="bargraph_4" class="dropdown-item bargraph" 	 data-id="4" data-type="permonth" > Show bar graphs</a></div>
						</div>
					</div>                                                                                                                                                                     
					<div class="card-body">
						<div class="chart-area" ><canvas id="graph_canvas_4" data-bs-chart="{&quot;type&quot;:&quot;bar&quot;,&quot;data&quot;:{&quot;labels&quot;:[<?php echo $graphs->category()?>],&quot;datasets&quot;:[{&quot;data&quot;:[<?php echo $graphs->categoryvalue()?>],&quot;label&quot;:&quot;Product&quot;,&quot;fill&quot;:true,&quot;backgroundColor&quot;:&quot;rgba(78, 78, 78, 0.3)&quot;,&quot;borderColor&quot;:&quot;rgba(78, 115, 223, 1)&quot;}]},&quot;options&quot;:{&quot;responsive&quot;:true,&quot;maintainAspectRatio&quot;:false,&quot;legend&quot;:{&quot;display&quot;:false},&quot;title&quot;:{},&quot;scales&quot;:{&quot;xAxes&quot;:[{&quot;gridLines&quot;:{&quot;color&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;zeroLineColor&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;drawBorder&quot;:false,&quot;drawTicks&quot;:false,&quot;borderDash&quot;:[&quot;2&quot;],&quot;zeroLineBorderDash&quot;:[&quot;2&quot;],&quot;drawOnChartArea&quot;:false},&quot;ticks&quot;:{&quot;fontColor&quot;:&quot;#858796&quot;,&quot;padding&quot;:20}}],&quot;yAxes&quot;:[{&quot;gridLines&quot;:{&quot;color&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;zeroLineColor&quot;:&quot;rgb(234, 236, 244)&quot;,&quot;drawBorder&quot;:false,&quot;drawTicks&quot;:false,&quot;borderDash&quot;:[&quot;2&quot;],&quot;zeroLineBorderDash&quot;:[&quot;2&quot;]},&quot;ticks&quot;:{&quot;fontColor&quot;:&quot;#858796&quot;,&quot;padding&quot;:20}}]}}}"></canvas></div>
					</div>
				</div>
			</div>
		</div>
	
		<footer class="bg-white sticky-footer mt-3">
			<div class="container my-auto">		
				<div class="text-center my-auto copyright">
					<span>Copyright © <?php echo $graphs->website_name()?> 
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
  
});






</script>