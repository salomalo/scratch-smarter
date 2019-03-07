<?php
class easyTestimonialDisplayOptions extends easyTestimonialOptions{
	var $tabs;
	var $config;
	
	function __construct($config){			
		//call register settings function
		add_action( 'admin_init', array($this, 'register_settings'));	
		
		//assign config
		$this->config = $config;
	}
	
	function register_settings(){		
		//register our settings		
		
		/* Display settings */
		register_setting( 'easy-testimonials-display-settings-group', 'testimonials_link' );
		register_setting( 'easy-testimonials-display-settings-group', 'easy_t_view_more_link_text' );
		register_setting( 'easy-testimonials-display-settings-group', 'easy_t_show_view_more_link' );		
		register_setting( 'easy-testimonials-display-settings-group', 'easy_t_previous_text' );
		register_setting( 'easy-testimonials-display-settings-group', 'easy_t_next_text' );
		register_setting( 'easy-testimonials-display-settings-group', 'testimonials_image' );
		register_setting( 'easy-testimonials-display-settings-group', 'meta_data_position' );
		register_setting( 'easy-testimonials-display-settings-group', 'easy_t_mystery_man' );
		register_setting( 'easy-testimonials-display-settings-group', 'easy_t_fallback_image_method' );
		register_setting( 'easy-testimonials-display-settings-group', 'easy_t_gravatar' );
		register_setting( 'easy-testimonials-display-settings-group', 'easy_t_image_size' );
		register_setting( 'easy-testimonials-display-settings-group', 'easy_t_width' );
		register_setting( 'easy-testimonials-display-settings-group', 'easy_t_cache_buster', array($this, 'easy_t_bust_options_cache') );
		register_setting( 'easy-testimonials-display-settings-group', 'easy_t_excerpt_text', array($this, 'easy_t_excerpt_text') );
		register_setting( 'easy-testimonials-display-settings-group', 'easy_t_excerpt_length', array($this, 'easy_t_excerpt_length') );
		register_setting( 'easy-testimonials-display-settings-group', 'easy_t_link_excerpt_to_full' );
		register_setting( 'easy-testimonials-display-settings-group', 'easy_t_use_custom_excerpt' );
	}
	
	function render_settings_page()
	{
		//instantiate tabs object for output basic settings page tabs
		$tabs = new GP_Sajak( array(
			'header_label' => 'Display Settings',
			'settings_field_key' => 'easy-testimonials-display-settings-group', // can be an array			
		) );		
		
		$this->settings_page_top();
		$this->setup_basic_tabs($tabs);
		$this->settings_page_bottom();
	}

	function output_image_options(){
		//fix for legacy users
		if (get_option('easy_t_mystery_man', 1)){
			update_option('easy_t_fallback_image_method', 'mystery_person');
			update_option('easy_t_mystery_man', 0);			
		}	
		?>
		<h3>Testimonial Images</h3>
		<table class="form-table">
			<tr valign="top">
				<td><input type="checkbox" name="testimonials_image" id="testimonials_image" value="1" <?php if(get_option('testimonials_image', true)){ ?> checked="CHECKED" <?php } ?>/>
				<label for="testimonials_image">Show Testimonial Image</label>
				<p class="description">If checked, the Image will be shown next to the Testimonial.</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="easy_t_image_size">Testimonial Image Size</label></th>
				<td>
					<select name="easy_t_image_size" id="easy_t_image_size">	
						<?php $this->easy_t_output_image_options(); ?>
					</select>
					<p class="description">Select which size image to display with your Testimonials.  Defaults to 50px X 50px.</p>
				</td>
			</tr>
			<tr valign="top">
				<td><input type="checkbox" name="easy_t_gravatar" id="easy_t_gravatar" value="1" <?php if(get_option('easy_t_gravatar', true)){ ?> checked="CHECKED" <?php } ?>/>
				<label for="easy_t_gravatar">Use Gravatars</label>
				<p class="description">Use a Gravatar if one is found matching the E-Mail Address on the Testimonial.</p>
				</td>
			</tr>
			<tr valign="top">			
				<th scope="row">
				<label for="easy_t_gravatar">Fallback Images</label><br>
				<p class="description" style="font-weight:normal">If no Featured Image is set and no Gravatar is found, a fallback image can be used.</p>
				</th>
				<td>
				<input type="radio" name="easy_t_fallback_image_method" id="easy_t_fallback_image_method_mystery_person" value="mystery_person" <?php if(get_option('easy_t_fallback_image_method', '') == "mystery_person" ){ ?> checked="CHECKED" <?php } ?>/>
				<label for="easy_t_fallback_image_method_mystery_person">Mystery Person</label>
				<p class="description">Use the Mystery Person avatar for any missing images.</p>
				<br>
				<input type="radio" name="easy_t_fallback_image_method" id="easy_t_fallback_image_method_smart_text_avatars" value="smart_text_avatars" <?php if(get_option('easy_t_fallback_image_method', '') == "smart_text_avatars" ){ ?> checked="CHECKED" <?php } ?>/>
				<label for="easy_t_fallback_image_method_smart_text_avatars">Smart Text Avatars</label>
				<p class="description">Use the Client's initials overlayed on top of bold colors as an avatar for any missing images.</p>
				<br>
				<input type="radio" name="easy_t_fallback_image_method" id="easy_t_fallback_image_method_no_image" value="no_image" <?php if(get_option('easy_t_fallback_image_method', '') == "no_image" ){ ?> checked="CHECKED" <?php } ?>/>
				<label for="easy_t_fallback_image_method_no_image">No Fallback Image</label>
				</td>
			</tr>
		</table>
		<?php
	}

