
$(document).ready(function(){

  
  $(".file_upload").change(function()
  {
          var extension = $(".file_upload").val().split('.').pop().toLowerCase();
          var upload_time=$(this).data('upload_time'); 
          var file = $(this)[0].files[0];         
          var filesize = this.files[0].size;            
          var allowed_file=$(this).data('allowed_file');           
          if(extension != '')
          {
                if(jQuery.inArray(extension, allowed_file) == -1)
                {
                  return showAlert("Invalid Image File type");
                }
                else if (filesize > 500000)
                {     
                  return showAlert("File Image size is too large"); 
                }
                else 
                  {
                        var _URL = window.URL || window.webkitURL;
                        var image = new Image();
                        image.src = _URL.createObjectURL(file);
                        image.onload = function() 
                        {
                            imgwidth = this.width;
                            imgheight = this.height;
                            if (imgheight + imgwidth == 0) 
                                return showAlert('This file is not an image');
                            else if ( upload_time=='now')
                                uploadNow();
                            else
                                return false;  
                        }
                        image.onerror = function() {
                          return showAlert("This is not a valid " + extension+" file");                
                        }
                  };
          }
  });
       
  $(document).on('click','.delete_btn',function(event){ 
      event.preventDefault();  
        $.confirm
        ({
            title: 'Delete Picture!',
            content: 'This will delete the data permanently. Are you sure?',    
            buttons: 
            {
                Yes: 
                {//name of the function            
                      action: function()
                      {                     
                          var url = $('#picture_upload').attr('action'); 
                          var form = $('form#picture_upload')[0]; // You need to use standard javascript object here
                          var data  = new FormData(form);                
                          // If you want to add an extra field for the FormData
                          data.append("delete_picture", 1);
                          $.ajax
                          ({
                              url:url,
                              method:"POST",
                              data:data,
                              contentType:false,
                              processData:false,
                              dataType:"JSON",
                              success:function(data)
                              {          
                                if(data.error != '')
                                {
                                  $('.error_msg').text(data.error);
                                  $('.error_msg').show(); 
                                  $(".error_msg").fadeTo(3500, 800).slideUp(800);	              
                                }
                                else
                                {					
                                  $('.success_msg').text(data.success);                      
                                  $('.success_msg').show();
                                  $(".success_msg").fadeTo(3500, 800).slideUp(800);						
                                  $('#user_uploaded_image').html('<a data-fancybox="gallery" href="'+data.profile_image+'"data-caption="Your full picture"><img class="rounded-circle mb-0 mt-0" src="'+data.profile_image+'" width="200" height= "200" ></span>');
                                  $('#user_uploaded_image_medium').html('<img src="'+data.profile_image+'" class="rounded-circle mb-1 mt-0" width="100" height="100" />');
                                  $('#user_uploaded_image_small').html('<img src="'+data.profile_image+'" class="rounded-circle mb-1 mt-0" width="30" height="30" />');
                                  $('#upload_icon_text').text(' Upload New');
                                  $('.delete_btn').hide();
                                }
                              }
                          });
                        }
                  },                      
            }
        });
    });

      $("a.logout,button.logout").click(function(event)
      {
          event.preventDefault();
          var url=$(this).attr('href');
          var form = $(this).closest('form');
          $.confirm
          ({
              title: 'Log Out!',
              content: 'This will log you out. Are you sure buddy?',    
              buttons: 
              {
                  Yes: 
                  {//name of the function            
                      action: function()
                      {
                        form.submit();
                        if(url!='' && typeof url!='undefined')
                        window.location.href =  url;         
                      }
                  },
                
              }
           });
      });
    $("a#logout,button.logout").hover(function()
    {
      $(this).css({"background-color": "red","color":"white"});
    },
      function(){//return to original state when hover out
        $(this).css({"background-color": "","color":""});
    });

    
function showAlert($content){

  $.alert
  ({
      title: 'File selection Invalid',
      content: $content,
      buttons: 
        {
            No: 
            {
              text:'OK',           
              btnClass: 'btn-blue',
            }, 
            Yes:
            {
              isHidden: true,
            }
        }
  });
  $(".file_upload").val('');
  return false;
}


    function uploadNow()
    {
        $.confirm
        ({
            title: 'Change Image!',
            content: 'This will change the image. Are you sure to proceed?',
            type: 'blue',    
            buttons: 
            {
                Yes: 
                {//name of the function            
                    btnClass: 'btn-blue',            
                    action: function()
                    {
                        var url = $('#picture_upload').attr('action');            
                        var form = $('form#picture_upload')[0]; // You need to use standard javascript object here
                        var data  = new FormData(form);   
                        // If you want to add an extra field for the FormData
                        data.append("upload", 1);                    
                        $.ajax
                        ({
                            url:url,
                            method:"POST",
                            data:data,
                            contentType:false,
                            processData:false,
                            dataType:"JSON",
                            success:function(data)
                                  {          
                                      if(data.error != '')
                                      {
                                        $('.error_msg').text(data.error);
                                        $('.error_msg').show(); 
                                        $(".error_msg").fadeTo(2500, 500).slideUp(500);	              
                                      }
                                      else
                                      {					
                                        $('.success_msg').text(data.success);                                      
                                        $('.success_msg').show();
                                        $(".success_msg").fadeTo(2500, 500).slideUp(500);						
                                        $('#user_uploaded_image').html('<a data-fancybox="gallery" href="'+data.profile_image+'"data-caption="Your full picture"><img class="rounded-circle mb-0 mt-0" src="'+data.profile_image+'" width="200" height= "200" ></span>');
                                        $('#user_uploaded_image_medium').html('<img src="'+data.profile_image+'" class="rounded-circle mb-1 mt-0" width="100" height="100" />');
                                        $('#user_uploaded_image_small').html('<img src="'+data.profile_image+'" class="rounded-circle mb-1 mt-0" width="30" height="30" />');
                                        $('#upload_icon_text').text(' Change');
                                        $('#delete_div').html(data.button);
                                        $('#delete_picture').show();                                        
                                      }
                                  } 
                          });     //ajax call end
                    }
                },        
            }         
        });//confirm box
    }


});