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
                	<div class="row">
                    	<div class="col-6 col-sm-4">
                            <h3 class="card-title">Purchase List</h3>
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
	            				<button type="submit" name="filter" id="filter" title="Get Report"class="btn btn-info btn-sm"><i class="fas fa-filter"></i></button>
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
                	<table id="purchase_data" class="table table-bordered table-striped">
                		<thead>
							<tr>
								<th>Purchase ID</th>
								<th>Supplier Name</th>
								<th>Total Amount (<?php	echo $ims->website_currency();	?>)</th>
								<th>Payment Method</th>
								<th>Purchase Status</th>
								<th>Purchase Date</th>
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

    <div id="purchaseModal" class="modal fade">
    	<div class="modal-dialog">
    		<form method="post" id="purchase_form" action="<?php echo SERVER_URL?>purchase_action.php">
    			<div class="modal-content">
    				<div class="modal-header">
					<h4 class="modal-title"><i class="fa fa-plus"></i> Create Purchase order</h4>
    					<button type="button" class="close" data-dismiss="modal">&times;</button>						
    				</div>
    				<div class="modal-body">
    					<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Enter Supplier Name</label>	
									<input type="text" name="inventory_purchase_name" id="inventory_purchase_name" list="inventory_purchase_name_list"   autocomplete="off" class="form-control" required />
									<datalist id="inventory_purchase_name_list">
									<?php echo $command->fill_supplier_list()?>
									</datalist>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Date</label>
									<input type="date" name="inventory_purchase_date" id="inventory_purchase_date" class="form-control" autocomplete="off" required />
								</div>
							</div>
						</div>
						<div class="form-group">
							<label>Enter Supplier Address</label>
							<textarea name="inventory_purchase_address" id="inventory_purchase_address" class="form-control"></textarea>
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
    					<input type="hidden" name="inventory_purchase_id" id="inventory_purchase_id" />
    					<input type="hidden" name="btn_action" id="btn_action" />
						<input type="submit" name="action" id="action" class="btn btn-success" value="Add" />
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    				</div>
    			</div>
    		</form>
    	</div>
    </div>		
	<script type="text/javascript">
    $(document).ready(function(){        
		var url=$('#purchase_form').attr('action');
		var product_list;		
			$.ajax({
			'type': "POST",
			'global': false,
			'dataType': 'json',
			'url': "eventhandler.php",
			'data': { 
						get_product_list:1,
						status:'active'
					},
			'success': function(data){
				callback(data);
			},
			});
			function callback(response) {
				product_list = response;								
			}		
		
    	var datatable = $('#purchase_data').DataTable({
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
			$('#purchaseModal').modal('show');
			$('#purchase_form')[0].reset();
			$('.modal-title').html("<i class='fa fa-plus'></i> Create purchase");
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
			html += '<select name="product_id[]" id="product_id'+count+'" class="form-control" data-live-search="true" required>';
			html += product_list;
			html += '</select>';
			html += '</div>';
			html += '<div class="col-md-3 px-0">';
			html += '<input type="text" name="quantity[]" class="form-control" required />';
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
		
		$(document).on('submit', '#purchase_form', function(event){
			event.preventDefault();
			$('#action').attr('disabled', 'disabled');
			var form_data = $(this).serialize();
			$.ajax({
				url:url,
				method:"POST",
				data:form_data,
				dataType:"json",
				error:function(){
					$('#action').attr('disabled', false);
				},
				complete:function(){
					$('#action').attr('disabled', false);
				},
				success:function(data){
					$('#purchase_form')[0].reset();
					$('#purchaseModal').modal('hide');
					showMessage(datatable,data)
				}
			});
		});

		$(document).on('click', '.update', function(){
			var inventory_purchase_id = $(this).attr("id");			
			var btn_action = 'fetch_single';
			$.ajax({
				url:url,
				method:"POST",
				data:{inventory_purchase_id:inventory_purchase_id, btn_action:btn_action},
				dataType:"json",
				success:function(data)
				{
					$('#purchaseModal').modal('show');
					$('#inventory_purchase_name').val(data.inventory_purchase_name);
					$('#inventory_purchase_date').val(data.inventory_purchase_date);
					$('#inventory_purchase_address').val(data.inventory_purchase_address);
					$('#span_product_details').html(data.product_details);
					$('#payment_status').val(data.payment_status);
					$('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit purchase");
					$('#inventory_purchase_id').val(inventory_purchase_id);
					$('#action').val('Edit');
					$('#btn_action').val('Edit');
				}
			})
		});
	
		$(document).on('click', '.delete', function(){
			var inventory_purchase_id = $(this).attr("id");
			var status = $(this).data("status");
			var btn_action = "delete";	
			var data={inventory_purchase_id:inventory_purchase_id, status:status, btn_action:btn_action};
			disable(url,datatable,data,'change the status');    
  	});



	  $('#reportdata').on('submit', function(event){
		event.preventDefault();
		var data  = new FormData(this);
		data.append('table','purchase')
		$.ajax({
			url:'server/getOrderReport.php',
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
	  function printOrder(orderId ) {
	$.ajax({
		url: printurl,
		method:"POST",
		data: {orderId: orderId,table:'purchase'},
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
<?php include_once(INC.'footer.php');?>