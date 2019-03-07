<?php
$vars=$_POST;

extract($vars);

/*if actions is delete then delete image used forZ*/
if($action='delete'){
	unlink($scrImageVar);

}




?>