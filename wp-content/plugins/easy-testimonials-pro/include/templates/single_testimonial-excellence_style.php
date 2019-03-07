<?php
//single testimonial default template
//chicago style
//remove prefix from $output_theme
$output_theme = str_replace("style-", "", $output_theme);
?>
<div class="<?php echo $output_theme; ?> <?php echo $attribute_classes; ?> easy_t_single_testimonial excellence_style" <?php echo $width_style; ?>>
	<?php
		//output json-ld review markup, if option is set
		if($output_schema_markup){
			echo $this->output_jsonld_markup($display_testimonial);
		}
	?>
	<?php if ($show_thumbs) {
		?><div class="user_img_2"><?php
		echo $display_testimonial['image'];
		?></div><?php
	} ?>
	<div class="main_wrapper_2">
		<div class="header_2_area">
			<div class="user-area-2">
				<?php if($show_the_client): ?>
					<p class="testimonial-client"><?php echo $this->easy_t_clean_html($display_testimonial['client']);?></p>
				<?php endif; ?>
				<?php if($show_the_position): ?>
					<p class="testimonial-position"><?php echo $this->easy_t_clean_html($display_testimonial['position']);?></p>
				<?php endif; ?>
			</div>
			<div class="header_2_bottom">
				<?php if($show_the_rating): ?>
					<?php if(strlen($display_testimonial['num_stars'])>0): ?>
					<?php $rating_as_percentage = ( intval($display_testimonial['num_stars']) * 2) * 10; ?>
					<div class="rate-area-2">
						<div class="progress_bar">
							<span class="progress_fill_<?php echo $rating_as_percentage; ?> easy_t_star_filled"></span>
						</div>
					</div>			
					<?php endif; ?>
				<?php endif; ?>
				<?php if($show_the_date): ?>
					<div class="date_2 date"><p><?php echo $this->easy_t_clean_html($display_testimonial['date']);?></p></div>
				<?php endif; ?>
			</div>
		</div>
		<div class="title_heading_2">
			<?php if ($show_title) {
				printf( '<div class="easy_testimonial_title">%s</div>', get_the_title($display_testimonial['id']) );
			} ?>
			<?php if($show_the_other): ?>
				<p class="testimonial-other"><span>for</span> <?php echo $this->easy_t_clean_html($display_testimonial['other']);?></p>
			<?php endif; ?>
		</div>
		<div class="main_content_2 testimonial-body">
			<?php echo $display_testimonial['content']; ?>
			<?php if($show_view_more):?><a href="<?php echo $testimonials_link; ?>" class="easy_testimonials_read_more_link"><?php echo get_option('easy_t_view_more_link_text', 'Read More Testimonials'); ?></a><?php endif; ?>
		</div>
	</div> 
</div>