<?php
/*
Plugin Name: US MAP III
Plugin URI: http://www.wpmapplugins.com/
Description: Customize each state (colors, link, etc) through the admin panel and use the shortcode in your page.
Version: 2.2.1
Author: WP Map Plugins
Author URI: http://www.wpmapplugins.com/
*/

class US_Map_III {

	public function __construct(){
		$this->constant();
		$this->options = get_option( 'us_map_iii' );
		add_action( 'admin_menu', array($this, 'us_map_iii_options_page') );
	 	add_action( 'admin_footer', array( $this,'add_js_to_wp_footer') );
	 	add_action( 'wp_footer', array($this,'add_span_tag') );
		add_action( 'admin_enqueue_scripts', array($this,'init_admin_script') );
		add_shortcode( 'us_map_iii', array($this, 'us_map_iii') );
		$this->default = array(
			'borderclr' => '#6B8B9E',
			'visnames' => '#666666',
			'lakesfill' => '#66CCFF',
			'lakesoutline' => '#6B8B9E',
		);
		foreach (array(
			'ALABAMA', 'ALASKA', 'ARIZONA', 'ARKANSAS', 'CALIFORNIA', 'COLORADO', 'CONNECTICUT', 'DELAWARE', 'FLORIDA', 'GEORGIA', 'HAWAII', 'IDAHO', 'ILLINOIS', 'INDIANA', 'IOWA', 'KANSAS', 'KENTUCKY', 'LOUISIANA', 'MAINE', 'MARYLAND', 'MASSACHUSETTS', 'MICHIGAN', 'MINNESOTA', 'MISSISSIPPI', 'MISSOURI', 'MONTANA', 'NEBRASKA', 'NEVADA', 'NEW HAMPSHIRE', 'NEW JERSEY', 'NEW MEXICO', 'NEW YORK', 'NORTH CAROLINA', 'NORTH DAKOTA', 'OHIO', 'OKLAHOMA', 'OREGON', 'PENNSYLVANIA', 'RHODE ISLAND', 'SOUTH CAROLINA', 'SOUTH DAKOTA', 'TENNESSEE', 'TEXAS', 'UTAH', 'VERMONT', 'VIRGINIA', 'WASHINGTON', 'WEST VIRGINIA', 'WISCONSIN', 'WYOMING', 'WASHINGTON DC'
		) as $k=>$area) {
			$this->default['upclr_'.($k+1)] = '#E0F3FF';
			$this->default['ovrclr_'.($k+1)] = '#8FBEE8';
			$this->default['dwnclr_'.($k+1)] = '#477CB2';
			$this->default['url_'.($k+1)] = '';
			$this->default['turl_'.($k+1)] = '_self';
			$this->default['info_'.($k+1)] = $area;
			$this->default['enbl_'.($k+1)] = 1;
		}
		if(isset($_POST['us_map_iii']) && isset($_POST['submit-clrs'])){
			$i = 1;
			while (isset($_POST['url_'.$i])) {
				$_POST['upclr_'.$i] = $_POST['upclr_all'];
				$_POST['ovrclr_'.$i] = $_POST['ovrclr_all'];
				$_POST['dwnclr_'.$i] = $_POST['dwnclr_all'];
				$i++;
			}
			update_option('us_map_iii', array_map('stripslashes_deep', $_POST));
			$this->options = array_map('stripslashes_deep', $_POST);
		}
		if(isset($_POST['us_map_iii']) && isset($_POST['submit-url'])){
			$i = 1;
			while (isset($_POST['url_'.$i])) {
				$_POST['url_'.$i] = $_POST['url_all'];
				$_POST['turl_'.$i] = $_POST['turl_all'];
				$i++;
			}
			update_option('us_map_iii', array_map('stripslashes_deep', $_POST));
			$this->options = array_map('stripslashes_deep', $_POST);
		}	
		if(isset($_POST['us_map_iii']) && isset($_POST['submit-info'])){
			$i = 1;
			while (isset($_POST['url_'.$i])) {
				$_POST['info_'.$i] = $_POST['info_all'];
				$i++;
			}
			update_option('us_map_iii', array_map('stripslashes_deep', $_POST));
			$this->options = array_map('stripslashes_deep', $_POST);
		}
		if(isset($_POST['us_map_iii']) && !isset($_POST['preview_map'])){
			update_option('us_map_iii', array_map('stripslashes_deep', $_POST));
			$this->options = array_map('stripslashes_deep', $_POST);
		}
		if(isset($_POST['us_map_iii']) && isset($_POST['restore_default'])){
			update_option('us_map_iii', $this->default);
			$this->options = $this->default;
		}
		if(!is_array($this->options)){
			$this->options = $this->default;
		}
	}

	protected function constant(){
		define( 'USMAPIII_VERSION', '1.0' );
		define( 'USMAPIII_DIR', plugin_dir_path( __FILE__ ) );
		define( 'USMAPIII_URL', plugin_dir_url( __FILE__ ) );
	}

	public function us_map_iii(){
		ob_start();
		include 'design/map.php';
		?>
		<script type="text/javascript">
			<?php include 'config.php'; ?>
		</script>
		<?php
		wp_enqueue_style( 'us-mapstyle-frontend', USMAPIII_URL . 'map-style.css', false, '1.0', 'all' );
		wp_enqueue_script( 'us-map-interact', USMAPIII_URL . 'map-interact.js', array('jquery'), 10, '1.0', true );
		$html = ob_get_clean();
		return $html;
	}

	public function us_map_iii_options_page() {
		add_menu_page('US Map III', 'US Map III', 'manage_options', 'us-map-iii', array($this, 'options_screen'), USMAPIII_URL . 'images/map-icon.png');
	}

	public function admin_ajax_url(){
		$url_action = admin_url( '/' ) . 'admin-ajax.php';
		echo '<div style="display:none" id="wpurl">'. $url_action.'</div>';
	}

	public function options_screen(){ ?>
		<script type="text/javascript">
			<?php include 'config.php'; ?>
		</script>
	<?php include 'design/admin.php';
	}

	public function add_js_to_wp_footer(){ ?>
	<span id="tipus" style="margin-top:-32px"></span>
	<?php }

	public function add_span_tag(){
		?>
		<span id="tipus"></span>
		<?php
	}

	public function stripslashes_deep($value) {
		$value = is_array($value) ?
		array_map(array($this, 'stripslashes_deep'), $value) : stripslashes($value);
		return $value;
	}

	public function init_admin_script(){
		if(isset($_GET['page']) && $_GET['page'] == 'us-map-iii'):
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style('thickbox');
		wp_enqueue_script('thickbox');
		wp_enqueue_script( 'media-upload');
		wp_enqueue_style( 'us-map-style', USMAPIII_URL . 'style.css', false, '1.0', 'all' );
		wp_enqueue_style( 'us-mapstyle', USMAPIII_URL . 'map-style.css', false, '1.0', 'all' );
		wp_enqueue_style( 'wp-tinyeditor', USMAPIII_URL . 'tinyeditor.css', false, '1.0', 'all' );
		wp_enqueue_script( 'us-map-interact', USMAPIII_URL . 'map-interact.js', array('jquery'), 10, '1.0', true );
		wp_enqueue_script( 'us-map-tiny.editor', USMAPIII_URL . 'js/tinymce.min.js', 10, '1.0', true );
		wp_enqueue_script( 'us-map-script', USMAPIII_URL . 'js/scripts.js', array( 'wp-color-picker' ), false, true );
		endif;
	}
}

$us_map_iii = new US_Map_III();