<?php
/*
This file is part of Easy Testimonials.

Easy Testimonials is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Easy Testimonials is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with The Easy Testimonials.  If not, see <http://www.gnu.org/licenses/>.
*/

class Easy_Testimonials_Pro_Options
{	
	var $messages = array();
	
	function __construct($config){
		//instantiate Sajak so we get our JS and CSS enqueued
		new GP_Sajak();	
	}
	
	function is_our_settings_page()
	{
		$screen = $_SERVER['REQUEST_URI'];
		return (
			is_admin()
			&& !empty($screen)
			&& strpos($screen, 'aloha') === false
			&& strpos($screen, 'upgrade') === false
			&& strpos($screen, 'easy-testimonials') !== false
		);
	}
	
	//output top of settings page
	function settings_page_top($show_tabs = true){
		global $pagenow;
		$title = "Easy Testimonials Settings";

		if( isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true' && $_GET['page'] != 'easy-testimonials-license-settings' ) {
			$this->messages[] = "Easy Testimonials settings updated.";
		}
	?>
	<div class="wrap easy_testimonials_admin_wrap">
	<?php
		if( !empty($this->messages) ){
			foreach($this->messages as $message){
				echo '<div id="messages" class="gp_updated fade">';
				echo '<p>' . $message . '</p>';
				echo '</div>';
			}
			
			$this->messages = array();
		}
	?>
        <div id="icon-options-general" class="icon32"></div>
		<?php
		
		if($show_tabs){
			$this->get_and_output_current_tab($pagenow);
		}
	}
	
	//builds the bottom of the settings page
	//includes the signup form, if not pro
	function settings_page_bottom(){
		?>
		</div>
		<?php
	}
	
	function get_and_output_current_tab($pagenow){
		$tab = $_GET['page'];
		
		do_action( 'easy_t_admin_render_settings_tabs', $tab );
				
		return $tab;
	}
} // end class