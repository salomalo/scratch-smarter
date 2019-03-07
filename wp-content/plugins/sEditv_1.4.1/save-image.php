<?php 

$vars=$_GET;

/*vars:
	[saveOption] => SaveNew /or/ Overwrite
    [scrImageVar] => temp/eff_Mein-Land_large.jpg
    [scrImageOrig] => http://data.whicdn.com.dal.llnw-trials.com/images/28752611/Mein-Land_large.jpg
    [sEditorNewImageName] => Test
*/



/*echo $path_parts['dirname'], "\n"; http://localhost/projects/jUpload/jUploader2/Version1.3.2.T/test
echo $path_parts['basename'], "\n"; bulls.jpg
echo $path_parts['extension'], "\n";  jpg
echo $path_parts['filename'], "\n"; // since PHP 5.2.0  bulls*/
//print_r($vars);
//die();

extract($vars);
$tempImgPath=$scrImageOrig;
$tempImgPath=explode("?", $tempImgPath);
$scrImageOrig=$tempImgPath[0];

$path_parts = pathinfo($scrImageOrig);
$extension=$path_parts['extension'];

$sEditorNewImageName=str_replace(' ', '_', $sEditorNewImageName);

if($saveOption=='SaveNew'){

	/*			'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpe' => 'image/jpeg',
            */
	if(file_exists($scrImageVar))
	{
		header("Content-disposition: attachment; filename={$sEditorNewImageName}.{$extension}");
		header('Content-type: application/octet-stream'); 
		readfile($scrImageVar);
	}else{
		echo "You have to apply some effect to this image first, and after that you can save it!";
	}
}elseif($saveOption=='Overwrite'){

	/*if i am not able to save throw an error*/
	if (!copy($scrImageVar, $scrImageOrig)) {

		//echo $scrImageVar.'------'.$scrImageOrig;

    echo "Error";

	}else{
		echo "Success";
	}/*if i am able to copy*/
}
 ?>