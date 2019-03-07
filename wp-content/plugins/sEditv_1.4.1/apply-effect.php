<?php
include('settings.php');
include('inc/functions.php');
$vars=$_POST;
//echo json_encode($vars);
extract($vars);

//status array
$imageStatus=array();

//prevent memory limit when working with big images
ini_set('memory_limit', '-1');
ini_set('display_errors','Off');
/*
Array
(
    [x] => 141 //top left box point start by x os
    [x2] => 335 // bottom right box point by x os
    [y] => 47  //top left box start by y os
    [y2] => 136// bottom right box point by x os
    [w] => 193 // width of selected rectangle
    [h] => 88  // height of selected rectangle
    [imgsrc] => sourceImage.jpg
)
*/
/*
ful path:
http://localhost/projects/jUpload/jUploader2/Version1.3.2.T/test/bulls.jpg

$path_parts = pathinfo('/www/htdocs/inc/lib.inc.php');

echo $path_parts['dirname'], "\n"; http://localhost/projects/jUpload/jUploader2/Version1.3.2.T/test
echo $path_parts['basename'], "\n"; bulls.jpg
echo $path_parts['extension'], "\n";  jpg
echo $path_parts['filename'], "\n"; // since PHP 5.2.0  bulls

*/
$removeQuestionMark=explode("?", $imgsrc);


$path_parts = pathinfo($removeQuestionMark[0]);

//print_r($path_parts);exit;



//create new unique name for temp image
if($srcImagevar=='temp'){
	$unique=new_name();
	$uniqueImageName=$unique.'.'.$path_parts['extension'];
	$imageStatus['uniqueImageName']=$uniqueImageName;
	$imageStatus['status'] ='OK';
	$tempFolder = $settingsValue['tempFolder'];
	$sourceImage=$tempFolder.'/'.$uniqueImageName; //'eff'+scrImageOrig ==> eff_sourceImage.jpg
}else{
	$sourceImage=$srcImagevar;
	$currentImageArray=explode('/', $sourceImage);
	$imageStatus['uniqueImageName']=$currentImageArray[1];
	$imageStatus['status'] ='OK';
	$tempFolder = $settingsValue['tempFolder'];
}
 
/* ++++++++++++++++++++++++ get image infos++++++++++++++++++++++++ */

// here we are going to create a copy of original image
// original image will be preserved for revert button
// and all effect will be applied on newly created image

//get original image name 
$origImage=$imgsrc;

//if this is first applied effect create a copy of original image with prefix eff_
if(!file_exists($sourceImage))
{ 
	if(!copy($origImage,$sourceImage))
		{
			//echo "CanNotCreateImage";
			$imageStatus['status'] ='CanNotCreateImage';
			echo json_encode($imageStatus);
			exit;
		}

}

/*++++++++++++++++++++++++ get extension++++++++++++++++++++++++ */
 $srcExt = strtolower('.'.$path_parts['extension']);
 $srcExt=str_replace(' ','', $srcExt);
/*++++++++++++++++++++++++ get extension++++++++++++++++++++++++ */

/*++++++++++++++++++++++++ /get image infos++++++++++++++++++++++++ */

/*get width and height*/
list($width, $height) = getimagesize($sourceImage);
$width=$widthCurrentImage;
$height=$heightCurrentImage;

/*get selected color*/
$valueArray=explode(',',$color);//split color by coma


/*create image from (based on extension)*/
if ($srcExt == ".jpg" || $srcExt == ".jpeg"){ 
	 
	 $image = imagecreatefromjpeg($sourceImage);
    }
 
$dst_image = imagecreatetruecolor( $w, $h );//create new image from selection

/*
//preserve transparency for transparent gif and png images
if($srcExt ==".png"){ 
	imagealphablending($dst_image, false);
 	imagesavealpha($dst_image, true);
}
if($srcExt ==".gif"){ 
   	imagecolortransparent($dst_image, imagecolorallocatealpha($dst_image, 0, 0, 0, 127));
	imagealphablending($dst_image, false);
	imagesavealpha($dst_image, true);
    }
*/
imagecopyresampled($dst_image, $image,0,0,$x,$y,$w,$h,$w,$h);

/*choose circle instead of square*/
if($typeOfSelection=='circle' && $effect != 'Text' && $effect != 'Crop'){
	$dst_image =createRoundselection($sourceImage,$dst_image,$x,$y,$x2,$y2,$w,$h);

}

