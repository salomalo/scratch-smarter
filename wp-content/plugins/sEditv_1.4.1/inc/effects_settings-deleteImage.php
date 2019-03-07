<?php 

$posted=$_POST;
extract($posted);

// folderToDelete : imageFolder,
// imageToDelete : imageName

$img="../effects/".$folderToDelete."/".$imageToDelete;


if(unlink($img)){
	echo json_encode("success");
}else{
	echo json_encode("error");
}
 ?>