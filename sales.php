<?php
include_once('config.php');
include_once(INC.'init.php');

if(!$ims->is_login())
    header("location:".$ims->login);

include_once(INC.'header.php');
?>
	<span class="position-absolute w-100 text-center" id="message"style="z-index:10"></span>	
	<div class="row">
		<div class="col-lg-12">
			
			<div class="card card-secondary">
                <div class="card-header"> 
                	<div class="row d-flex">
                    	<div class="col-6 col-sm-4 ">
                            <h3 class="card-title">Sales List</h3>
						</div>
						<div class="col-sm-4">
							<form action="" method="post" id="reportdata">
	            				<div class="row input-daterange">
	            					<div class="col-md-6">
		            					<input type="date" name="startDate" id="startDate" class="form-control form-control-sm"  />
		            				</div>
		            				<div class="col-md-6">
		            					<input type="date" name="endDate" id="endDate" class="form-control form-control-sm"   />
		            				</div>
		            			</div>
							</div>
							<div class="col-md-2">
	            				<button type="submit" name="filter" id="filter"  title="Get Report"class="btn btn-info btn-sm"><i class="fas fa-filter"></i></button>
	            				&nbsp;
	            				<button type="button" name="refresh" id="refresh" class="btn btn-secondary btn-sm"><i class="fas fa-sync-alt"></i></button>
	            			</div>
							</form>
                        <div class="col-4 col-sm-2 text-right" >
                            <button type="button" name="add" id="add_button" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Add</button>    	
                        </div>
                    </div>
                </div>
                <div class="card-body">
                	<table id="sales_data" class="table table-bordered table-striped ">
                		<thead>
							<tr>
								<th>Sales ID</th>
								<th>Customer Name</th>
								<th>Total Amount (<?php	echo $ims->website_currency();	?>)</th>
								<th>Payment Method</th>
								<th>Sales Status</th>
								<th>Sales Date</th>
								<?php if($ims->is_admin()):?>								
								<th class="created">Created By</th>						
								<?php endif?>
								<th class="action">Action</th>
							</tr>
						</thead>
                	</table>
                </div>
            </div>
        </div>
    </div>
	
    <div id="salesModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">    
    		<form method="post" id="sales_form" data-url="<?php echo SERVER_URL?>printOrder.php" action="<?php echo SERVER_URL?>sales_action.php">
    			<div class="modal-content">
    				<div class="modal-header">
					<h4 class="modal-title"><i class="fa fa-plus"></i> Create Sales Order</h4>
    					<button type="button" class="close" data-dismiss="modal">&times;</button>						
    				</div>
    				<div class="modal-body">
    					<div class="row">
							<div class="col-6">
								<div class="form-group">
									<label>Enter Receiver Name</label>
									<input type="text" name="inventory_sales_name" id="inventory_sales_name" class="form-control" autocomplete="off" required />
								</div>
							</div>
							<div class="col-6">
								<div class="form-group">
									<label>Date</label>
									<input type="date" name="inventory_sales_date" id="inventory_sales_date" class="form-control datepicker" autocomplete="off" required />
								</div>
							</div>
						</div>
						<div class="form-group">
							<label>Enter Receiver Address</label>
							<textarea name="inventory_sales_address" id="inventory_sales_address" class="form-control" required></textarea>
						</div>
						<div class="form-group">
							<label>Enter Product Details</label>							
							<span id="span_product_details"></span>							
						</div>
						<div class="form-group">
							<label>Select Payment Method</label>
							<select name="payment_status" id="payment_status" class="form-control">
								<option value="cash">Cash</option>
								<option value="credit">Credit</option>
							</select>
						</div>
    				</div>
    				<div class="modal-footer">
    					<input type="hidden" name="inventory_sales_id" id="inventory_sales_id" />
    					<input type="hidden" name="btn_action" id="btn_action" />
						<input type="submit" name="action" id="action" class="btn btn-success" value="Add" />
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    				</div>
    			</div>
    		</form>
    	</div>
    </div><?php include_once(INC.'footer.php');?>
	<script type="text/javascript">
    $(document).ready(function(){	
		var url=$('#sales_form').attr('action');
		var printurl=$('#sales_form').data('url');
		var reporturl='server/getOrderReport.php';
		var fetchurl="eventhandler.php";
		var product_list;		
			$.ajax({
			'type': "POST",
			'global': false,
			'dataType': 'json',
			'url': fetchurl,
			'data': { get_product_list:1},
			'success': function(data){
				callback(data);
			},
			});
			function callback(response) {
				product_list = response;								
			}
		
		
    	var datatable = $('#sales_data').DataTable({
			"processing":true,
			"serverSide":true,			
			"order":[],
			"ajax" : {
			url:url,
			type:"POST",
			dataType:'json',
			data:{action:'fetch'}
			},		
			"columnDefs":[
				{
					"targets":  [ 'created','action'],					
					"orderable":false,
				},
			],		
			"pageLength": 10
		});

		$('#add_button').click(function(){
			$('#salesModal').modal('show');
			$('#sales_form')[0].reset();
			$('.modal-title').html("<i class='fa fa-plus'></i> Create Sales Order");
			$('#action').val('Add');
			$('#btn_action').val('Add');
			$('#span_product_details').html('');
			add_product_row();
			
		});

		function add_product_row(count = '')
		{
			var html = '';
			html += '<span class="product_details" id="row'+count+'">';
			html += '<div class="row" id="product_details_row'+count+'">';
			html += '<div class="col-md-8">';
			html += '<select name="product_id[]" id="product_id'+count+'" class="form-control selectpicker" data-live-search="true" required>';
			html += product_list;
			html += '</select>';
			html += '</div>';
			html += '<div class="col-md-3 px-0">';
			html += '<input type="number" name="quantity[]" id="quantity'+count+'"  min="1" class="form-control" required />';
			html += '</div>';
			html += '<div class="col-md-1 pl-0">';
			if(count == '')			
				html += '<button type="button" name="add_more" id="add_more" class="btn btn-success">+</button>';			
			else			
				html += '<button type="button" name="remove" id="'+count+'" class="btn btn-danger remove">-</button>';			
			html += '</div>';
			html += '</div></div></span>';
			$('#span_product_details').append(html);
		}

		var count = 0;
		$(document).on('click', '#add_more', function(){
			count = $('.product_details').length;
			count = count + 1;
			add_product_row(count);
		});
		$(document).on('click', '.remove', function(){
			var row_no = $(this).attr("id");
			$('#product_details_row'+row_no).remove();
			$('#row'+row_no).hide();
		});

		$(document).on('change', '.selectpicker', function(){
			select=$(this);
			var product_id=select.val();
			$quantityform=select.parent().siblings().find("input[type=number]");
			//var max_value=$quantityform.attr('max');				
			$.ajax({
				url:fetchurl,
				method:"POST",
				data:{max_available_quantity:1,product_id:product_id},
				dataType:"json",
				success:function(data){
					$quantityform.attr('max',data);
				}
			});
			
		});
		$(document).on('submit', '#sales_form', function(event){
			event.preventDefault();
			$('#action').attr('disabled', 'disabled');
			var form_data = $(this).serialize();
			$.ajax({
				url:url,
				method:"POST",
				data:form_data,
				dataType:"json",
				complete:function(){
					$('#action').attr('disabled', false);
				},
				success:function(data){
					$('#sales_form')[0].reset();
					$('#salesModal').modal('hide');					
					$('#action').attr('disabled', false);
					showMessage(datatable,data);
				}
			});
		});

		$(document).on('click', '.update', function(){
			var inventory_sales_id = $(this).attr("id");			
			var btn_action = 'fetch_single';
			$.ajax({
				url:url,
				method:"POST",
				data:{inventory_sales_id:inventory_sales_id, btn_action:btn_action},
				dataType:"json",
				success:function(data)
				{
					$('#salesModal').modal('show');
					$('#inventory_sales_name').val(data.inventory_sales_name);
					$('#inventory_sales_date').val(data.inventory_sales_date);
					$('#inventory_sales_address').val(data.inventory_sales_address);
					$('#span_product_details').html(data.product_details);
					$('#payment_status').val(data.payment_status);
					$('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Sales Order");
					$('#inventory_sales_id').val(inventory_sales_id);
					$('#action').val('Edit');
					$('#btn_action').val('Edit');
				}
			})
		});
		
		$(document).on('click', '.delete', function(){
			var inventory_sales_id = $(this).attr("id");
			var status = $(this).data("status");
			var data={inventory_sales_id:inventory_sales_id, status:status,"btn_action":"delete"};
			disable(url,datatable,data);			
		});

	  $('#reportdata').on('submit', function(event){
		event.preventDefault();
		var data  = new FormData(this);
		data.append('table','sales')
		$.ajax({
			url:reporturl,
			method:"POST",
			data:data,
			dataType:'text',
			contentType:false,
			processData:false,
			success:function(data)
			{				
				printReport(data )
			}
		})
	});
	  function printReport(response) {
	var mywindow = window.open('', '', 'height=400,width=600');	       
	mywindow.document.write('</head><body>');
	mywindow.document.write(response);
	mywindow.document.write('</body></html>');
	mywindow.document.close(); // necessary for IE >= 10
	mywindow.focus(); // necessary for IE >= 10
	mywindow.resizeTo(screen.width, screen.height);
		}// /success function
	


  	$('#refresh').click(function(){
  		$('#startDate').val('');
  		$('#endDate').val('');  		
  		
  	})

		$(document).on('click', '.view', function(){
			event.preventDefault();
			var inventory_sales_id = $(this).attr("id");			
			printOrder(inventory_sales_id )	;		
		});
		
		
		function printOrder(orderId ) {
	$.ajax({
		url: printurl,
		method:"POST",
		data: {orderId: orderId,table:'sales'},
		dataType: 'text',
		success:function(response) {
	var mywindow = window.open('', '', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Order Invoice</title>');        
	mywindow.document.write('</head><body>');
	mywindow.document.write(response);
	mywindow.document.write('</body></html>');
	mywindow.document.close(); // necessary for IE >= 10
	mywindow.focus(); // necessary for IE >= 10
	mywindow.resizeTo(screen.width, screen.height);
		}// /success function
	}); // /ajax function to fetch the printable order
}
});
</script>
