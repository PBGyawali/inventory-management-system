<?php
include_once('config.php');
include_once(INC.'init.php');

if(!$ims->is_login())
    header("location:".$ims->login);

if(!$ims->is_admin())
    header("location:".$ims->dashboard);

include_once(INC.'header.php');
?>
        <span id='alert_action'></span>
		<div class="row">
			<div class="col-lg-12">
				<div class="card card-secondary">
                    <div class="card-header">
                    	<div class="row">
                            <div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            	<h3 class="card-title">Product List</h3>
                            </div>
                        
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6 text-right">
                                <button type="button" name="add" id="add_button" class="btn btn-success btn-sm"> <i class="fas fa-plus"></i> Add</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row"><div class="col-sm-12 table-responsive">
                            <table id="product_data" class="table table-bordered table-striped">
                                <thead><tr>
                                    <th>ID</th>
                                    <th>Category</th>
                                    <th>Brand</th>
                                    <th>Product Name</th>
                                    <th>Available Quantity</th>
                                    <th>Status</th>
                                    <?php if($ims->is_admin()):?>								
								<th class="created">Created By</th>						
								<?php endif?>
								<th class="action">Action</th>
                                </tr></thead>
                            </table>
                        </div></div>
                    </div>
                </div>
			</div>
		</div>

        <div id="productModal" class="modal fade">
            <div class="modal-dialog">
                <form method="post" id="product_form" action="<?php echo SERVER_URL?>product_action.php">
                    <div class="modal-content">
                        <div class="modal-header py-0 pt-1">
                        <h4 class="modal-title"><i class="fa fa-plus"></i> Add Product</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            
                        </div>
                        <div class="modal-body pb-0 my-0">
                            <div class="form-group">
                                <label>Select Category</label>
                                <select name="category_id" id="category_id" class="form-control" required>                                    
                                    <?php echo $command->fill_category_list();?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Select Brand</label>
                                <select name="brand_id" id="brand_id" class="form-control" required>
                                    <option value="">Select Category First</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Enter Product Name</label>
                                <input type="text" name="product_name" id="product_name" class="form-control" required />
                            </div>
                            <div class="form-group">
                                <label>Enter Product Description</label>
                                <textarea name="product_description" id="product_description" class="form-control" rows="auto" ></textarea>
                            </div>
                            <div class="form-group">
                                <label>Enter Product Opening Stock Quantity</label>
                                <div class="input-group">
                                    <input type="number" name="product_quantity" id="product_quantity" class="form-control" required  /> 
                                    <span class="input-group-addon ">
                                        <select name="product_unit" id="product_unit" class="form-control" required>
                                        <?php echo $command->fill_unit_list()?>                                            
                                        </select>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Enter Product Base Price</label>
                                <input type="text" name="product_base_price" id="product_base_price" class="form-control" required pattern="[+-]?([0-9]*[.])?[0-9]+" />
                            </div>
                            <div class="form-group">
                                <label>Enter Product Tax (%)</label>
                                <input type="text" name="product_tax" id="product_tax" class="form-control" style="display:none" required pattern="[+-]?([0-9]*[.])?[0-9]+" />
                                <span id="span_tax_details"></span>                            
                            </div>
                        </div>
                        <div class="modal-footer py-0 my-0">
                            <input type="hidden" name="product_id" id="product_id" />
                            <input type="hidden" name="btn_action" id="btn_action" />
                            <input type="submit" name="action" id="action" class="btn btn-success" value="Add" />
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="productdetailsModal" class="modal fade">
            <div class="modal-dialog">
                <form method="post" id="product_detail_form">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h4 class="modal-title"><i class="fa fa-eye"></i> Product Details</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            
                        </div>
                        <div class="modal-body">
                            <div id="product_details"></div>
                        </div>
                        <div class="modal-footer">                       
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

