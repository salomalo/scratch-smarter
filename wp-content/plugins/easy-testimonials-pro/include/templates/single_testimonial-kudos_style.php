<?php
//single testimonial default template
//chicago style
//remove prefix from $output_theme
$output_theme = str_replace("style-", "", $output_theme);
?>
<div class="<?php echo $output_theme; ?> <?php echo $attribute_classes; ?> easy_t_single_testimonial kudos_style" <?php echo $width_style; ?>>
	<?php
		//output json-ld review markup, if option is set
		if($output_schema_markup){
			echo $this->output_jsonld_markup($display_testimonial);
		}
	?>
	<div class="header_1_area">
		<div class="user-area-1">
			<?php if($show_the_client): ?>
				<p class="testimonial-client"><?php echo $this->easy_t_clean_html($display_testimonial['client']);?></p>
			<?php endif; ?>
			<?php if($show_the_position): ?>
				<p class="testimonial-position"><?php echo $this->easy_t_clean_html($display_testimonial['position']);?></p>
			<?php endif; ?>
		</div>
		<div class="header_1_bottom">
			<?php if($show_the_date): ?>
				<div class="date_1 date"><p><?php echo $this->easy_t_clean_html(date('M d Y', strtotime($display_testimonial['date'])));?></p></div>
			<?php endif; ?>
			<?php if ($show_thumbs) {
				?><div class="user_img_1"><?php
				echo $display_testimonial['image'];
				?></div><?php
			} ?>	
			<?php if($show_the_rating): ?>
				<?php if(strlen($display_testimonial['num_stars'])>0): ?>			
				<div class="rate-area-1">
					<i class="ion-star"></i>
					<span class="easy_t_star_filled"><?php echo $display_testimonial['num_stars']; ?></span>
				</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
	<div class="title_heading_1">
		<?php if ($show_title) {
			printf( '<div class="easy_testimonial_title">%s</div>', get_the_title($display_testimonial['id']) );
		} ?>
		<?php if($show_the_other): ?>
			<p class="testimonial-other"><span>for</span> <?php echo $this->easy_t_clean_html($display_testimonial['other']);?></p>
		<?php endif; ?>
	</div>
	<div class="main_content_1 testimonial-body">
		<?php echo $display_testimonial['content']; ?>
		<?php if($show_view_more):?><a href="<?php echo $testimonials_link; ?>" class="easy_testimonials_read_more_link"><?php echo get_option('easy_t_view_more_link_text', 'Read More Testimonials'); ?></a><?php endif; ?>
	</div>
</div>