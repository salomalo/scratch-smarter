<?php
// Before removing this file, please verify the PHP ini setting `auto_prepend_file` does not point to this.

if (file_exists('/srv/www/vhosts/www.scratchsmarter.com/wp-content/plugins/wordfence/waf/bootstrap.php')) {
	define("WFWAF_LOG_PATH", '/srv/www/vhosts/www.scratchsmarter.com/wp-content/wflogs/');
	include_once '/srv/www/vhosts/www.scratchsmarter.com/wp-content/plugins/wordfence/waf/bootstrap.php';
}
?>