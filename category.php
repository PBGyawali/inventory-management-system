<?php
include_once('config.php');
include_once(INC.'init.php');

if(!$ims->is_login())
    header("location:".$ims->login);

if(!$ims->is_admin())
    header("location:".$ims->dashboard);

include_once(INC.'header.php');
?>

		<span class="position-absolute w-100 text-center" id="message"style="z-index:10"></span>
		<div class="row">
			<div class="col-lg-12">
				<div class="card card-secondary">
                    <div class="card-header">
                    	<div class="row">
                        	<div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            	<h3 class="card-title">Category List</h3>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6 text-right">
                            	<button type="button" name="add" id="add_button" data-toggle="modal" data-target="#categoryModal" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Add</button>
                        	</div>
                        </div>
                       
                        <div class="clear:both"></div>
                   	</div>
                <div class="card-body">
                    <div class="row">
                    	<div class="col-sm-12 table-responsive">
                    		<table id="category_data" class="table table-bordered table-striped">
                    			<thead><tr>
									<th>ID</th>
									<th>Category Name</th>
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
    				
    		
	
	<div id="categoryModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="category_form"  action="<?php echo SERVER_URL?>category_action.php">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title"><i class="fa fa-plus"></i>Add category</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
		          	<div class="form-group">
		          		<div class="row">
			            	<label class="col-md-3 text-left px-0 pl-1">Enter Category</label>
			            	<div class="col-md-9 px-0 pr-1">
			            		<input type="text" name="category_name" id="category_name" class="form-control"  data-parsley-pattern="/^[a-zA-Z\s]+$/" data-parsley-trigger="keyup" />
			            	</div>
			            </div>
		          	</div>
		          	
        		<div class="modal-footer">
				<input type="hidden" name="category_id" id="category_id"/>
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
	var url=$('#category_form').attr('action');	

	$('#add_button').click(function(){
		$('#category_form')[0].reset();
		$('.modal-title').html("<i class='fa fa-plus'></i> Add Category");
		$('#action').val('Add');
		$('#btn_action').val('Add');
	});

	$(document).on('submit','#category_form', function(event){
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
				$('#action').attr('disabled', false);
				$('#action').val('Save');
			},
			success:function(data)
			{
				if(data.error != '')
					$('#form_message').html(data.error);
				else{
					$('#category_form')[0].reset();
					$('#categoryModal').modal('hide');					
					showMessage(datatable,data.success);						
				}				
			}
		})
	});

	$(document).on('click', '.update', function(){
		var category_id = $(this).attr("id");
		var btn_action = 'fetch_single';
		$.ajax({
			url:url,
			method:"POST",
			data:{category_id:category_id, btn_action:btn_action},
			dataType:"json",
			success:function(data)
			{
				$('#categoryModal').modal('show');
				$('#category_name').val(data.category_name);
				$('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Category");
				$('#category_id').val(category_id);
				$('#action').val('Edit');
				$('#btn_action').val("Edit");
			}
		})
	});

	var datatable = $('#category_data').DataTable({
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
			var category_id = $(this).attr('id');
			var status = $(this).data("status");
			var btn_action = "delete";	
			var data={category_id:category_id, status:status, btn_action:btn_action};
			disable(url,datatable,data,'change the status');    
  	});


});
</script>

<?php
include_once(INC.'footer.php');
?>


				