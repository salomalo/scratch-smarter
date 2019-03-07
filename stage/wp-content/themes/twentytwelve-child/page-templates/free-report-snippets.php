<?php
/**
 * Template Name: SS Free Report Snippets
 *
 * @package N/A
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

 
 
 
 
 
 
 
get_header(); ?>

	<div id="primary" class="site-content" style="width:100%;">
		<div id="content" role="main">
			<?php
				if( $_GET['state'] )
				{
					$state = $_GET['state'];
				
					$snippets = file_get_contents('//www.scratchsmarter.com/scraper/weekly_pdf_free_snippets/pdf_free_snippets_'.$state.'.htm');
				
					echo $snippets;
				}
				else
					echo 'No State supplied.';
			?>
		</div><!-- #content -->
	</div><!-- #primary -->


<?php
//get_sidebar();
get_footer();?>
