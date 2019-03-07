<?php $us_map_ii = $this->options; ?>

var us_config_ii = {
	'default':{
		'borderclr':'<?php echo $us_map_ii['borderclr']; ?>',
		'visnames':'<?php echo $us_map_ii['visnames']; ?>',
		'lakesfill':'<?php echo $us_map_ii['lakesfill']; ?>',
		'lakesoutline':'<?php echo $us_map_ii['lakesoutline']; ?>'
	}<?php echo (isset($us_map_ii['url_1']))?',':''; ?><?php $i = 1; 	while (isset($us_map_ii['url_'.$i])) { ?>'us_<?php echo $i; ?>':{
		'hover': '<?php echo str_replace(array("\n","\r","\r\n","'"),array('','','','’'), is_array($us_map_ii['info_'.$i]) ?
				array_map('stripslashes_deep', $us_map_ii['info_'.$i]) : stripslashes($us_map_ii['info_'.$i])); ?>',
		'url':'<?php echo $us_map_ii['url_'.$i]; ?>',
		'targt':'<?php echo $us_map_ii['turl_'.$i]; ?>',
		'upclr':'<?php echo $us_map_ii['upclr_'.$i]; ?>',
		'ovrclr':'<?php echo $us_map_ii['ovrclr_'.$i]; ?>',
		'dwnclr':'<?php echo $us_map_ii['dwnclr_'.$i]; ?>',
		'enbl':<?php echo $us_map_ii['enbl_'.$i]=='1'?'true':'false'; ?>,
		'visnames':'us_vn<?php echo $i; ?>',
		}
		<?php echo (isset($us_map_ii['url_'.($i+1)]))?',':''; ?><?php $i++;} ?>
}