	function output_excerpt_options(){
		?>
		<h3>Testimonial Excerpt Options</h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="easy_t_excerpt_length">Excerpt Length</label></th>
				<td><input type="text" name="easy_t_excerpt_length" id="easy_t_excerpt_length" value="<?php echo get_option('easy_t_excerpt_length', 55); ?>" />
				<p class="description">This is the number of words to use in an shortened testimonial.  The default value is 55 words.</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="easy_t_excerpt_text">Excerpt Text</label></th>
				<td><input type="text" name="easy_t_excerpt_text" id="easy_t_excerpt_text" value="<?php echo get_option('easy_t_excerpt_text', 'Continue Reading'); ?>" />
				<p class="description">The text used after the Excerpt.  If you are linking your Excerpts to Full Testimonials, this text is used in the Link.  This defaults to "Continue Reading".</p>
				</td>
			</tr>
			<tr valign="top">
				<td><input type="checkbox" name="easy_t_link_excerpt_to_full" id="easy_t_link_excerpt_to_full" value="1" <?php if(get_option('easy_t_link_excerpt_to_full', true)){ ?> checked="CHECKED" <?php } ?>/>
				<label for="easy_t_link_excerpt_to_full">Link Excerpts to Full Testimonial</label>
				<p class="description">If checked, shortened testimonials will end with a link that goes to the full length Testimonial.</p>
				</td>
			</tr>
		</table>
		<?php
	}

	function output_viewmoretestimonials_options(){
		?>
		<h3>View More Testimonials Link</h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="testimonials_link">Link Address</label></th>
				<td><input type="text" name="testimonials_link" id="testimonials_link" value="<?php echo get_option('testimonials_link', ''); ?>" />
				<p class="description">This is the URL of the 'View More' Link.</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="easy_t_view_more_link_text">Link Text</label></th>
				<td><input type="text" name="easy_t_view_more_link_text" id="easy_t_view_more_link_text" value="<?php echo get_option('easy_t_view_more_link_text', 'Read More Testimonials'); ?>" />
				<p class="description">The Value of the View More Link text.  This defaults to Read More Testimonials, but can be changed.</p>
				</td>
			</tr>
			<tr valign="top">
				<td><input type="checkbox" name="easy_t_show_view_more_link" id="easy_t_show_view_more_link" value="1" <?php if(get_option('easy_t_show_view_more_link', false)){ ?> checked="CHECKED" <?php } ?>/>
				<label for="easy_t_show_view_more_link">Show View More Testimonials Link</label>
				<p class="description">If checked, the View More Testimonials Link will be displayed after each testimonial.  This is useful to direct visitors to a page that has many more Testimonials on it to read.</p>
				</td>
			</tr>
		</table>
		<?php
	}

