<?php
include_once('config.php');
include_once(INC.'init.php');

if(!$ims->is_login())
    header("location:".$ims->login);

if(!$ims->is_admin())
    header("location:".$ims->dashboard);

include_once(INC.'header.php');
?>
                    <span class="position-absolute text-center w-100"id="message" style="z-index:10;"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col">
                            		<h3 class="card-title">Tax Management</h3>
                            	</div>
                            	<div class="col text-right">
                            		<button type="button" name="add_tax" id="add_tax" class="btn btn-success btn-circle btn-sm"><i class="fas fa-plus"></i> Add</button>
                            	</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered tax_table" id="tax_table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Tax Name</th>
                                            <th>Tax Percentage</th>
                                            <th>Status</th>
                                            <th class="action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

					

<div id="taxModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="tax_form" action="<?php echo SERVER_URL?>tax_action.php">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Add Data</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
		          	<div class="form-group">
		          		<label>Tax Name</label>
		          		<input type="text" name="tax_name" id="tax_name" class="form-control" required data-parsley-pattern="/^[a-zA-Z0-9 \s]+$/" data-parsley-trigger="keyup" />
		          	</div>
                    <div class="form-group">
                        <label>Tax Percentage</label>
                        <input type="text" name="tax_percentage" id="tax_percentage" class="form-control" required data-parsley-pattern="^[0-9]{1,2}\.[0-9]{2}$" data-parsley-trigger="keyup" />
                    </div>
        		</div>
        		<div class="modal-footer">
          			<input type="hidden" name="hidden_id" id="hidden_id" />
          			<input type="hidden" name="action" id="action" value="Add" />
          			<input type="submit" name="submit" id="submit_button" class="btn btn-success" value="Add" />
          			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        		</div>
      		</div>
    	</form>
  	</div>
</div>

<?php include_once(INC.'footer.php');?>
<script>
$(document).ready(function(){
var url=$('#tax_form').attr('action');
	var datatable = $('.tax_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:url,
			type:"POST",
			data:{action:'fetch'}
		},
		"columnDefs":[
			{
				"targets":  [ 'created','action'],
				"orderable":false,
			},
		],
	});

	$('#add_tax').click(function(){		
		$('#tax_form')[0].reset();
		$('#tax_form').parsley().reset();
    	$('#modal_title').text('Add Data');
    	$('#action').val('Add');
    	$('#submit_button').val('Add');
    	$('#taxModal').modal('show');    	
		$('#submit_button').attr('disabled', false);
	});

	$('#tax_form').parsley();

	$('#tax_form').on('submit', function(event){
		event.preventDefault();
		if($('#tax_form').parsley().isValid())
		{		
			$.ajax({
				url:url,
				method:"POST",
				data:$(this).serialize(),
				dataType:'json',
				beforeSend:function()
				{
					$('#submit_button').attr('disabled', 'disabled');
					$('#submit_button').val('wait...');
				},
				success:function(data)
				{
					$('#submit_button').attr('disabled', false);
					if(data.error != ''){
						$('#form_message').html(data.error);
						$('#submit_button').val('Add');						
					}
					else{
						$('#taxModal').modal('hide');						
						showMessage(datatable,data.success);						
					}				
				}
			})
		}
	});

	$(document).on('click', '.edit_button', function(){
		var tax_id = $(this).data('id');
		$('#tax_form').parsley().reset();		
		$.ajax({
	      	url:url,
	      	method:"POST",
	      	data:{tax_id:tax_id, action:'fetch_single'},
	      	dataType:'JSON',
	      	success:function(data)
	      	{
	        	$('#tax_name').val(data.tax_name);
                $('#tax_percentage').val(data.tax_percentage);
	        	$('#modal_title').text('Edit Data');
	        	$('#action').val('Edit');
	        	$('#submit_button').val('Edit');
	        	$('#taxModal').modal('show');
	        	$('#hidden_id').val(tax_id);
				$('#submit_button').attr('disabled', false);
	      	}
	    })
	});

	$(document).on('click', '.status_button', function(){
			var id = $(this).data('id');
			var status = $(this).data('status');
			var next_status = 'active';
			if(status == 'active')
				next_status = 'inactive';			
			var data={id:id, action:'change_status', status:status, next_status:next_status};
			disable(url,datatable,data);			
		});

	$(document).on('click', '.delete_button', function(){		
    	var id = $(this).data('id');
		var data={id:id, action:'delete'};
		disable(url,datatable,data,'delete the data');    
  	});

	


});
</script>