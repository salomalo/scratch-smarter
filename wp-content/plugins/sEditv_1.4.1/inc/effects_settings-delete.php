<?php 
	
	//when directory is not empty form php.net
	function rrmdir($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
		return(true);
	}

	$posted=$_POST;
	extract($posted);
	 //echo json_encode($folderToDelete);
	//rmdir("../effects/".$folderToDelete."/");

	
	if(rrmdir("../effects/".$folderToDelete."/")){
		echo json_encode("success");
		}else{
			echo json_encode("error");
		}

	// if(rmdir("../effects/".$folderToDelete."/")){
	// 	echo json_encode("success");
	// 	}else{
	// 		echo json_encode("error");
	// 	}
 



 ?>