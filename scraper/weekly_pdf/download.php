<?php
if(get_magic_quotes_gpc())
{
	$_GET['state'] = stripslashes($_GET['state']);
	$_GET['free'] = stripslashes($_GET['free']);
}

$match = false;
if ( isset( $_GET['free'] ) )
{
	if( $_GET['free'] == '1' )
	{
		$state = strtoupper($_GET['state']);
		
		// determine the full pdf report file name given the state name
		
			$array_dir_files = array_diff( scandir(getcwd()), array('..', '.') );
			foreach( $array_dir_files as $str_file )
			{
				// This expression matches all 'state' file names, starting with the oldest to the most recent.
				// (The most recent file name will be the last matched, replacing all previous matches; hence, the one used.)
				if( preg_match('%BGTP_'.$state.'_\d+_\d+_'.date('y').'_Free\.pdf%', $str_file, $array_match) )
				{
					$file = $array_match[0];
					$match = true;
				}
			}
			
			// If applicable, we need the second expression here because, sometimes, at the turn of the year, the latest report for the new year won't be available and the date() above will yield a year string that matches to no file names. Hence, we need the second generic expression to catch the latest report of the previous year.
			if( $match == false )
			{
				foreach( $array_dir_files as $str_file )
				{
					if( preg_match('%BGTP_'.$state.'_\d+_\d+_\d{2}_Free\.pdf%', $str_file, $array_match) )
						$file = $array_match[0];
				}			
			}
	}
}
else
{
	$state = strtoupper($_GET['state']);
	
	// determine the full pdf report file name given the state name
	
		$array_dir_files = array_diff( scandir(getcwd()), array('..', '.') );
		foreach( $array_dir_files as $str_file )
		{
			// This expression matches all 'state' file names, starting with the oldest to the most recent.
			// (The most recent file name will be the last matched, replacing all previous matches; hence, the one used.)
			if( preg_match('%BGTP_'.$state.'_\d+_\d+_'.date('y').'\.pdf%', $str_file, $array_match) )
			{
				$file = $array_match[0];
				$match = true;
			}
		}
		
		// If applicable, we need the second expression here because, sometimes, at the turn of the year, the latest report for the new year won't be available and the date() above will yield a year string that matches to no file names. Hence, we need the second generic expression to catch the latest report of the previous year.
		if( $match == false )
		{
			foreach( $array_dir_files as $str_file )
			{
				if( preg_match('%BGTP_'.$state.'_\d+_\d+_\d{2}\.pdf%', $str_file, $array_match) )
					$file = $array_match[0];
			}			
		}
}

if (file_exists($file))
{
	header('Content-Description: File Transfer');
	header("Content-Type: application/pdf");
		// header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.basename($file));
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: '.filesize($file));
	
    ob_clean();
    flush();
	
    readfile($file);
	
    exit;
}
else
	echo "no file exists: $file"."<br />\n";

?>