//imagegif($dst_image,'NULL.gif');
//imagepng($dst_image,'NULL.png');die();
if($effectCategory=='simplesEffects'){
	switch ($effect){
		case 'Pixelate':
		 	imagefilter($dst_image, IMG_FILTER_PIXELATE,$slider);/*pixelate new image*/
		 	imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,100); 
		break; 

		case 'Pixelate-out':
		 	imagefilter($image, IMG_FILTER_PIXELATE,$slider);/*pixelate new image*/
		 	imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,100); 
		break;

		case 'Negative':
		 	 imagefilter($dst_image, IMG_FILTER_NEGATE); 
		 	 if($typeOfSelection=='circle'){
		 		createRoundselection($sourceImage,$dst_image,$x,$y,$x2,$y2,$w,$h);
		 	}
		 	 imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,100); 
		break;

		case 'Greyscale':
		imagefilter($dst_image, IMG_FILTER_GRAYSCALE);/*grayscale selected part of image*/
		if($typeOfSelection=='circle'){
		 		createRoundselection($sourceImage,$dst_image,$x,$y,$x2,$y2,$w,$h);
		 	}
		 	imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,100); 
		break; 

		case 'Greyscale-out'://problem sa circle selection
		 	 imagefilter($image, IMG_FILTER_GRAYSCALE);/*grayscale selected part of image*/
 			 imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,100); 
		break;

		
		case 'Blur':
		 	if($typeOfSelection=='circle'){
			for ($i=0; $i < $slider; $i++) { 
				imagefilter($dst_image, IMG_FILTER_SELECTIVE_BLUR); 
			}
			
		 		createRoundselection($sourceImage,$dst_image,$x,$y,$x2,$y2,$w,$h);
		 	}else{
		 		for ($i=0; $i < $slider; $i++) { 
		 		imagefilter($dst_image, IMG_FILTER_GAUSSIAN_BLUR); 
		 		}
		 	}
			imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,100); 
			 
		break;
		case 'Blur-out':
		for ($i=0; $i < $slider; $i++) {
		 	 imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR); 
		 	}
		 	  imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,100); 
		break;

		case 'Brightness':
			
		 	 imagefilter($dst_image, IMG_FILTER_BRIGHTNESS, $slider);
			 	if($typeOfSelection=='circle'){
			 		createRoundselection($sourceImage,$dst_image,$x,$y,$x2,$y2,$w,$h);
			 	}
		 	 imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,100); 
		break;

		case 'Contrast':
		 	 imagefilter($dst_image, IMG_FILTER_CONTRAST, -$slider); 
		 	 //createRoundselection($sourceImage,$dst_image,$x,$y,$x2,$y2,$w,$h);
		 	 imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,100); 
		break;

		case 'Colorize':
		     //imagefilter($dst_image, IMG_FILTER_GRAYSCALE, $slider);
       		 imagefilter($dst_image, IMG_FILTER_COLORIZE, $valueArray[0], $valueArray[1], $valueArray[2]);
       		 if($typeOfSelection=='circle'){
		 		createRoundselection($sourceImage,$dst_image,$x,$y,$x2,$y2,$w,$h);
		 	}
        	 imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,$slider); 
		break;
			
		case 'Emboss':

         	 //imagefilter($dst_image, IMG_FILTER_EDGEDETECT);
         	 imagefilter($dst_image, IMG_FILTER_EMBOSS);
	        	 if($typeOfSelection=='circle'){
			 		createRoundselection($sourceImage,$dst_image,$x,$y,$x2,$y2,$w,$h);
			 	}
		 	imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,100);
		 	break;
		case 'Text':

			$white = imagecolorallocate($dst_image, $valueArray[0], $valueArray[1], $valueArray[2]); // white text
			imagettftext($dst_image, $slider, 0, 0, $slider, $white, $settingsValue['font'], $currentTextValue);
		 	imagecopy($image, $dst_image, $x,$y,0,0,$w,$h);
		 break;
           
        case 'Crop':
        	imagejpeg($dst_image, $sourceImage,100);
        	//imagejpeg($image, $sourceImage,100);
        break;

		default:
         
			die(json_encode($imageStatus));
        break;
        
        

    }/*end switch*/
}/* end if($effectCategory=='simplesEffects')*/
elseif($effectCategory=='compiledsEffects'){
	switch ($effect) {
		case 'Rosie':
			//contrast
			imagefilter($dst_image, IMG_FILTER_CONTRAST, -30); 
		 	imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,100);

		 	//colorize
		 	imagefilter($dst_image, IMG_FILTER_COLORIZE, 45,45,190);//blue color
       		imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,40); 

       		imagejpeg($image, $sourceImage,100);

       		//create current folder var &current image var
	        $curentFolder='web_images/compiledEffects/';
	        
	        //effect is Round
	        $curentImage='Round.png';
	        //assign name for temp image
	        $tempaname=new_name();
	        $tempImage=$tempFolder.'/temp_'.$tempaname.'.png';

	        //color values
	        $color1=0;
	        $color2=0;
	        $color3=0;
	        $slider=60;
	        $noColor='Off';

	        // call function apply effect from folder
	        applyEffectFromFolder($sourceImage,$curentFolder,$curentImage,$tempImage,$x,$y,$w, $h,$slider,$noColor,$color1,$color2,$color3);
	        die(json_encode($imageStatus));

			break;

			case 'FilmFrame':
			//contrast
			imagefilter($dst_image, IMG_FILTER_CONTRAST, -23); 
		 	imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,100);

		 	//colorize
		 	imagefilter($dst_image, IMG_FILTER_COLORIZE, 196,155,32);//blue color
       		imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,23); 

       		imagejpeg($image, $sourceImage,100);

       		//create current folder var &current image var
	        $curentFolder='web_images/compiledEffects/';
	        
	        //effect is Round
	        $curentImage='Film.png';
	        //assign name for temp image
	        $tempaname=new_name();
	        $tempImage=$tempFolder.'/temp_'.$tempaname.'.png';

	        //color values
	        $color1=0;
	        $color2=0;
	        $color3=0;
	        $slider=100;
	        $noColor='Off';

	        // call function apply effect from folder
	        applyEffectFromFolder($sourceImage,$curentFolder,$curentImage,$tempImage,$x,$y,$w, $h,$slider,$noColor,$color1,$color2,$color3);
	        die(json_encode($imageStatus));

			break;

			case 'Patty':
			//contrast
			imagefilter($dst_image, IMG_FILTER_CONTRAST, -20); 
		 	imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,100);

		 	//colorize
		 	imagefilter($dst_image, IMG_FILTER_COLORIZE, 152,32,149);//blue color
       		imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,40); 


			break;

			case 'Sonny':
			//contrast
			imagefilter($dst_image, IMG_FILTER_CONTRAST, -40); 
		 	imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,100);

		 	//colorize
		 	imagefilter($dst_image, IMG_FILTER_COLORIZE, 220,140,41);//blue color
       		imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,40); 


			break;

			case 'Oddie':
			//contrast
			imagefilter($dst_image, IMG_FILTER_CONTRAST, -30); 
		 	imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,100);

		 	//grayscale
		 	for($i=0;$i<3;$i++){
		 		imagefilter($dst_image, IMG_FILTER_GRAYSCALE);
		 		imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,100); 
		 	}
		 	
		 	//colorize
		 	imagefilter($dst_image, IMG_FILTER_COLORIZE, 148,138,24);//blue color
       		imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,30); 
			
			imagejpeg($image, $sourceImage,100);
			//create current folder var &current image var
	        $curentFolder='web_images/compiledEffects/';
	        
	        //effect is Round
	        $curentImage='Rusty.png';
	        //assign name for temp image
	        $tempaname=new_name();
	        $tempImage=$tempFolder.'/temp_'.$tempaname.'.png';

	        //color values
	        $color1=255;
	        $color2=255;
	        $color3=255;
	        $slider=100;
	        $noColor='On';

	        // call function apply effect from folder
	        applyEffectFromFolder($sourceImage,$curentFolder,$curentImage,$tempImage,$x,$y,$w, $h,$slider,$noColor,$color1,$color2,$color3);
	        die(json_encode($imageStatus));

			break;

			case 'Juliette':
			//contrast
			imagefilter($dst_image, IMG_FILTER_CONTRAST, -30); 
		 	imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,100);

		 	//grayscale
		 	imagefilter($dst_image, IMG_FILTER_GRAYSCALE);
		 	imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,100);
		 	 
       		//colorize
		 	imagefilter($dst_image, IMG_FILTER_COLORIZE, 16,61,199);//blue color
       		imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,30);
       		
		 	imagejpeg($image, $sourceImage,100);


			break;
		
		default:
			# code...
			break;
	}
}
	else{ /* if is not simple and precompiled then is from folder*/
 		
 		//create current folder var &current image var
        $curentFolder=$settingsValue['effectsFolder'].'/'.$effectCategory.'/';
        
        //get selected effect
        $curentImage=$effect.'.png';
        //assign name for temp image
        $tempaname=new_name();
	        $tempImage=$tempFolder.'/temp_'.$tempaname.'.png';

        //color values
        $color1=$valueArray[0];
        $color2= $valueArray[1];
        $color3=$valueArray[2];

        // call function apply effect from folder
        applyEffectFromFolder($sourceImage,$curentFolder,$curentImage,$tempImage,$x,$y,$w, $h,$slider,$noColor,$color1,$color2,$color3);
         die(json_encode($imageStatus));

        
	}/* end else form folder*/
 
//if effect is not crop then use this because crop is creating image based on selection
 if($effect != 'Crop'){
 	imagejpeg($image, $sourceImage,100);
 }
 
 echo json_encode($imageStatus);

?>