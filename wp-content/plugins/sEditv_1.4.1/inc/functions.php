<?php 

//sEdit admin css
function sEdit_admin_css(){
      wp_register_style('sEditAdmin-style', plugin_dir_url(dirname(__FILE__)).'css/sEdit_admin_style.css');
     wp_enqueue_style('sEditAdmin-style'); 
global $wp_version;
     wp_localize_script(
                'sEditorAttachButtons', 
                'sEditVars', array(
                    'sEdit_pluginurl'           => plugin_dir_url(dirname(__FILE__)),
                    'sEdit_WP_version'          => $wp_version
                    ));
        
    }


function sEdit_attach_sEdit_buttons_scripts(){
    wp_register_script('sEditorAttachButtons', plugin_dir_url(dirname(__FILE__)).'js/sEditInsertButtons.js');
    wp_enqueue_script('sEditorAttachButtons');
}



/*include css and javascript*/

function sEdit_load_admin_scripts(){

 	//css
 	
 	 wp_register_style('sEditColorPicker-style', plugin_dir_url(dirname(__FILE__)).'css/colorpicker.css');
     wp_enqueue_style('sEditColorPicker-style');

     wp_register_style('sEditHoverscroll-style', plugin_dir_url(dirname(__FILE__)).'css/jquery.hoverscroll.css');
     wp_enqueue_style('sEditHoverscroll-style'); 

     wp_register_style('sEditJcrop-style', plugin_dir_url(dirname(__FILE__)).'css/jquery.Jcrop.css');
     wp_enqueue_style('sEditJcrop-style');

     wp_register_style('sEditLayout-style', plugin_dir_url(dirname(__FILE__)).'css/layout.css');
     wp_enqueue_style('sEditLayout-style');

     wp_register_style('sEditorStyle-style', plugin_dir_url(dirname(__FILE__)).'css/sEditorStyle.css');
     wp_enqueue_style('sEditorStyle-style');

    //javascript
 	 wp_register_script('sEditcolorPicker', plugin_dir_url(dirname(__FILE__)).'js/colorpicker.js');
     wp_enqueue_script('sEditcolorPicker');

     wp_register_script('sEditeye', plugin_dir_url(dirname(__FILE__)).'js/eye.js');
     wp_enqueue_script('sEditeye');

     wp_register_script('sEditjqueryCrop', plugin_dir_url(dirname(__FILE__)).'js/jquery.Jcrop.min.js');
     wp_enqueue_script('sEditjqueryCrop');

     wp_register_script('sEditScrollingCarousel', plugin_dir_url(dirname(__FILE__)).'js/scrollingcarousel.2.0.min.js');
     wp_enqueue_script('sEditScrollingCarousel');

     wp_register_script('sEditUtils', plugin_dir_url(dirname(__FILE__)).'js/utils.js');
     wp_enqueue_script('sEditUtils');

     wp_register_script('sEditor', plugin_dir_url(dirname(__FILE__)).'js/sEditor.js');
     wp_enqueue_script('sEditor');


     //send vars to sEditor.js
     	 wp_localize_script(
			 	'sEditor', 
			 	'sEditVars', array(
			 		'sEdit_pluginurl'    		=> plugin_dir_url(dirname(__FILE__)),
                    'sEdit_WP_version'          => $wp_version
			 		));


 
 }/* /sEdit_load_scripts */


function sEdit_register_jquery_only(){

    wp_register_style('sEditColorPicker-jquery-ui-style', plugin_dir_url(dirname(__FILE__)).'css/ui-lightness/jquery-ui-1.8.21.custom.css');
     wp_enqueue_style('sEditColorPicker-jquery-ui-style');
       
     wp_enqueue_script('jquery');
     wp_enqueue_style('jquery-sEdit-style', 'http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css');
     wp_enqueue_script('jqueryUI-sEdit', 'http://code.jquery.com/ui/1.9.1/jquery-ui.js');
    }




