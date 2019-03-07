<?php $us_map_iii = $this->options; ?>

var us_config_iii = {
	'default':{
		'borderclr':'<?php echo $us_map_iii['borderclr']; ?>',
		'visnames':'<?php echo $us_map_iii['visnames']; ?>',
		'lakesfill':'<?php echo $us_map_iii['lakesfill']; ?>',
		'lakesoutline':'<?php echo $us_map_iii['lakesoutline']; ?>'
	}<?php echo (isset($us_map_iii['url_1']))?',':''; ?><?php $i = 1; 	while (isset($us_map_iii['url_'.$i])) { ?>'us_<?php echo $i; ?>':{
		'hover': '<?php echo str_replace(array("\n","\r","\r\n","'"),array('','','','’'), is_array($us_map_iii['info_'.$i]) ?
				array_map('stripslashes_deep', $us_map_iii['info_'.$i]) : stripslashes($us_map_iii['info_'.$i])); ?>',
		'url':'<?php echo $us_map_iii['url_'.$i]; ?>',
		'targt':'<?php echo $us_map_iii['turl_'.$i]; ?>',
		'upclr':'<?php echo $us_map_iii['upclr_'.$i]; ?>',
		'ovrclr':'<?php echo $us_map_iii['ovrclr_'.$i]; ?>',
		'dwnclr':'<?php echo $us_map_iii['dwnclr_'.$i]; ?>',
		'enbl':<?php echo $us_map_iii['enbl_'.$i]=='1'?'true':'false'; ?>,
		'visnames':'us_vn<?php echo $i; ?>',
		}
		<?php echo (isset($us_map_iii['url_'.($i+1)]))?',':''; ?><?php $i++;} ?>
}