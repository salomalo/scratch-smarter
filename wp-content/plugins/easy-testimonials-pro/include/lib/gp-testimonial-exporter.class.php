<?php
class TestimonialsPlugin_Exporter
{	
	public function __construct()
	{
		$this->csv_headers = array(
			'Title',
			'Body',
			'Client Name',
			'E-Mail Address',
			'Position / Location / Other',
			'Location / Product / Other',
			'Rating',
			'HTID',
			'Featured Image',
			'Categories',
			'Date'
		);
	}
	
	public function output_form()
	{
		//updated to be used inside sajak
		?>		
		<p>Click the "Export My Testimonials" button below to download a CSV file of your testimonials.</p>
		<input type="hidden" name="_easy_t_do_export" value="_easy_t_do_export" />
		<p class="submit" style="margin-top:0;">
				<a href="?page=easy-testimonials-import-export-settings&et_process_export=true" class="button-primary" title="<?php _e('Export My Testimonials', 'easy-testimonials') ?>"><?php _e('Export My Testimonials', 'easy-testimonials') ?></a>
			</p>
		<?php
	}
	
	/* Renders a CSV file to STDOUT representing every record in the database
	 * NOTE: this file is, and must remain, compatible with the Importer
	 */
	public function process_export($filename = "export.csv")
	{		
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-Description: File Transfer');
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename={$filename}");
		header("Expires: 0");
		header("Pragma: public");
		
		// set memory limit to high value (4GB) and remove time limit, to allow 
		// for large (20k+) exports. this still might not accommodate all users.
		@ini_set( 'memory_limit', apply_filters( 'admin_memory_limit', '4096M' ) );
		set_time_limit(0);
		
		// open file handle to STDOUT
		$fh = @fopen( 'php://output', 'w' );
		
		// output the headers first
		$this->fputcsv_safe($fh, $this->csv_headers);
			
		$page = 1;
		$posts = $this->get_posts_paged(100, $page);
		
		
		while ( !empty($posts) ) {		
			// now output one row for each record
			foreach($posts as $testimonial) {
				$row = array();
				$row['title'] = $testimonial->post_title;
				$row['body'] = $testimonial->post_content;
				$row['client_name'] = get_post_meta( $testimonial->ID, '_ikcf_client', true);
				$row['email_address'] = get_post_meta( $testimonial->ID, '_ikcf_email', true);
				$row['position_location_other'] = get_post_meta( $testimonial->ID, '_ikcf_position', true);
				$row['location_product_other'] = get_post_meta( $testimonial->ID, '_ikcf_other', true);
				$row['rating'] = get_post_meta( $testimonial->ID, '_ikcf_rating', true);
				$row['htid'] = get_post_meta( $testimonial->ID, '_ikcf_htid', true);
				$row['photo_path'] = $this->get_photo_path( $testimonial->ID );
				$row['categories'] = $this->list_taxonomy_ids( $testimonial->ID, 'easy-testimonial-category' );	
				$row['date'] = $testimonial->post_date;
				$this->fputcsv_safe($fh, $row);
				flush();
			}
			$posts = null;
			flush();			
			$page++;
			$posts = $this->get_posts_paged(10, $page);
		}

		// Close the file handle
		fclose($fh);
		exit;
		
	}	

	/*
	* A version of fputcsv that used Windows line endings (CR LF) 
	* instead of Unix line endings (LF)
	*
	* @param File Pointer $fp The file pointer to output the CSV line to
	* @param Array $array Array of data to write for this line
	*/
	function fputcsv_safe($fp, $array)
	{
		$eol = "\r\n";
		
		/* Replace new lines which appear inside values with a random string, 
		 * so we can  distinguish between newlines inside values and new 
		 * lines at the end of rows.
		 *
		 * IMPORTANT: the placeholder must contain a carriage return (\r) so
		 * that fputcsv will put quotes around the value
		 */
		// random string that includes a carrige return and is unlikely to appear in a file
		$nl_placeholder = "EREW33WXX\rW\rWDDXX\rWFFFXXW\rEEEW\rRRRWXX\rWXFFXW\rWGGXX\rXPPW\r420XXW";
		foreach( $array as $k => $v ) {
			$array[$k] = str_replace( array("\r\n", "\n\r", "\n"), $nl_placeholder, $v );
		}
		
		// generate the CSV line in a temp buffer, so we can replace the line ending before writing it
		$line = $this->temp_csv_line($array);
		
		// change line endings from Unix (LF) to Windows (CR LF) to make Excel happy
		$line = str_replace("\n", "\r\n", $line);
		
		// restore the new lines inside values
		$line = str_replace($nl_placeholder, "\r\n", $line);
		fwrite($fp, $line);
	}
	
	/*
	 * Generates a CSV line in a temp buffer and returns it
	 * 
	 * @param Array $array Array of data to write for this line
	 * 
	 * @return String The CSV line (Note: ends with new line char ("\n", a.k.a. LF)
	 */	 
	function temp_csv_line($array)
	{
		// output up to 5MB is kept in memory, if it becomes bigger it will automatically be written to a temporary file
		$csv = fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');
		fputcsv($csv, $array);
		rewind($csv);
		$output = stream_get_contents($csv);				
		return $output;
	}
	
	function getcsvline($list,  $seperator, $enclosure, $newline = "" )
	{
		$fp = fopen('php://temp', 'r+'); 

		fputcsv($fp, $list, $seperator, $enclosure );
		rewind($fp);

		$line = fgets($fp);
		if( $newline and $newline != "\n" ) {
			if( $line[strlen($line)-2] != "\r" and $line[strlen($line)-1] == "\n") {
				$line = substr_replace($line,"",-1) . $newline;
			} else {
				// return the line as is (literal string)
				//die( 'original csv line is already \r\n style' );
			}
		}
		return $line;
	}	
	
	function get_posts_paged($posts_per_page = 100, $page_number = 1)
	{
		//load records
		$args = array(
			'posts_per_page'   	=> $posts_per_page,
			'paged'   			=> $page_number,
			'orderby'          	=> 'post_date',
			'order'            	=> 'DESC',
			'post_type'        	=> 'testimonial',
			'post_status'      	=> 'publish',
			'suppress_filters' 	=> true 				
		);
		return get_posts($args);		
	}
	
	/*
	 * Get the path to the testimonials's photo
	 *
	 * @returns a string representing the path to the photo
	*/
	function get_photo_path($post_id){
		$image_str = "";
		
		if (has_post_thumbnail( $post_id ) ){
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'single-post-thumbnail' );
			$image_str = $image[0];
		}
		
		return $image_str;
	}
	
	/* 
	 * Get a comma separated list of IDs representing each term of $taxonomy that $post_id belongs to
	 *
	 * @returns comma separated list of IDs, or empty string if no terms are assigned
	*/
	function list_taxonomy_ids($post_id, $taxonomy)
	{
		$terms = wp_get_post_terms( $post_id, $taxonomy ); // could also pass a 3rd param, $args
		if (is_wp_error($terms)) {
			return '';
		}
		else {
			$term_list = array();
			foreach ($terms as $t) {
				$term_list[] = $t->term_id;
			}
			return implode(',', $term_list);
		}
	}
}