<script>
$(document).ready(function(){
eventurl='eventhandler.php';
    var tax_list;		
			$.ajax({
                    'type': "POST",
                    'global': false,
                    'dataType': 'json',
                    'url': eventurl,
                    'data': { get_tax_list:1},
                    'success': function(data){
                        callback(data);
                    },
			    });
			function callback(response) {
				tax_list = response;								
			}

    var url=$('#product_form').attr('action');
    var productdataTable = $('#product_data').DataTable({
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
        "pageLength": 10
    });    
            function add_tax_row(count = '')
		{
			var html = '';
			html += '<span id="row'+count+'"><div class="row">';
			html += '<div class="col-md-11 pr-0">';
			html += '<select name="tax_id[]" id="tax_id'+count+'" class="form-control" data-live-search="true" required>';
			html += tax_list;
			html += '</select><input type="hidden" name="hidden_tax_id[]" id="hidden_product_id'+count+'" />';
			html += '</div>';			
			html += '<div class="col-md-1 pl-0">';
			if(count == '')			
				html += '<button type="button" name="add_more" id="add_more" class="btn btn-success">+</button>';			
			else			
				html += '<button type="button" name="remove" id="'+count+'" class="btn btn-danger remove">-</button>';			
			html += '</div>';
			html += '</span>';
			$('#span_tax_details').append(html);
		}

		var count = 0;

		$(document).on('click', '#add_more', function(){
			count = count + 1;
			add_tax_row(count);
		});
		$(document).on('click', '.remove', function(){
			var row_no = $(this).attr("id");
			$('#row'+row_no).remove();
		})

    $('#add_button').click(function(){
        $('#productModal').modal('show');
        $('#product_form')[0].reset();
        $('.modal-title').html("<i class='fa fa-plus'></i> Add Product");
        $('#action').val("Add");
        $('#btn_action').val("Add");
        $('#span_tax_details').html('');
        $('#product_tax').hide();
        $('#product_tax').attr('required',false);        
        $('#brand_id').html('<option value="" >Select Category First</option>');
        add_tax_row();
    });

    $('#category_id').change(function(){
        var category_id = $('#category_id').val();        
        $.ajax({
            url:eventurl,
            method:"POST",
            dataType:'json',
            data:{category_id:category_id, get_brand_list:1},
            success:function(data){
                $('#brand_id').html(data);
            }
        });
    });

    $(document).on('submit', '#product_form', function(event){
        event.preventDefault();
        $('#action').attr('disabled', 'disabled');
        var form_data = $(this).serialize();
        $.ajax({
            url:url,
            method:"POST",
            data:form_data,
            dataType:'json',
            complete:function(){  
                $('#action').attr('disabled', false);                
            },
            success:function(data){
                $('#product_form')[0].reset();
                $('#productModal').modal('hide');
                $('#alert_action').fadeIn().html(data);
                $('#span_tax_details').html('');               
                timeout();
                productdataTable.ajax.reload();
            }
        })
    });

    $(document).on('click', '.view', function(){
        var product_id = $(this).attr("id");
        var btn_action = 'product_details';
        $.ajax({
            url:url,
            method:"POST",
            dataType:'json',
            data:{product_id:product_id, btn_action:btn_action},
            success:function(data){
                $('#productdetailsModal').modal('show');                
                $('#product_details').html(data);
            }
        })
    });

    $(document).on('click', '.update', function(){
        var product_id = $(this).attr("id");
        var btn_action = 'fetch_single';
        $.ajax({
            url:url,
            method:"POST",
            data:{product_id:product_id, btn_action:btn_action},
            dataType:"json",
            success:function(data){
                $('#productModal').modal('show');
                $('#category_id').val(data.category_id);
                $('#brand_id').html(data.brand_select_box);
                $('#brand_id').val(data.brand_id);
                $('#product_name').val(data.product_name);
                $('#product_description').val(data.product_description);
                $('#product_quantity').val(data.product_quantity);
                $('#product_unit').val(data.product_unit);
                $('#product_base_price').val(data.product_base_price);
                $('#product_tax').val(data.product_tax);
                $('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Product");
                $('#product_id').val(product_id);
                $('#action').val("Edit");
                $('#product_tax').attr('required','required');
                $('#span_tax_details').html('');
                $('#product_tax').show();
                $('#btn_action').val("Edit");
            }
        })
    });  

    $(document).on('click', '.delete', function(){
        var product_id = $(this).attr("id");
        var status = $(this).data("status");
        var btn_action = 'delete';	
		var data={product_id:product_id, status:status, btn_action:btn_action};
		disable(url,productdataTable,data,change+' the status');    
  	});

});
</script>
<?php include_once(INC.'footer.php');?>