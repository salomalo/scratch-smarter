<?php
//single testimonial default template
//chicago style
//remove prefix from $output_theme
$output_theme = str_replace("style-", "", $output_theme);
?>
<div class="<?php echo $output_theme; ?> <?php echo $attribute_classes; ?> easy_t_single_testimonial highlights_style" <?php echo $width_style; ?>>
	<?php
		//output json-ld review markup, if option is set
		if($output_schema_markup){
			echo $this->output_jsonld_markup($display_testimonial);
		}
	?>
	<div class="header_4_area">
		<?php if ($show_thumbs) {
			?><div class="user_img_4"><?php
			echo $display_testimonial['image'];
			?></div><?php
		} ?>
		<div class="header_4_right">
			<div class="user-area-4">
				<?php if($show_the_client): ?>
					<p class="testimonial-client"><?php echo $this->easy_t_clean_html($display_testimonial['client']);?></p>
				<?php endif; ?>
				<?php if($show_the_position): ?>
					<p class="testimonial-position"><?php echo $this->easy_t_clean_html($display_testimonial['position']);?></p>
				<?php endif; ?>
			</div>
			<?php if($show_the_rating): ?>
				<?php if(strlen($display_testimonial['num_stars'])>0): ?>	
				<div class="rating-area-4">
					<div class="rate-area_4">
					<?php			
						$x = 5; //total available stars
						//output dark stars for the filled in ones
						for($i = 0; $i < $display_testimonial['num_stars']; $i ++){
							echo '<i class="fa fa-circle easy_t_star_filled" aria-hidden="true"></i>&nbsp;';
							$x--; //one less star available
						}
								
						//fill out the remaining empty stars
						echo '<span>';		
						for($i = 0; $i < $x; $i++){
							echo '<i class="fa fa-circle easy_t_star_empty" aria-hidden="true"></i>&nbsp;';
						}
						echo '</span>';
					?>	
					</div>					
					<?php if($show_the_date): ?>
						<div class="date_4 date"><p><?php echo $this->easy_t_clean_html($display_testimonial['date']);?></p></div>
					<?php endif; ?>
					<div style="clear: both;"></div>
				</div>	
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
	<div class="main_content_4">
		<div class="title-area_3">
			<div class="title_heading_4">
				<?php if ($show_title) {
					printf( '<div class="easy_testimonial_title">%s</div>', get_the_title($display_testimonial['id']) );
				} ?>				
				<?php if($show_the_other): ?>
					<p class="testimonial-other"><span>for</span> <?php echo $this->easy_t_clean_html($display_testimonial['other']);?></p>
				<?php endif; ?>
			</div>
		</div>
		<div class="testimonial-body">
			<?php echo $display_testimonial['content']; ?>
			<?php if($show_view_more):?><a href="<?php echo $testimonials_link; ?>" class="easy_testimonials_read_more_link"><?php echo get_option('easy_t_view_more_link_text', 'Read More Testimonials'); ?></a><?php endif; ?>
		</div>
	</div>
</div>