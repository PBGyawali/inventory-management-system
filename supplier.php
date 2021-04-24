<?php
//supplier.php

include_once('config.php');
include_once(INC.'init.php');

if(!$ims->is_login())
    header("location:".$ims->login);

if(!$ims->is_admin())
    header("location:".$ims->dashboard);

include_once(INC.'header.php');
?>
		<span class="position-absolute text-center w-100"id="message" style="z-index:10;"></span>
		<div class="row">
			<div class="col-lg-12">
				<div class="card card-secondary">
                    <div class="card-header">
                    	<div class="row">
                        	<div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            	<h3 class="card-title">Supplier List</h3>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6 text-right">
                            	<button type="button" name="add" id="add_button" data-toggle="modal" data-target="#supplierModal" class="btn btn-success btn-sm"><i class="fas fa-user-plus"></i> Add</button>
                        	</div>
                        </div>
                       
                        <div class="clear:both"></div>
                   	</div>
                   	<div class="card-body">
                   		<div class="row"><div class="col-sm-12 table-responsive">
                   			<table id="supplier_data" class="table table-bordered table-striped">
                   				<thead>
									<tr>
										<th>ID</th>
										<th>Supplier name</th>
										<th>Email</th>										
										<th>Status</th>
										<th class="action">Action</th>
									</tr>
								</thead>
                   			</table>
                   		</div>
                   	</div>
               	</div>
           	</div>
        </div>
        <div id="supplierModal" class="modal fade" data-backdrop="static">
        	<div class="modal-dialog">
        		<form method="post" id="supplier_form"  action="<?php echo SERVER_URL?>supplier_action.php">
        			<div class="modal-content">
        			<div class="modal-header">        			
						<h4 class="modal-title" id="modal_title">Add supplier</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        			</div>
        			<div class="modal-body">
        				<div class="form-group">
							<label>Enter Supplier Name</label>
							<input type="text" name="supplier_name" id="supplier_name" class="form-control" required />
						</div>
						<div class="form-group">
							<label>Enter Supplier Email</label>
							<input type="email" name="supplier_email" id="supplier_email" class="form-control" required />
						</div>
						<div class="form-group">
							<label>Enter Supplier address</label>
							<input type="password" name="supplier_address" id="supplier_address" class="form-control" />
						</div>
        			</div>
        			<div class="modal-footer">
        				<input type="hidden" name="supplier_id" id="supplier_id" />
        				<input type="hidden" name="btn_action" id="btn_action" />
        				<input type="submit" name="action" id="action" class="btn btn-success" value="Add" />
        				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        			</div>
        		</div>
        		</form>

        	</div>
		</div>
	
        		
        		


<script>
$(document).ready(function(){
	var url=$('#supplier_form').attr('action');
	
	$('#add_button').click(function(){
		$('#supplier_form')[0].reset();
		$('.modal-title').html("<i class='fa fa-plus'></i> Add supplier");
		$('#action').val("Add");
		$('#btn_action').val("Add");
	});

	var datatable = $('#supplier_data').DataTable({
		"processing": true,
		"serverSide": true,
		"order": [],
		"ajax" : {
			url:url,
			type:"POST",
			dataType:'json',
			data:{action:'fetch'}
		},
		"columnDefs":[
			{
				"targets":  [ 'created','action'],
				"orderable":false
			}
		],
		"pageLength": 25
	});

	$(document).on('submit', '#supplier_form', function(event){
		event.preventDefault();
		$('#action').attr('disabled','disabled');
		var form_data = $(this).serialize();
		$.ajax({
			url:url,
			method:"POST",
			data:form_data,
			dataType:"json",
			complete:function()
			{				
				$('#supplierModal').modal('hide');				
				$('#action').attr('disabled', false);				
			},
			success:function(data)
			{
				$('#supplier_form')[0].reset();
				$('#supplierModal').modal('hide');
				$('#action').attr('disabled', false);
				showMessage(datatable,data);
			}
		})
	});

	$(document).on('click', '.update', function(){
		var supplier_id = $(this).attr("id");
		var btn_action = 'fetch_single';
		$.ajax({
			url:url,
			method:"POST",
			data:{supplier_id:supplier_id, btn_action:btn_action},
			dataType:"json",
			success:function(data)
			{
				$('#supplierModal').modal('show');
				$('#supplier_name').val(data.supplier_name);
				$('#supplier_email').val(data.supplier_email);
				$('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit supplier");
				$('#supplier_id').val(supplier_id);
				$('#action').val('Edit');
				$('#btn_action').val('Edit');
				$('#supplier_password').attr('required', false);
			}
		})
	});

	$(document).on('click', '.delete', function(){	
		var supplier_id = $(this).attr("id");
		var status = $(this).data('status');
		var change="disable";		
		if (status=='Inactive')			
			change="enable";		
		var btn_action = "disable";			
    	var id = $(this).data('id');
		var data={supplier_id:supplier_id, status:status, btn_action:btn_action};
		disable(url,datatable,data,change+' the data');    
  	});

});
</script>

<?php
include_once(INC."footer.php");
?>
