<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the
 * #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
?>
	</div><!-- #main .wrapper -->
	<footer id="colophon" role="contentinfo">
		<div class="site-info">
			<table cellpadding=2 cellspacing=0 border=0 background="//www.scratchsmarter.com/images/blue_bar_960.jpg" width="100%" height="100%">
				<tr>
					<td>
						<table cellpadding=2 cellspacing=0 border=0 width="100%">
							<tr>
								<td>
									<center>
										<a class="footer" href="//www.scratchsmarter.com/privacy-policy" target="_blank">Privacy Policy</a> 
									</center>
								</td>
								<td>
									<center>
										<a class="footer" href="//www.scratchsmarter.com/terms-and-conditions" target="_blank">Terms and Conditions</a> 
									</center>
								</td>
								<td>
									<center>
										<a class="footer" href="//www.scratchsmarter.com/contact-us-2" target="_blank">Contact Us</a> 
									</center>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>

			<center>
			<?php printf ('Copyright '.date('Y').' ScratchSmarter, LLC. All rights reserved.');?>
			</center>
			<!-- <?php do_action( 'twentytwelve_credits' ); ?> 
			<a href="<?php echo esc_url( __( '//wordpress.org/', 'twentytwelve' ) ); ?>" title="<?php esc_attr_e( 'ScratchSmarter, LLC.', 'twentytwelve' ); ?>"><?php printf( __( 'Copyright 2013. ScratchSmarter, LLC. All rights reserved.', 'twentytwelve' ), 'WordPress' ); ?></a> -->
			
			
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>