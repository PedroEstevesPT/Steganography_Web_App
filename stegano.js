
// your all code




function btnClick(id) {
	if(checkFiles(id) && checkExtensions(id)){
		if(id == 1 ){
			return true;
		}
		else{
		   	if(checkDimensions() == false)
			return false;
		}
	} 
	return false;
}


function checkFiles(id){
	if(id == 0){
		var files = $('#file').prop("files");
		if (files.length == 0 ){
			$("#message").html("STEGANOGRAPHER/DISCOVER: \"You didnt select any files\"").css("color", "yellow");
			return false;
		}
		else if(files.length == 1){
			$("#message").html("STEGANOGRAPHER/DISCOVER: \"You only selected one file.\"").css("color", "yellow");
			return false;
		}
		else if(files.length > 2){
			$("#message").html("STEGANOGRAPHER/DISCOVER: \"You selected more than 2 files. Please select only 2.\"").css("color", "yellow");
			return false;
		}
	}
	else if(id ==1){
		var files = $('#file2').prop("files");
		if (files.length == 0 ){
			$("#message").html("STEGANOGRAPHER/DISCOVER: \"You didnt select any files.\"").css("color", "yellow");
			return false;
		}
	}
	return true;
}

function checkExtensions(id){

	if(id == 0)
		var ficheiros = document.getElementById('file');
	else 
		var ficheiros = document.getElementById('file2');

	for(var x = 0; x < ficheiros.files.length; x++){

		//Se a extensao nao for a certa.
		if(!checkExtension(ficheiros.files.item(x).name,id))
			return false;
	}
	return true;	
}

function checkExtension(name,id){
	var res = name.split(".");
	if(id == 0 ){ 
		if(res[1] !="jpeg" && res[1] != "jpg" && res[1] != "png"){
			$("#message").html("STEGANOGRAPHER/DISCOVER: \"The extension " + res[1] + " isn't valid. Please input jpg,jpeg or png.\"" ).css("color", "yellow");
			return false;
		}
	}
	else{
		if(res[1] != "png"){
			$("#message").html("STEGANOGRAPHER/DISCOVER: \"The extension " + res[1] + " isn't valid. Please input a png.\"" ).css("color", "yellow");
			return false;
		}
	}
	return true;
}



function checkDimensions(){
	return false;	
	var files = $('#file').prop("files");
	var pixeis1,pixeis2;
	var img0 = new Image();
	var img1 = new Image();
	var url = URL.createObjectURL(files[0]);
	img0.src = url;

	var url1 = URL.createObjectURL(files[1]);
	img.src = url1;

	var poll = setInterval(function(){
		if(img0.NaturalWidth && img1.NaturalWidth){
			clearInterval(poll);
			console.log(img0.NaturalWidth);
		}
	},10);


	img0.onload = function(){
		img1.onload = function(){
			pixeis1 = img0.naturalWidth * img0.naturalHeight;
			return true;
		}
	}
	return false;


}



$("#form1").submit(function(e){
	$.ajax({
		url: "hide.php", 
		type: "POST",             
		data: new FormData(this), 
		contentType: false,       
		cache: false,             
		processData:false,  

		success: function(data){	
		  $("#message").html("\"Success!Your picture has been sucessfully hidden!\"");
		},
	});
 //e.preventDefault();

})

$("#form2").submit(function(e){
	$.ajax({

		url: "discover.php", 
		type: "POST",             
		data: new FormData(this), 
		contentType: false,       
		cache: false,             
		processData:false,          
		success: function(data){
			$("#message").html("Success!Your secrets have been revealed.");

		}
	});
}) 

 