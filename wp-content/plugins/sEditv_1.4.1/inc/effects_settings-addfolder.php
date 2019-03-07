<?php 

	$posted=$_POST;
	extract($posted);
	
	$newDir="../effects/".$folderToAdd;
	
	//if this directory does not exist create it, otherwise throw an "exists" parameter
	if (file_exists($newDir)) {
    	echo json_encode("exists");
	} else {
	    if(mkdir($newDir))
		{
			echo json_encode("success");
		}else{
			echo json_encode("error");
		}
	}

	

	



 ?>