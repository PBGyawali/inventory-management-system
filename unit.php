<?php
include_once('config.php');
include_once(INC.'init.php');

if(!$ims->is_login())
    header("location:".$ims->login);

if(!$ims->is_admin())
    header("location:".$ims->dashboard);

include_once(INC.'header.php');
?>

		<span id="alert_action"></span>
		<div class="row">
			<div class="col-lg-12">
				<div class="card card-secondary">
                    <div class="card-header">
                    	<div class="row">
                        	<div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            	<h3 class="card-title">Unit List</h3>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6 text-right">
                            	<button type="button" name="add" id="add_button" data-toggle="modal" data-target="#unitModal" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Add</button>
                        	</div>
                        </div>
                       
                        <div class="clear:both"></div>
                   	</div>
                <div class="card-body">
                    <div class="row">
                    	<div class="col-sm-12 table-responsive">
                    		<table id="unit_data" class="table table-bordered table-striped">
                    			<thead><tr>
									<th>ID</th>
									<th>Unit Name</th>
									<th>Status</th>
									<th class="action">Action</th>
								</tr></thead>
                    		</table>
                    	</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  
    				
    		
	
	<div id="unitModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="unit_form"  action="<?php echo SERVER_URL?>unit_action.php">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title"><i class="fa fa-plus"></i>Add unit</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
		          	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-3 text-left px-0 pl-1">Enter unit</label>
			            	<div class="col-md-9 px-0 pr-1">
			            		<input type="text" name="unit_name" id="unit_name" class="form-control"  data-parsley-pattern="/^[a-zA-Z\s]+$/" data-parsley-trigger="keyup" />
			            	</div>
			            </div>
		          	</div>
		          	
        		<div class="modal-footer">
				<input type="hidden" name="unit_id" id="unit_id"/>
    					<input type="hidden" name="btn_action" id="btn_action"/>
    					<input type="submit" name="action" id="action" class="btn btn-success"value="Add" />
    					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>          			
        		</div>
      		</div>
    	</form>
  	</div>
</div>
<script>
$(document).ready(function(){
	var url=$('#unit_form').attr('action');
	

	$('#add_button').click(function(){
		$('#unit_form')[0].reset();
		$('.modal-title').html("<i class='fa fa-plus'></i> Add unit");
		$('#action').val('Add');
		$('#btn_action').val('Add');
	});

	$(document).on('submit','#unit_form', function(event){
		event.preventDefault();	
		$.ajax({
				url:url,
				method:"POST",
				data:$(this).serialize(),
				dataType:'json',
				beforeSend:function()
				{
					$('#action').attr('disabled', 'disabled');
					$('#action').val('wait...');
				},
				complete:function()
				{
					$('#action').attr('disabled', false);
					$('#action').val('Save');
				},
				success:function(data)
				{
					if(data.error != ''){
						$('#form_message').html(data.error);												
					}
					else{
						$('#unit_form')[0].reset();
						$('#unitModal').modal('hide');
						$('#alert_action').fadeIn().html(data);
						unitdataTable.ajax.reload();						
					}
					timeout();
				}
			})

	});

	$(document).on('click', '.update', function(){
		var unit_id = $(this).attr("id");
		var btn_action = 'fetch_single';
		$.ajax({
			url:url,
			method:"POST",
			data:{unit_id:unit_id, btn_action:btn_action},
			dataType:"json",
			success:function(data)
			{
				$('#unitModal').modal('show');
				$('#unit_name').val(data);
				$('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit unit");
				$('#unit_id').val(unit_id);
				$('#action').val('Edit');
				$('#btn_action').val("Edit");
			}
		})
	});

	var unitdataTable = $('#unit_data').DataTable({
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
		"pageLength": 25
	});
	
	$(document).on('click', '.delete', function(){
		var unit_id = $(this).attr('id');
		var status = $(this).data("status");
		var btn_action = 'delete';		
		var data={unit_id:unit_id, status:status, btn_action:btn_action};
		disable(url,unitdataTable,data,'change the status');    
  	});



});
</script>

<?php
include_once(INC.'footer.php');
?>


				