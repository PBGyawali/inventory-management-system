<?php
//user.php
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
                            	<h3 class="card-title">User List</h3>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6 text-right">
                            	<button type="button" name="add" id="add_button" data-toggle="modal" data-target="#userModal" class="btn btn-success btn-sm"><i class="fas fa-user-plus"></i> Add</button>
                        	</div>
                        </div>
                       
                        <div class="clear:both"></div>
                   	</div>
                   	<div class="card-body">
                   		<div class="row"><div class="col-sm-12 table-responsive">
                   			<table id="user_data" class="table table-bordered table-striped">
                   				<thead>
									<tr>
										<th>ID</th>
										<th>Username</th>
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
        <div id="userModal" class="modal fade" data-backdrop="static">
        	<div class="modal-dialog">
        		<form method="post" id="user_form"  action="<?php echo SERVER_URL?>user_action.php">
        			<div class="modal-content">
        			<div class="modal-header">        			
						<h4 class="modal-title" id="modal_title">Add User</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        			</div>
        			<div class="modal-body">
        				<div class="form-group">
							<label>Enter User Name</label>
							<input type="text" name="user_name" id="user_name" class="form-control" required />
						</div>
						<div class="form-group">
							<label>Enter User Email</label>
							<input type="email" name="user_email" id="user_email" class="form-control" required />
						</div>
						<div class="form-group">
							<label>Enter User Password</label>
							<input type="password" name="user_password" id="user_password" class="form-control" required />
						</div>
        			</div>
        			<div class="modal-footer">
        				<input type="hidden" name="user_id" id="user_id" />
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
	var url=$('#user_form').attr('action');
	
	$('#add_button').click(function(){
		$('#user_form')[0].reset();
		$('.modal-title').html("<i class='fa fa-plus'></i> Add User");
		$('#action').val("Add");
		$('#action').attr('disabled', false);
		$('#btn_action').val("Add");
	});

	var userdataTable = $('#user_data').DataTable({
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

	$(document).on('submit', '#user_form', function(event){
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
				$('#user_form')[0].reset();
				$('#userModal').modal('hide');				
				$('#action').attr('disabled', false);
			},
			success:function(data)
			{
				$('#alert_action').html('<div class="text-center">'+data+'</div>');				
				timeout();
				userdataTable.ajax.reload();
			}
		})
	});

	$(document).on('click', '.update', function(){
		var user_id = $(this).attr("id");
		var btn_action = 'fetch_single';
		$.ajax({
			url:url,
			method:"POST",
			data:{user_id:user_id, btn_action:btn_action},
			dataType:"json",
			success:function(data)
			{
				$('#userModal').modal('show');
				$('#user_name').val(data.user_name);
				$('#user_email').val(data.user_email);
				$('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit User");
				$('#user_id').val(user_id);
				$('#action').val('Edit');
				$('#btn_action').val('Edit');
				$('#user_password').attr('required', false);
				$('#action').attr('disabled', false);
			}
		})
	});

	$(document).on('click', '.delete', function(){
		$clickedbtn=$(this);
		var user_id = $clickedbtn.attr("id");
		var status = $clickedbtn.data('status');
		var change="disable";		
		if (status=='Inactive')			
			change="enable";		
		var btn_action = "disable";		
		var data={user_id:user_id, status:status, btn_action:btn_action};
		disable(url,userdataTable,data,change+' the status');    
  	});




});
</script>

<?php
include_once(INC."footer.php");
?>
