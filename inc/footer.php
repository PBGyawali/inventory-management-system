		</div>
	</body>
</html>
<script>

	function timeout()
	{		setTimeout(function(){
            $('.error, .message, .alert').slideUp();
		}, 3000);
		
		setTimeout(function(){
		$('#message,#alert_action,#form_message').html('');
		}, 5000);
	}
	function showMessage(datatable=null,data)
	{		$('#alert_action,#message').fadeIn().html(data);
		if(datatable)
			datatable.ajax.reload();
		timeout();		
		
	}
	
	function disable(url,datatable,data,message="change the status"){	
		//var data2 = {"btn_action":"delete"};
		//var main_data = Object.assign({}, data, data2);
        $.confirm
        ({
            title: 'Confirmation please!',
            content: "This will "+ message+". Are you sure?", 
			type: 'blue',   
            buttons:{
						Yes: { 
							btnClass: 'btn-blue',           
							action: function() {   
								$.ajax({
									url:url,
									method:"POST",
									data:data,                              
									dataType:"JSON",
									success:function(response){   									
										showMessage(datatable,response);                             
									}
								});
							}
						},                      
					}
        });
    }
	

</script>
