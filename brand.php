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
                		<div class="col-md-10">
                			<h3 class="card-title">Brand List</h3>
                		</div>
                		<div class="col-md-2 text-right">
                			<button type="button" name="add" id="add_button" class="btn btn-success"><i class="fa fa-plus"></i> Add</button>
                		</div>
                	</div>
                </div>
                <div class="card-body">
                	<table id="brand_data" class="table table-bordered table-striped">
                		<thead>
							<tr>
								<th>ID</th>
								<th>Category</th>
								<th>Brand Name</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
                	</table>
                </div>
            </div>
        </div>
    </div>

    <div id="brandModal" class="modal fade">
    	<div class="modal-dialog">
    		<form method="post" id="brand_form" action="<?php echo SERVER_URL?>brand_action.php">
    			<div class="modal-content">
    				<div class="modal-header">    					
						<h4 class="modal-title"><i class="fa fa-plus"></i> Add Brand</h4>
						<button type="button" class="close" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-loading d-none m-auto py-5 text-center"> 
						<i class="fa fa-spinner fa-pulse fa-5x text-secondary"></i><br> Loading...
						<span class="sr-only">Loading...</span>
					</div>
    				<div class="modal-body ">
					<span id="form_message"></span>
					
    					<div class="form-group">
						<label>Category</label>
    						<select name="category_id" id="category_id" class="form-control" required>								
								<?php echo $command->fill_category_list(); ?>
							</select>
    					</div>
    					<div class="form-group">
							<label>Enter Brand Name</label>
							<input type="text" name="brand_name" id="brand_name" class="form-control" required />
						</div>
    				</div>
    				<div class="modal-footer">
    					<input type="hidden" name="brand_id" id="brand_id" />
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
	var url=$('#brand_form').attr('action');	

	$('#add_button').click(function(){
		$('#brandModal').modal('show');
		$('#brand_form')[0].reset();
		$('.modal-title').html("<i class='fa fa-plus'></i> Add Brand");
		$('#action').val('Add');
		$('#btn_action').val('Add');
	});

	$(document).on('submit','#brand_form', function(event){
		event.preventDefault();
		$('#action').attr('disabled','disabled');
		var form_data = $(this).serialize();
		$.ajax({
			url:url,
			method:"POST",
			data:form_data,
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
						$('#brand_form')[0].reset();
						$('#brandModal').modal('hide');
						$('#alert_action').fadeIn().html(data);
						branddataTable.ajax.reload();						
					}
					timeout();
				}
		})

	});

	$(document).on('click', '.update', function(){
		var brand_id = $(this).attr("id");
		var btn_action = 'fetch_single';
		$.ajax({
			url:url,
			method:"POST",
			data:{brand_id:brand_id, btn_action:btn_action},
			dataType:"json",
			success:function(data)
			{
				$('#brandModal').modal('show');
				$('#category_id').val(data.category_id);
				$('#brand_name').val(data.brand_name);
				$('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Brand");
				$('#brand_id').val(brand_id);
				$('#action').val('Edit');
				$('#btn_action').val('Edit');
			}
		})
	});

	$(document).on('click', '.delete', function(){
        var brand_id = $(this).attr("id");
		var status  = $(this).data('status');
		var btn_action = 'delete';	
		var data={brand_id:brand_id, status:status, btn_action:btn_action};
		disable(url,branddataTable,data,'change the status');    
  	});

	var branddataTable = $('#brand_data').DataTable({
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
				"targets":[4],
				"orderable":false,
			},
		],
		"pageLength": 10
	});

});
</script>


<?php
include_once(INC.'footer.php');
?>