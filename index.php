<?php
//login.php
include_once('config.php');
include_once(INC.'init.php');
if($ims->is_login())
    header("location:".$ims->dashboard);
 ?>
    <?php include_once(INC.'header.php'); ?>
	<body>
		<br />
		<div class="container container-fluid mt-3">
			<h2 class="text-center">Inventory Management System</h2>
			<br />
			<div class="card ">
				<div class="card-header text-center"><h4>Login Menu</h4></div>
				<span class="position-absolute w-100 text-center" id="message"style="z-index:10"></span>
				<div class="card-body">
					<form method="post" id="login_form" action="<?php echo SERVER_URL?>user_action.php">
						
						<div class="form-group">
								<label>Username/Email </label>									
								<div class="input-group">	
								<div class="input-group-prepend">					
								<span class="input-group-text" id="basic-addon1"><i class="fa fa-user fa-md position-relative"></i></span>
								</div>							
								<input type="text" name="user_email"  class="form-control" id="user_email"placeholder="Your Username" required>
								</div>
								</div>
								<div class="form-group">
								<label>Password</label>
								<div class="input-group">
								<div class="input-group-prepend">
								<span class="input-group-text" id="basic-addon1"><i class="fa fa-lock fa-md position-relative"></i></span>
								</div>
								<input type="password" class="form-control "name="user_password" id="user_password" placeholder="Your Password" required>
								<div class="input-group-append">
								<span toggle="#password" class="input-group-text" ><i class="fa fa-fw fa-eye field-icon toggle-password"></i></span></div>
							</div>							
							</div>
				
						<div class="form-group">
							<button type="submit" name="login" class="btn btn-info">Login</button>
							<butoon type="button"  id="hint" class="btn btn-primary" />Login hint</button>
						</div>	 
					</form>
				</div>
			</div>
		</div>
	</body>
</html>

<script type="text/javascript" src="<?php echo JS_URL.'toggle_password.js'?>"></script>	
<link rel="stylesheet" href="<?php echo CSS_URL.'parsley.css'?>" >
<script type="text/javascript" src="<?php echo JS_URL.'parsley.min.js'?>"></script>	  
<script type="text/javascript" src="<?php echo JS_URL.'popper.min.js'?>"></script>	
<div id="wrapper">
        <div class="blocker"></div>
        <div  class="bg-dark text-white text-center py-0 px-2 pb-0 mb-0" id="popup" style="border-radius:4px;font-size: 16px;">
            <p class="text-warning py-0 my-0">For user login
            <p class="py-0 my-0">username: prakhar
            <p class="py-0 my-0">password: philieep </p>  
            <p  class="text-warning py-0 my-0 ">For user login
            <p class="py-0 my-0">username: gyawali
            <p class="py-0 my-0">password: 123456<p>   
            <p class="text-warning py-0 my-0">For admin login
            <p class="py-0 my-0">username: puskar
            <p class="py-0 my-0">password: philieep</p>                  
        </div>
        <div class="blocker"></div>        
</div>
<script>
        var ref = $('#hint');        
        var popup = $('#popup');
        popup.hide();
        
        ref.click(function(){ 
            popup.show();
                var popper = new Popper(ref,popup,{
                        placement: 'end',
                        onCreate: function(data){
                                console.log(data);
                        },
                        modifiers: {
                                flip: {
                                        behavior: ['left', 'right', 'top','bottom']
                                },
                                offset: { 
                                        enabled: true,
                                        offset: '0,10'
                                }
                        }
                });
                setTimeout(function(){
                    $(popup).slideUp();
                }, 4000);
        });
       


</script>

<script>

$(document).ready(function(){
    classtimeout();
	$('#login_form').parsley();
	var url=$('#login_form').attr('action');
	$('#login_form').on('submit', function(event){
		event.preventDefault();
		if($('#login_form').parsley().isValid())
		{	
            var data = new FormData(this);	
            data.append("login", 1);  
			$.ajax({
				url:url,
				method:"POST",
                data:data,
                contentType:false,
				processData:false,
				dataType:'json',
				beforeSend:function(){
					disableButton();
				},				
                error:function(){  
                    $('#message').html('<div class="alert alert-warning">There was an error logging you in. Please try again later</div>');
					enableButton()
                },
				success:function(data)
				{	
					if(data.status == '')
					{    enableButton();
						$('#message').html(data.error);                        				
					}
					else
					{
                        $('#message').html('<div class="alert alert-success">Login success. Redirecting.......</div>');
                        enableButton(true);                      
						window.location.href = data.login;
					}
				}
			})
		}  
	});

function enableButton(value=false){
    $('.btn').attr('disabled', false);    
    $('.btn').css({"filter": "","-webkit-filter": ""});	
    enableText(value);
}
function enableText(value=false){
	if (!value)
	classtimeout();   
    $('.btn').html('Login');
    $('#hint').text('Login hint');
    if (value)
        $('.btn').html('Logging in'); 
}
function disableButton(){
    $('.btn').css({"filter": "grayscale(100%)","-webkit-filter": "grayscale(100%)"});
    $('.btn').attr('disabled', 'disabled');
	$('.btn').text('Please wait...');

}
function classtimeout(){
    setTimeout(function(){
            $('.alert,.message').slideUp();
        }, 3000);

}

});

</script>