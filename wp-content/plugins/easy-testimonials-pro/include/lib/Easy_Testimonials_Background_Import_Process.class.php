<?php
	class Easy_Testimonials_Background_Import_Process extends Vandelay_Background_Import_Process
	{
		/**
		 * Insert Post
		 *
		 * Override this method to perform any actions required on each
		 * queue item. Return the modified item for further processing
		 * in the next pass through. Or, return false to remove the
		 * item from the queue.
		 *
		 * @param mixed $item Queue item to iterate over
		 *
		 * @return mixed
		 */
		function insert_post($post)
		{	
			//defaults
			$the_name = $the_body = $the_date = '';

			if (isset ($post['Title'])) {
				$the_name = $post['Title'];
			}
			
			if (isset ($post['Body'])) {
				$the_body = $post['Body'];
			}	
			
			if (isset ($post['Date'])) {
				$the_date = date("Y-m-d H:i:s", strtotime($post['Date']));
			}
			
			//look for a testimonial with the title and body
			//if not found, insert this one
			$postslist = !empty($the_name)
						 ? get_page_by_title( $the_name, OBJECT, 'testimonial' )
						 : false;
			
			//if this is empty, a match wasn't found and therefore we are safe to insert
			if ( !empty($the_name) && empty($postslist) ) {
				//insert the testimonials				
				$tags = array();
			   
				$new_post = array(
					'post_title'    => $the_name,
					'post_content'     => $the_body,
					'post_category' => array(1),  // custom taxonomies too, needs to be an array
					'tags_input'    => $tags,
					'post_status'   => 'publish',
					'post_type'     => 'testimonial',
					'post_author' => get_option('easy_t_testimonial_author', 1),
					'post_date' => $the_date
				);
			
				$new_id = wp_insert_post($new_post);
			   
				// assign Testimonial Categories if any were specified
				// NOTE: we are using wp_set_object_terms instead of adding a tax_input key to wp_insert_posts, because 
				// it is less likely to fail b/c of permissions and load order (i.e., taxonomy may not have been created yet)
				if (!empty($post['Categories'])) {
					$post_cats = explode(',', $post['Categories']);
					$post_cats = array_map('intval', $post_cats); // sanitize to ints
					wp_set_object_terms($new_id, $post_cats, 'easy-testimonial-category');
				}

				//defaults, in case certain data wasn't in the CSV			
				$client_name = isset($post['Client Name']) ? $post['Client Name'] : "";
				$email = isset($post['E-Mail Address']) ? $post['E-Mail Address'] : "";
				$position_location_other = isset($post['Position / Location / Other']) ? $post['Position / Location / Other'] : "";
				$location_product_other = isset($post['Location / Product / Other']) ? $post['Location / Product / Other'] : "";
				$rating = isset($post['Rating']) ? $post['Rating'] : "";
				$htid = isset($post['HTID']) ? $post['HTID'] : "";
				$featured_image = isset($post['Featured Image']) ? $post['Featured Image'] : "";
				update_post_meta( $new_id, '_ikcf_client', $client_name );
				update_post_meta( $new_id, '_ikcf_email', $email );
				update_post_meta( $new_id, '_ikcf_position', $position_location_other );
				update_post_meta( $new_id, '_ikcf_other', $location_product_other );
				update_post_meta( $new_id, '_ikcf_rating', $rating );
				update_post_meta( $new_id, '_ikcf_htid', $htid );
				
				// Look for a photo path on CSV
				// If found, try to import this photo and attach it to this testimonial
				$this->import_testimonial_photo($new_id, $featured_image);		
				
				$this->update_batch_status($post['batch_id'], 'imported');
				
				return true;
			} 
			
			$this->update_batch_status($post['batch_id'], 'duplicate');
			return false;
		}
		
		function import_testimonial_photo($post_id = '', $photo_source = '')
		{	
			//used for overriding specific attributes inside media_handle_sideload
			$post_data = array();
			
			//set attributes in override array
			$post_data = array(
				'post_title' => '', //photo title
				'post_content' => '', //photo description
				'post_excerpt' => '', //photo caption
			);
		
			require_once( ABSPATH . 'wp-admin/includes/image.php');
			require_once( ABSPATH . 'wp-admin/includes/media.php' );//need this for media_handle_sideload
			require_once( ABSPATH . 'wp-admin/includes/file.php' );//need this for the download_url function
			
			$desc = ''; // photo description
			
			$picture = urldecode($photo_source);
			
			// Download file to temp location
			$tmp = download_url( $picture);
			
			// Set variables for storage
			// fix file filename for query strings
			preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $picture, $matches);
			$file_array['name'] = isset($matches[0]) ? basename($matches[0]) : basename($picture);
			$file_array['tmp_name'] = $tmp;

			// If error storing temporarily, unlink
			if ( is_wp_error( $tmp ) ) {
				//$error_string = $tmp->get_error_message();
				//echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';
				
				@unlink($file_array['tmp_name']);
				$file_array['tmp_name'] ='';
			}
			
			$id = media_handle_sideload( $file_array, $post_id, $desc, $post_data );

			// If error storing permanently, unlink
			if ( is_wp_error($id) ) {
				//$error_string = $id->get_error_message();
				//echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';
				
				@unlink($file_array['tmp_name']);
			} else {		
				//add as the post thumbnail
				if( !empty($post_id) ){
					add_post_meta($post_id, '_thumbnail_id', $id, true);
				}
			}
		}	
	}