
$(document).ready(function(){
	var url = $('#user_form').attr('action');
	$('#user_form').on('submit', function(e){		
		e.preventDefault();							
		var data = new FormData(this);
		// If you want to add an extra field for the FormData
        data.append("update_user", 1);
			$.ajax({
				url:url,
				method:"POST",
				data:data,
				contentType:false,
				processData:false,
				dataType:"JSON",
				beforeSend:function()
				{
					$('#submit_button').attr('disabled', 'disabled');
					$('#submit_button').html('wait...');
				},
				error:function(){
					$('#submit_button').attr('disabled', false);
					$('#submit_button').html('<i class="far fa-save"></i> Update');
				},
				complete:function(){
					$('#submit_button').attr('disabled', false);
					$('#submit_button').html('<i class="far fa-save"></i> Update');
				},
				success:function(data)
				{	
					errors=data.error;					
					if(errors!='')	{
						$.each(errors, function(i){
						$('.error_msg').append(" <p>"+  errors[i]+ "</p>");	
						});	
						$('.error_msg').show(); 
						$(".error_msg").fadeTo(3500,500,function() {
						$(".error_msg").html('')  }).slideUp(500);
					}			
					else{
						$('#success_msg').html(data.success);
						$('#success_msg').show();
						$("#success_msg").fadeTo(2500, 500).slideUp(500);

					}
							
				}
			})
		
	});
		
			$("#facebook").click(function(event){
				socialMedia('Facebook','facebook');
			});

			$("#twitter").click(function(event){
				socialMedia('Twitter','twitter');
			});

			$("#google-plus").click(function(event){
				socialMedia('Google-plus','google-plus','red');	
			});


			$('.social_media_data').each(function ()
				{
					var data= $(this).val();
					var button_id=$(this).data('id');												
					if (data == '')
					{
						$('#'+button_id).css({"filter": "grayscale(100%)","-webkit-filter":"grayscale(100%)"});							
					}
				});

function socialMedia($maintitle,$title,$color='blue'){
	$.confirm
	({
		title: $maintitle,
		content: '' +
		'<form action="" id="confirm_form">' +
		'<div class="form-group">' +
		'<label>Please enter your '+$title+ ' profile link.</label>' +
		'<input type="text" value="' +$('#'+$title+'_data').val()+ '" placeholder="Your link here" class="link " required />' +
		'</div>' +
		'</form>',  
		type: $color,  
		boxWidth: '35%',    
		icon: 'fab fa-'+$title,    
		buttons: {
				Yes: {//also the name of the function
						text: 'Save',
						btnClass: 'btn-green',          
						action: function(){
						var link= this.$content.find('.link').val();
						if(!link){
							$('#'+$title).css({"filter": "grayscale(100%)","-webkit-filter": "grayscale(100%)"});				
						}
						else{
							$('#'+$title).css({"filter": "","-webkit-filter": ""});				
						}
						$('#'+$title+'_data').val(link);
					}        
				},
				No: {text:'Cancel',
				btnClass: 'btn-blue',
			   
			}
		},
	   
	});

};


});

