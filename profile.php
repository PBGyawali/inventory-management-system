<?php
//profile.php

include_once('config.php');
include_once(INC.'init.php');

if(!$ims->is_login())
{
    header("location:".$ims->login);
}



include_once(INC.'header.php');
$name = $row['user_name'];
$email = $row['user_email'];
$user_id = $row['user_id'];
?>



	        <div class="col-xs-12 col-sm-9 col-md-8 col-lg-6 pt-3 m-auto">
			<div class="row row-fluid">					
			<div id="error_msg" class="alert alert-danger error_msg m-auto text-center col-md-12" role="alert" style="display:none"></div>	
			<div id="success_msg" class="alert alert-success success_msg m-auto text-center" role="alert" style="display:none" ></div>	</div>
			<span id="message"></span>	
	            <div class="card">
	            	<div class="card-header">
	            		<div class="row">
	            			<div class="col">
	            				<h2>Update Profile <i class="fas fa5x fa-user"></i></h2>
	            			</div>
	            			
	            		</div>
	            	</div>
	            	<div class="card-body pr-0">	            		
	            		<div class="col-md-12">
							<form method="post" id="edit_profile_form" action="<?php echo SERVER_URL?>user_action.php">
							<div class="form-group">
					          		<div class="row">
						            	<label class="col-xs-12 col-sm-3 text-left pl-0 pr-1 ">Username <span class="text-danger">*</span></label>
						            	<div class="col-xs-12 col-sm-9 pl-0">
										<input type="text" name="user_name" id="user_name" class="form-control" value="<?php echo $name; ?>" required data-parsley-trigger="on change"/>						          
						            	</div>
						            </div>
					          	</div>
							<div class="form-group">
					          		<div class="row">
						            	<label class="col-xs-12 col-sm-3 text-left pl-0 pr-1 ">Email <span class="text-danger">*</span></label>
						            	<div class="col-xs-12 col-sm-9 pl-0">
											<input type="email" name="user_email" id="user_email" class="form-control" required value="<?php echo $email; ?>" data-parsley-trigger="on change" />						            		
						            	</div>
						            </div>
								  </div>
								 
					          		<div class="row">						            	
						            	<div class="col-xs-12 col-sm-3 pl-0 pr-1">						            		
										</div>
										<label class="col-xs-12 col-sm-9 text-left pl-0  ">Leave Password blank if you do not want to change <span class="text-danger invisible">*</span></label>
						            </div>
					        								
	            				<div class="form-group">
					          		<div class="row">
						            	<label class="col-xs-12 col-sm-3 text-left pl-0 pr-1 ">Current Password <span class="text-danger invisible">*</span></label>
						            	<div class="col-xs-12 col-sm-9 pl-0">
										<input type="password" name="old_password" id="old_password" class="form-control password"    data-parsley-minlength="6" data-parsley-maxlength="16" data-parsley-trigger="on blur" />
						            		   
						            	</div>
						            </div>
					          	</div>
					          	<div class="form-group">
					          		<div class="row">
						            	<label class="col-xs-12 col-sm-3 text-left pl-0 pr-1 ">New Password <span class="text-danger invisible">*</span></label>
						            	<div class="col-xs-12 col-sm-9 pl-0">
										<input type="password" name="user_password" id="user_new_password" class="form-control password" data-parsley-minlength="6" data-parsley-maxlength="16" data-parsley-trigger="on blur" />
						            	</div>
						            </div>
					          	</div>
					          	<div class="form-group">
					          		<div class="row">
						            	<label class="col-xs-12 col-sm-3 text-left pl-0 pr-1 ">Confirm Password <span class="text-danger invisible">*</span></label>
						            	<div class="col-xs-12 col-sm-9 pl-0 ">
										<input type="password" name="user_re_enter_password" id="user_re_enter_password" class="form-control password" data-parsley-equalto="#user_new_password" data-parsley-trigger="on change" />
										<span id="error_password"></span>	
						            	</div>
						            </div>
								  </div>
								  <div class="form-group">
								  <div class="row">
						            	<label class="col-xs-12 col-sm-3 text-left pl-0 pr-1 ">Profile Picture <span class="text-danger invisible">*</span></label>
						            	<div class="col-xs-12 col-sm-9 pl-0 ">		
										<input type="file" name="profile_image" id="upload_picture"  class=" ml-1 file_upload" data-allowed_file='[<?php echo '"' . implode('","', ALLOWED_IMAGES) . '"'?>]' data-upload_time="later" accept="<?php echo "image/" . implode(", image/", ALLOWED_IMAGES);?>"/>							
										</div>
									</div>
								</div>
					          	<br />
					          	<div class="form-group text-center">
										<input type="hidden" name="user_id" value="<?php echo $user_id ?>" />
										<input type="hidden" name="btn_action" value="Edit" />
					          		<button type="submit" name="update_profile" id="submit_button" class="btn btn-success"><i class="fas fa-lock"></i> Change</button>
					          	</div>
	            			</form>
	            		</div>
	            		
					</div>
					
				</div><!--card body end!-->
				
			</div>
			
		</div>
		
	</div>
<script>
$(document).ready(function(){
	$('#edit_profile_form').on('submit', function(event){
		event.preventDefault();
		if($('#user_new_password').val() != '')
		{
			if($('#user_new_password').val() != $('#user_re_enter_password').val())
			{
				$('#error_password').html('<div class="alert alert-warning">Password did not Match</div>').show();			
				return false;
			}
			else
			{
				$('#error_password').html('');
			}		
		}		
		$('#user_re_enter_password').attr('required',false);
		var data  = new FormData(this);
		url=$('#edit_profile_form').attr('action');
		var buttonvalue=$('#submit_button').html();
		$.ajax({
			url:url,
			method:"POST",
			data:data,
			dataType:'json',
			contentType:false,
			processData:false,	
			beforeSend:function()
			{
				$('#submit_button').attr('disabled', 'disabled');
				$('#submit_button').text('Please wait...');
			},	
			complete:function()
			{
				$('#submit_button').attr('disabled', false);
				$('#submit_button').html(buttonvalue);
				$('.password').val('');					
			},				
			success:function(data)
			{				
				$('#message').html(data);
				timeout();
			}
		})
	});
});
</script>

<?php include_once(INC.'footer.php');?>
			
