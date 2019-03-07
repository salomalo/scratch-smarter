<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package SanFran
 * @since SanFran 0.1
 */
?>

	</div><!-- #main -->

	<footer id="colophon" role="contentinfo">
		<div id="site-generator">
			<?php do_action( 'SanFran_credits' ); ?>
			<p><?php printf( __( '%1$s by %2$s.', 'SanFran' ), 'San Francisco', '<a href="http://www.nustudio.com.au/" title="Nu Studio Perth">nustudio.com.au</a>' ); ?><br />
			<a href="<?php echo esc_url( __( 'http://wordpress.org/', 'SanFran' ) ); ?>" title="<?php esc_attr_e( 'Semantic Personal Publishing Platform', 'SanFran' ); ?>" rel="generator"><?php printf( __( 'Proudly powered by %s', 'SanFran' ), 'WordPress' ); ?></a></p>
			
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>