//load admin settings scripts
    function sEdit_load_admin_settings_scripts()
    {
        wp_register_script('sEditorSettings', plugin_dir_url(dirname(__FILE__)).'js/sEditSettings.js');
        wp_enqueue_script('sEditorSettings');

         //send vars to sEditSettings.js
         wp_localize_script(
                'sEditorSettings', 
                'sEditorSettingsVars', array(
                    'sEdit_pluginurl'           => plugin_dir_url(dirname(__FILE__))
                    ));
    }

 
 /*++++++++++++++++++++++++++++++++ Relative paths ++++++++++++++++++++++++++++++++++++++++++++++++*/
 // get relative paths
 function getRelativePath($from, $to)
    {
       $from = explode('/', $from);
       $to = explode('/', $to);
       foreach($from as $depth => $dir)
       {

            if(isset($to[$depth]))
            {
                if($dir === $to[$depth])
                {
                   unset($to[$depth]);
                   unset($from[$depth]);
                }
                else
                {
                   break;
                }
            }
        }
        //$rawresult = implode('/', $to);
        for($i=0;$i<count($from)-1;$i++)
        {
            array_unshift($to,'..');
        }
        $result = implode('/', $to);
        return $result;
    }


 /*++++++++++++++++++++++++++++++ / Relative paths ++++++++++++++++++++++++++++++++++++++++++++++++*/
 
 
 
 /*+++++++++++++++++++++++++++++++++++++++++ sEditor needed funcs ++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
 	

/* ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
+	serach multidimensional array and return parent id
+	usage:
+	$id=searchForId('SearchTerm',$arrayName); // call function
+	echo "result: ".$id; // id of parent array
+
+	print_r($arrayName[$id]); print array from multidimensional array with founded id+
 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

function searchForId($id, $array) {
	   foreach ($array as $key => $val) {
	       if ($val['value'] === $id) {
	           return $key;
	       }
	   }
	   return null;
}

/* colorize dark png template*/
function colorizePngImage($image,$r,$g,$b){
			$image2 = imagecreatefrompng($image);
			imagefilter($image2, IMG_FILTER_COLORIZE, $r,$g,$b,0);
			imagesavealpha($image2, true);
            imagepng($image2, $image);
}

