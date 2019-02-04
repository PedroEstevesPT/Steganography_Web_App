<?php
main();
//1
/*tmp_name: Directoria do ficheiro carregado(directoria temporaria)*/
/*name:     nome real do ficheiro*/
function main(){
	$bigInd;
	if($_FILES['file']['name'][1] == findBigImage()){ 
		$smallBuffer = convertImageToString(0);
		$bigInd = 1;
	}
	else{
		$smallBuffer = convertImageToString(1);
		$bigInd = 0; 
	}
	hideImage($smallBuffer,$bigInd);
	//$smallPhoto  = discoverImage($counter,$ind);
	//returnImage($smallPhoto,$bigInd);
}

function returnImage($img,$ind){
	if($ind == 1){ 
 		$new = fopen("uploads/".$_FILES['file']['name'][0] ,"wb");
	 	fwrite($new,$img); 
	}
	else{ 
 		$new = fopen("uploads/".$_FILES['file']['name'][1] ,"wb");
	 	fwrite($new,$img); 
	}
}


function discoverImage($counter){

	$img = file_get_contents("uploads/imagemEscondida.png");    //img vai ser uma string contendo image data
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
			
			if($cont == $counter) break 2;
		} 
	}

	//echo $answ;
	return generateHiddenImage($answ);
}