	function output_slideshow_options(){
		?>
		<h3>Previous and Next Slide Controls</h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="easy_t_previous_text">Previous Testimonial Text</label></th>
				<td><input type="text" name="easy_t_previous_text" id="easy_t_previous_text" value="<?php echo htmlentities( get_option('easy_t_previous_text', '<< Prev') ); ?>" />
				<p class="description">This is the Text used for the Previous Testimonial button in the slideshow.</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="easy_t_next_text">Next Testimonial Text</label></th>
				<td><input type="text" name="easy_t_next_text" id="easy_t_next_text" value="<?php echo htmlentities( get_option('easy_t_next_text', 'Next >>') ); ?>" />
				<p class="description">This is the Text used for the Next Testimonial button in the slideshow.</p>
				</td>
			</tr>
		</table>
		<?php
	}

	function output_customfield_options(){
		?>
		<h3>Custom Fields</h3>
		<table class="form-table">
			<tr valign="top">
				<td><input type="checkbox" name="meta_data_position" id="meta_data_position" value="1" <?php if(get_option('meta_data_position', false)){ ?> checked="CHECKED" <?php } ?>/>
				<label for="meta_data_position">Show Testimonial Info Above Testimonial</label>
				<p class="description">If checked, the Testimonial Custom Fields will be displayed Above the Testimonial.  Defaults to Displaying Below the Testimonial.  Note: the Testimonial Image will be displayed to the left of this information.  NOTE: Checking this may have adverse affects on certain Styles.</p>
				</td>
			</tr>
		</table>
		<?php
	}

	function output_width_options(){
		?>
		<h3>Default Testimonials Width</h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="easy_t_width">Default Testimonials Width</label></th>
				<td><input type="text" name="easy_t_width" id="easy_t_width" value="<?php echo get_option('easy_t_width', ''); ?>" />
				<p class="description">If you want, you can set a global width for Testimonials.  This can be left blank and it can also be overrode directly, via the shortcode.</p>
				</td>
			</tr>
		</table>
		<?php
	}
	
	function setup_basic_tabs($tabs){	
		$this->tabs = $tabs;
		
		//load additional label string based upon pro status
		$pro_string = $this->config->is_pro ? "" : " (Pro)";
	
		$this->tabs->add_tab(
			'excerpt_options', // section id, used in url fragment
			'Excerpt Options', // section label
			array($this, 'output_excerpt_options'), // display callback
			array(
				'class' => 'extra_li_class', // extra classes to add sidebar menu <li> and tab wrapper <div>
				'icon' => 'ellipsis-h' // icons here: http://fontawesome.io/icons/
			)
		);
		$this->tabs->add_tab(
			'image_options', // section id, used in url fragment
			'Image Options', // section label
			array($this, 'output_image_options'), // display callback
			array(
				'class' => 'extra_li_class', // extra classes to add sidebar menu <li> and tab wrapper <div>
				'icon' => 'photo' // icons here: http://fontawesome.io/icons/
			)
		);
		$this->tabs->add_tab(
			'slideshow_options', // section id, used in url fragment
			'Slideshow Options', // section label
			array($this, 'output_slideshow_options'), // display callback
			array(
				'class' => 'extra_li_class', // extra classes to add sidebar menu <li> and tab wrapper <div>
				'icon' => 'clone' // icons here: http://fontawesome.io/icons/
			)
		);
		$this->tabs->add_tab(
			'viewmoretestimonials_options', // section id, used in url fragment
			'View More Testimonials Link', // section label
			array($this, 'output_viewmoretestimonials_options'), // display callback
			array(
				'class' => 'extra_li_class', // extra classes to add sidebar menu <li> and tab wrapper <div>
				'icon' => 'link' // icons here: http://fontawesome.io/icons/
			)
		);
		$this->tabs->add_tab(
			'customfield_options', // section id, used in url fragment
			'Custom Field Options', // section label
			array($this, 'output_customfield_options'), // display callback
			array(
				'class' => 'extra_li_class', // extra classes to add sidebar menu <li> and tab wrapper <div>
				'icon' => 'gears' // icons here: http://fontawesome.io/icons/
			)
		);
		$this->tabs->add_tab(
			'width_options', // section id, used in url fragment
			'Width Options', // section label
			array($this, 'output_width_options'), // display callback
			array(
				'class' => 'extra_li_class', // extra classes to add sidebar menu <li> and tab wrapper <div>
				'icon' => 'arrows-h' // icons here: http://fontawesome.io/icons/
			)
		);
		
		$this->tabs->display();
	}
	
	
}