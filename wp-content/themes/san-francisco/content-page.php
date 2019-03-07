<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package SanFran
 * @since SanFran 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php if(!is_home) { ?>
			<h1 class="entry-title"><?php the_title(); ?></h1>
		<?php } ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'SanFran' ), 'after' => '</div>' ) ); ?>
		<?php edit_post_link( __( 'Edit', 'SanFran' ), '<span class="edit-link">', '</span>' ); ?>
	</div><!-- .entry-content -->
</article><!-- #post-<?php the_ID(); ?> -->
