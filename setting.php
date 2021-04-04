<?php

include_once('config.php');
include_once(INC.'init.php');
include_once (CLASS_DIR.'select.php');
$select= new select();
$setup=false;
if(isset($_SESSION['setup'])&&!empty($_SESSION['setup']))
{
    $setup=true;
}
elseif(!$ims->is_login())
{
    header("location:".$ims->login);
}
elseif(!$ims->is_admin())
{
    header("location:".$ims->dashboard);
}
else
{
    $rows = $ims->getArray('company_table');
}
include_once(INC.'header.php');

?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800"><?php echo ($setup)?'System Configuration':'Setting'?></h1>

                    <!-- DataTales Example -->
                    <span id="message"></span>
                    <form method="post" class="setting_form" id="setting_form" enctype="multipart/form-data" action="<?php echo SERVER_URL?>setting_action.php">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="m-0 font-weight-bold text-primary"><?php echo ($setup)?'Set up Account':'Setting'?></h6>
                                    </div>
                                    <div clas="col text-right" >
                                        <button type="submit" name="edit_button" id="edit_button" class="btn btn-primary btn-sm"> <?php echo ($setup)?'<i class="fas fa-save"></i>  Set Up':'<i class="fas fa-edit"></i> Edit'?></button>
                                        &nbsp;&nbsp;
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Company Name</label>
                                            <input type="text" name="company_name" value="<?php echo ($setup)?'':$rows['company_name']?>"id="company_name" class="form-control" />
                                        </div>
                                        <div class="form-group">
                                            <label>Company Email</label>
                                            <input type="text" name="company_email" value="<?php echo ($setup)?'':$rows['company_email']?>"id="company_email" class="form-control" />
                                        </div>
                                        <div class="form-group">
                                            <label>Company Contact No.</label>
                                            <input type="text" name="company_contact_no" value="<?php echo ($setup)?'':$rows['company_contact_no']?>"id="company_contact_no" class="form-control" />
                                        </div>
                                        <div class="form-group">
                                            <label>Company Address</label>
                                            <input type="text" name="company_address" value="<?php echo ($setup)?'':$rows['company_address']?>"id="company_address" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                                <label>Sales Target</label>
                                                <input type="number" name="company_sales_target" value="<?php echo ($setup)?'':$rows['company_sales_target']?>"id="company_sales_target" class="form-control" />
                                            </div>  
                                            <div class="form-group">
                                                <label>Revenue Target</label>
                                                <input type="number" name="company_revenue_target" value="<?php echo ($setup)?'':$rows['company_revenue_target']?>"id="company_revenue_target" class="form-control" />
                                            </div>                                    
                                        <div class="form-group">
                                            <label>Currency</label>
                                            <?php  echo $select->Currency_list(($setup)?'':$rows['company_currency']); ?>
                                        </div>
                                        <div class="form-group">
                                            <label>Timezone</label>
                                            <?php  echo $select->Timezone_list(($setup)?'':$rows['company_timezone']); ?>
                                        </div>
                                        <div class="form-group">
                                            <label>Select Logo</label><br />
                                            <input type="file" name="company_logo" class="file_upload" id="company_logo" />
                                            <br />
                                            <span class="text-muted">Only .jpg, .png file allowed for upload</span><br />
                                            <span id="uploaded_logo"></span>
                                        </div>
                                    </div>
                                </div>
                                <?php if($setup):?>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Admin Email Address</label>
                                            <input type="text" name="admin_email" id="admin_email" class="form-control" required data-parsley-type="email" data-parsley-trigger="keyup" />
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Admin Username</label>
                                            <input type="text" name="admin_username" id="admin_name" class="form-control" required data-parsley-trigger="keyup" />
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Admin Password</label>
                                            <input type="password" name="admin_password" id="admin_password" class="form-control" required data-parsley-trigger="keyup" />
                                        </div>
                                    </div>
                                </div>
                                <?php endif?>
                            </div>
                        </div>
                    </form>
              
<script>
$(document).ready(function(){
	$('#setting_form').on('submit', function(event){
		event.preventDefault();
        $form=$(this);         
        $button= $form.find("button[type=submit]");    //$(document.activeElement); 
        $form.parsley();
        event.preventDefault();
        url=$form.attr('action'); 
        buttonvalue=$button.html();
		if($('#setting_form').parsley().isValid())
		{	      
			$.ajax({
				url:url,
				method:"POST",
				data:new FormData(this),
                dataType:'json',
                contentType:false,
                processData:false,
				beforeSend:function(){
                    $button.html('Wait...').attr('disabled', 'disabled');
				},
                error:function(request){          
                    $form[0].reset();                    
                    $('#message').html('<div class="alert alert-warning">There was some error updating the configuration at the moment</div>');                   
                },
                complete:function(){
                    $button.attr('disabled', false).html(buttonvalue);
                    setTimeout(function(){
				        $('#message').slideup();
				    }, 5000);
                },
				success:function(data){	
                    $('.file_upload').val('');           						
                    $('#message').html(data.success);					
				}
			})
		}
	});
});
</script>