$cont = 0;
function discoverString($anws,$argb){

	echo $GLOBALS['cont'].": ".decbin($argb['alpha'])."-".decbin($argb['red'])."-".decbin($argb['green'])."-".decbin($argb['blue'])."<br>";

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



function getHiddenImage($img){
	imagepng($img,"uploads/imagemEscondida.png"); 
    $buffer = file_get_contents("uploads/imagemEscondida.png");
 	$length = filesize("uploads/imagemEscondida.png");


	if (!$buffer || !$length) {
	  die("Reading error\n");
	}

	$_buffer = '';
	for ($i = 0; $i < $length; $i++) {
	   $_buffer .= sprintf("%08b", ord($buffer[$i]));
	}
	return $_buffer;     //Devolve a string binaria da imagem pequena apenas com 0s e 1s.

}




//4
function hideImage($smallBuffer,$bigInd){

	$counter = 0;

	$img = file_get_contents( $_FILES['file']['tmp_name'][$bigInd] );  //img vai ser uma string contendo image data
	$bigImg = imagecreatefromstring( $img );                          //$image vai ter a imagem e asua extensao automaticamente

	$width  = imagesx ($bigImg);
	$height = imagesy ($bigImg);

	imagealphablending($bigImg, false);
	imagesavealpha($bigImg, true);
	
	for($x = 0; $x < $width; $x++){
		for($y = 0; $y < $height; $y++){

			$octet = substr($smallBuffer, 0, 8);
			$smallBuffer=substr($smallBuffer,8);

       		if($octet ==""){ 
       			echo "<br> vi ".$counter ."pixels";
       			echo "<br> Escondi tudo<br>";
       			break 2;
       		}

	     	$rgb  = imagecolorat($bigImg, $x, $y);
			$argb = imagecolorsforindex($bigImg, $rgb);	
			$newPixel = hideOctet($argb,$octet,$bigImg);
			imagesetpixel($bigImg, $x, $y, $newPixel);

			$counter++;

		} 
	}


	$name = "sevetse".$counter.".png";

	imagepng($bigImg,"uploads/".$name);


	//Setting header
	header('Content-Description: File Transfer');
	header('Content-Type: image/png');
	header("Content-Disposition: attachment; filename=\"" . basename($name) . "\";");
	header('Content-Transfer-Encoding: binary');
	ob_clean();
	flush();
	readfile("uploads/".$name);                             //Sends file back to the client.
	exit;    

}

function printPixelBinary($argb){
//	echo "PIXEL_VALUE:".decbin($argb['alpha'])."-".decbin($argb['red'])."-".decbin($argb['green'])."-".decbin($argb['blue'])."<br>";
}

function hideOctet($argb, $octet,$img){

	//esconde no alpha
	$length = 7 - strlen(decbin($argb['alpha']));
	if($length != 0) $compBin = reparaString(decbin($argb['alpha']),$length);
	else $compBin = decbin($argb['alpha']);
	$result  = substr($octet, 0, 4);   //Obtenho primeiros 4 digitos do octeto a esconder.
	$compBin = substr($compBin,0,3);   //Obtenho primeiros 3 digitos(mais significativos) donde vou esconder.
	$alpha = $compBin . $result;
	//if(strlen($alpha) != 7 ) echo "len negativa  <br>";

	//esconde no red
	$length = 8 - strlen(decbin($argb['red']));

	if($length != 0) $compBin = reparaString(decbin($argb['red']),$length);
	else $compBin = decbin($argb['red']);

	$result  = $octet[4];              //Obtenho o quarto digito do octeto a esconder.
	$compBin = substr($compBin,0,7);   //Obtenho primeiros 7 digitos(mais significativos) donde vou esconder.
	$red = $compBin . $result;
	//if(strlen($red) != 8 ) echo "len negativa  <br>";

	//esconde no red
	$length = 8 - strlen(decbin($argb['green']));

	if($length != 0) $compBin = reparaString(decbin($argb['green']),$length);
	else $compBin = decbin($argb['green']);

	$result  = $octet[5];              //Obtenho o quinto digito do octeto a esconder.
	$compBin = substr($compBin,0,7);   //Obtenho primeiros 7 digitos(mais significativos) donde vou esconder.
	$green = $compBin . $result;

	//esconde no blue
	$length = 8 - strlen(decbin($argb['blue']));
	if($length != 0) $compBin = reparaString(decbin($argb['blue']),$length);
	else $compBin = decbin($argb['blue']);

	$result  = $octet[6].$octet[7];    //Obtenho o quinto digito do octeto a esconder.
	$compBin = substr($compBin,0,6);   //Obtenho primeiros 6 digitos(mais significativos) donde vou esconder.
	$blue = $compBin . $result;

	return  imagecolorallocatealpha($img,bindec($red),bindec($green),bindec($blue),bindec($alpha));
}


/*Se a string nao tiver 8 digitos esta funcao repara.*/
function reparaString($string,$length){

	$reparo = "";
	for($x=0; $x < $length; $x++){
		$reparo= $reparo ."0";
	}

	$res = $reparo . $string;
	return $res;
}



function debug($argb){
//	echo "alpha: ".$argb['alpha']. " red: ".$argb['red']. " green: ".$argb['green']. " blue: ".$argb['blue']."<br>"; 
//	echo decbin($argb['alpha']) ;
}




//3
/*Converte uma imagem para um binary string*/
function convertImageToString($ind){
    $buffer = file_get_contents($_FILES["file"]["tmp_name"][$ind]);
 	$length = filesize($_FILES["file"]["tmp_name"][$ind]);


	if (!$buffer || !$length) {
	  die("Reading error\n");
	}

	$_buffer = '';
	for ($i = 0; $i < $length; $i++) {
	   $_buffer .= sprintf("%08b", ord($buffer[$i]));
	}
	return $_buffer;     //Devolve a string binaria da imagem pequena apenas com 0s e 1s.

}

function generateHiddenImage($buffer){
	
	 $nb = "";
	 for($i = 0;$i<strlen($buffer)/8;$i++) {
	     $sub = substr($buffer, $i*8,8);
	     $nb .= chr(bindec($sub));
	 }

	 return $nb;
}
	 



//2
/*Given 2 images as input finds the one which has more pixels and returns its name. */
function findBigImage(){
	$filename = imagecreatefromstring(file_get_contents($_FILES['file']['tmp_name'][0]));
	$width  = imagesx ($filename);
	$height = imagesy ($filename);
	$firstSize = $width * $height;
	
	$filename = imagecreatefromstring(file_get_contents($_FILES['file']['tmp_name'][1]));
	$width  = imagesx ($filename);
	$height = imagesy ($filename);
	$secondSize = $width * $height;

	if($firstSize > $secondSize) return  $_FILES['file']['name'][0];   //a primeira imagem e maior
	else return $_FILES['file']['name'][1];                            //a segunda imagem e maior
}



?>