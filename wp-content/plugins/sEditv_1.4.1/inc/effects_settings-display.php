<?php  

	
	$posted=$_POST;
	extract($posted);

	$searchInDir="../effects/".$sEditFolderToSearchIn;

	$files = glob( $searchInDir . '/*.png');
	$imageNameOnly=array();
	foreach ($files as $var) {
		 $splitImage=end(explode('/',$var));
		 $imageNameOnly[]=$splitImage;
	}

	echo json_encode($imageNameOnly);exit;
	
?>