/*add watermark*/
function watermarkImage($sourcefile, $watermarkfile, $xPos, $yPos,$opacity) {


		        //Get the resource ids of the pictures
		        $watermarkfile_id = imagecreatefrompng($watermarkfile);

		        imageAlphaBlending($watermarkfile_id, false);
		        imageSaveAlpha($watermarkfile_id, true);

		        $extension=explode('.',$sourcefile);
		        $countElem=count($extension)-1;
		        $fileType=$extension[$countElem];
		        

		        switch ($fileType) {
		            case('gif'):
		                $sourcefile_id = imagecreatefromgif($sourcefile);
		                break;

		            case('png'):
		                $sourcefile_id = imagecreatefrompng($sourcefile);
		                break;

		            default:
		                $sourcefile_id = imagecreatefromjpeg($sourcefile);
		        }
		        $marge_right = 10;
		        $marge_bottom = 10;
		        //Get the sizes of both pix  
		        $sourcefile_width = imageSX($sourcefile_id);
		        $sourcefile_height = imageSY($sourcefile_id);
		        $watermarkfile_width = imageSX($watermarkfile_id);
		        $watermarkfile_height = imageSY($watermarkfile_id);

		       //echo $sourcefile.'-'.$watermarkfile.'-'.$xPos.'-'.$yPos.'-0-0-'.$watermarkfile_width.'-'.$watermarkfile_height.'-'.$opacity;

		        // if a gif, we have to upsample it to a truecolor image
		        if ($fileType == 'gif') {
		            // create an empty truecolor container
		            //$tempimage = imagecreatetruecolor($sourcefile_width, $sourcefile_height);
		            $tempimage=imageCreateTransparent($sourcefile_width, $sourcefile_height);
		           
		            // copy the 8-bit gif into the truecolor image
		            imagecopy($tempimage, $sourcefile_id, 0, 0, 0, 0, $sourcefile_width, $sourcefile_height);
		            //imagepng($watermarkfile_id, 'testtransparent.png');
		            //imagegif($tempimage, 'testtransparent.gif');
		            
		            // copy the source_id int
		            //$sourcefile_id = $tempimage;

		        }
		        if ($fileType == 'png' ) {
		            imagealphablending($sourcefile_id, true);
		            imagesavealpha($sourcefile_id, true);
		            imagecopy($sourcefile_id, $watermarkfile_id, $xPos, $yPos, 0, 0, $watermarkfile_width, $watermarkfile_height);
		        } 
		        else{
		        	 imagecopymerge_alpha($sourcefile_id, $watermarkfile_id, $xPos,$yPos,0,0,$watermarkfile_width,$watermarkfile_height,$opacity);
		            // for jpg imagecopy($sourcefile_id, $watermarkfile_id, $xPos, $yPos, 0, 0, $watermarkfile_width, $watermarkfile_height);

		        }
		        //imagecopy($sourcefile_id, $watermarkfile_id, $xPos, $yPos, 0, 0, $watermarkfile_width, $watermarkfile_height);
		        //imagecopymerge($sourcefile_id, $watermarkfile_id, $xPos,$yPos,0,0,$watermarkfile_width,$watermarkfile_height,$opacity);
		        //imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct);
		        //echo $sourcefile_id.'-'.$watermarkfile_id.'-'.$xPos.'-'.$yPos.'-0-0-'.$watermarkfile_width.'-'.$watermarkfile_height.'-'.$opacity;


		        if ($fileType == "png") {
		            imagepng($sourcefile_id, $sourcefile);
		        } else if ($fileType == "gif") {
		            imagegif($sourcefile_id, $sourcefile);
		        } else {
		            imagejpeg($sourcefile_id, $sourcefile, 100);
		        }

		        imagedestroy($sourcefile_id);
		        imagedestroy($watermarkfile_id);
		    }

/*create transparent instead of black background*/
function imageCreateTransparent($x, $y) {
$imageOut = imagecreate($x, $y);
$colourBlack = imagecolorallocate($imageOut, 0, 0, 0);
imagecolortransparent($imageOut, $colourBlack);
return $imageOut;
	}

/*resize watermark*/
function resizeImage($srcImage,$newImage,$newwidth, $newheight){
		list($width, $height) = getimagesize($srcImage);
		$thumb = imagecreatetruecolor($newwidth, $newheight);
	 	imagealphablending($thumb, false);
     	imagesavealpha($thumb,true);
 		$source = imagecreatefrompng($srcImage);
 		imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
 		imagepng($thumb, $newImage);
	}

/*imagecopy merge alpha problem fix*/
function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){
    // creating a cut resource
    $cut = imagecreatetruecolor($src_w, $src_h);

    // copying relevant section from background to the cut resource
    imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);

    // copying relevant section from watermark to the cut resource
    imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);

    // insert cut resource to destination image
    imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
} 

//select round part of image instead of block
function createRoundselection($sourceImage,$dst_image,$x,$y,$x2,$y2,$w,$h){

	list($widthCurrentImage, $heightCurrentImage) = getimagesize($sourceImage);
	$width=$widthCurrentImage;
	$height=$heightCurrentImage;

	$copy_magicpink = imagecolorallocate($dst_image, 255, 0, 255);  
	imagecolortransparent($dst_image, $copy_magicpink);  

    // 3. Create the mask  
	$mask = imagecreatetruecolor($w, $h);  
	imagealphablending($mask, true);  

    // 3-1. Set the masking colours  
	$mask_black = imagecolorallocate($mask, 0, 0, 0);  
	imagecolortransparent($mask, $mask_black);

	$mask_magicpink = imagecolorallocate($mask, 255, 0, 255);  
	imagefill($mask, 0, 0, $mask_magicpink);  

    // 3-2. Draw the circle for the mask  
	$circle_x = $w/2;  
	$circle_y = $h/2;  
	$circle_w = $w-2;  
	$circle_h = $h-2;  
	imagefilledellipse($mask, $circle_x, $circle_y, $circle_w, $circle_h, $mask_black);  

    // 4. Copy the mask over the top of the copied image, and apply the mask as an alpha layer  
	imagecopymerge($dst_image, $mask, 0, 0, 0, 0, $w, $h, 100);  
	
	return($dst_image);

}

