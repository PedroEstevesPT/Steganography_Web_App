<?php
	/*Suppose I submited on index.html the file abc.jpeg to the php server.*/



main();

function main(){
	$file = $_FILES["file"];		                        	//echoing $file: returns error, array to string conversion.				
	$filename=$_FILES['file']['name'];   						//echoing $filename: prints "abc.jpeg"
	move_uploaded_file($file["tmp_name"], "uploads/".$filename); //Saves the file on the server with this path: uploads/abc.jpeg

   $smallPhoto =  discoverImage();
   returnImage($smallPhoto);
}


function generateHiddenImage($buffer){
	
	 $nb = "";
	 for($i = 0;$i<strlen($buffer)/8;$i++) {
	     $sub = substr($buffer, $i*8,8);
	     $nb .= chr(bindec($sub));
	 }

	 return $nb;
}
	 



function returnImage($img){
	$name = $_FILES['file']['name'];
 	$new = fopen("uploads/".$name ,"wb");
	fwrite($new,$img); 
	header('Content-Description: File Transfer');
	header('Content-Type: image/png');
	header("Content-Disposition: attachment; filename=\"" . basename($name) . "\";");
	header('Content-Transfer-Encoding: binary');
	ob_clean();
	flush();
	readfile("uploads/".$name);                             //Sends file back to the client.
	exit;    
}




function discoverImage(){
	$filename=$_FILES['file']['name']; 
	$protoPixelIterations = substr($filename, 7);
	$arr = explode(".", $protoPixelIterations, 2);
	$pixelsIterations = $arr[0];

	$img = file_get_contents("uploads/".$filename);    //img vai ser uma string contendo image data
	$bigImg = imagecreatefromstring($img);          //$image vai ter a imagem e asua extensao automaticamente
	$width  = imagesx ($bigImg);
	$height = imagesy ($bigImg);

	imagealphablending($bigImg, false);
	imagesavealpha($bigImg, true);
	
	$cont = 0;
	$answ="";
	for($x = 0; $x < $width; $x++){
		for($y = 0; $y < $height; $y++){

	     	$rgb  = imagecolorat($bigImg, $x, $y);
			$argb = imagecolorsforindex($bigImg, $rgb);

			$answ = $answ . discoverString($answ,$argb);

			$cont++;
			
			if($cont == $pixelsIterations) break 2;
		} 
	}

	return generateHiddenImage($answ); 
}

$cont = 0;
function discoverString($anws,$argb){

	$GLOBALS['cont']++;
 
	$a = decbin($argb['alpha']);
	$a = checkLength($a,0);

	$r = decbin($argb['red']);
	$r = checkLength($r,1);

	$g = decbin($argb['green']);
	$g = checkLength($g,1);

	$b = decbin($argb['blue']);
	$b = checkLength($b,1);


	$protoAnsw = substr($a, -4).substr($r, -1).substr($g, -1).substr($b, -2);

	return $protoAnsw; 

}


//flag 0 para alpha.
//flag 1 para rgb.
function checkLength($component,$a){
	$extra = "";

	if($a == 0){
		$dif = 7 - strlen($component);
		if($dif != 0){
			for($x = 0; $x < $dif; $x++){
				$extra = $extra . "0";
			}
			return  $extra . $component;
		}
	}

	else{
		$dif = 8 - strlen($component);
		if($dif != 0){
			for($x = 0; $x < $dif; $x++){
				$extra = $extra . "0";
			}
			return  $extra . $component;
		}

	}
	return $component;

}

/*
	//Setting header
	header('Content-Description: File Transfer');
	header('Content-Type: image/png');
	header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\";");
	header('Content-Transfer-Encoding: binary');
	ob_clean();
	flush();
	readfile("uploads/".$filename);                             //Sends file back to the client. */
	exit;    



?>