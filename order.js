


function printOrder(orderId ) {
	
		$.ajax({
			url: 'printOrder.php',
			type: 'post',
			data: {orderId: orderId,table:'sales'},
			dataType: 'text',
			success:function(response) {
		var mywindow = window.open('', 'Stock Management System', 'height=400,width=600');
        mywindow.document.write('<html><head><title>Order Invoice</title>');        
        mywindow.document.write('</head><body>');
        mywindow.document.write(response);
        mywindow.document.write('</body></html>');
        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10
        mywindow.resizeTo(screen.width, screen.height);
			}// /success function
		}); // /ajax function to fetch the printable order
} // /print order function