/*merge images for some egffects in swich/case part*/
function mergeImages($srcExt,$image,$dst_image, $x,$y,$w,$h,$slider){

		 		if($srcExt=='.png' ){imagecopy($image, $dst_image, $x,$y,0,0,$w,$h);}
		 		if($srcExt=='.jpg' || $srcExt=='.jpeg'){imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,$slider); }
		 		if($srcExt=='.gif'){ imagecopymerge($image, $dst_image, $x,$y,0,0,$w,$h,$slider);}
			return($image);
			}
 
 /* apply effect form folder*/
 function applyEffectFromFolder($sourceImage,$curentFolder,$curentImage,$tempImage,$x,$y,$w, $h,$slider,$noColor,$color1,$color2,$color3){
        	resizeImage($curentFolder.$curentImage, $tempImage,$w, $h);

        	/*if noColor var is on colorize png effect image if not use as is */
        	if($noColor=='On'){
        	 	colorizePngImage($tempImage,$color1,$color2,$color3);
        	 }/*colorize image*/

        	 watermarkImage($sourceImage, $tempImage, $x,$y,$slider);
        	 unlink($tempImage);
        }

 
function new_name() {
    $name = microtime();
    $name = str_replace(".", "", $name);
    $name = str_replace(" ", "", $name);
    $name=  alphanumID($name);
    return $name;
    }

 function alphanumID($in, $to_num = false, $pad_up = false, $passKey = null) {
    $index = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-";
    if ($passKey !== null) {

        for ($n = 0; $n < strlen($index); $n++) {
            $i[] = substr($index, $n, 1);
        }

        $passhash = hash('sha256', $passKey);
        $passhash = (strlen($passhash) < strlen($index)) ? hash('sha512', $passKey) : $passhash;

        for ($n = 0; $n < strlen($index); $n++) {
            $p[] = substr($passhash, $n, 1);
        }

        array_multisort($p, SORT_DESC, $i);
        $index = implode($i);
    }

    $base = strlen($index);

    if ($to_num) {
        // Digital number  <<--  alphabet letter code
        $in = strrev($in);
        $out = 0;
        $len = strlen($in) - 1;
        for ($t = 0; $t <= $len; $t++) {
            $bcpow = bcpow($base, $len - $t);
            $out = $out + strpos($index, substr($in, $t, 1)) * $bcpow;
        }

        if (is_numeric($pad_up)) {
            $pad_up--;
            if ($pad_up > 0) {
                $out -= pow($base, $pad_up);
            }
        }
        $out = sprintf('%F', $out);
        $out = substr($out, 0, strpos($out, '.'));
    } else {
        // Digital number  -->>  alphabet letter code
        if (is_numeric($pad_up)) {
            $pad_up--;
            if ($pad_up > 0) {
                $in += pow($base, $pad_up);
            }
        }

        $out = "";
        for ($t = floor(log($in, $base)); $t >= 0; $t--) {
            $bcp = bcpow($base, $t);
            $a = floor($in / $bcp) % $base;
            $out = $out . substr($index, $a, 1);
            $in = $in - ($a * $bcp);
        }
        $out = strrev($out); // reverse
    }

    return $out;
}
 /*+++++++++++++++++++++++++++++++++++++++++ /sEditor needed funcs ++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
 
 
 


