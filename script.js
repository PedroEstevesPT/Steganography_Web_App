
// your all code

function btnClick() {
	console.log("gay");
	return false;
}




$("#form1").submit(function(e) {
	console.log("entrei");
	$.ajax({
		url: "hide.php", 
		type: "POST",             
		data: new FormData(this), 
		contentType: false,       
		cache: false,             
		processData:false,          
		success: function(data)   
			{
				    alert( "second success" );

				$("#message").html("\"Success!Your picture has been sucessfully hidden!\"");
			},



});


$("#form2").submit(function(e) {
	$.ajax({

		url: "discover.php", 
		type: "POST",             
		data: new FormData(this), 
		contentType: false,       
		cache: false,             
		processData:false,          
		success: function(data)   
			{
				$("#message").html("Success!Your secrets have been revealed.");

			}
		});
}); 

