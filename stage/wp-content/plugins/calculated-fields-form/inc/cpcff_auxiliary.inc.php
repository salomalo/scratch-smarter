<?php
/**
 * Miscellaneous operations: CPCFF_AUXILIARY class
 *
 * Metaclass with miscellanous operations used through all plugin.
 *
 * @package CFF.
 * @since 1.0.167
 */

if(!class_exists('CPCFF_AUXILIARY'))
{
	/**
	 * Metaclass with miscellaneous operations.
	 *
	 * Publishes miscellanous operations to be used through all plugin's sections.
	 *
	 * @since  1.0.167
	 */
	class CPCFF_AUXILIARY
	{
		/**
		 * Public URL of the current blog.
		 *
		 * @since 1.0.167
		 * @var string $_site_url
		 */
		private static $_site_url;

		/**
		 * URL to the WordPress of the current blog.
		 *
		 * @since 1.0.167
		 * @var string $_wp_url
		 */
		private static $_wp_url;

		/**
		 * ID of the current blog.
		 *
		 * @var string $_wp_id
		 */
		private static $_wp_id;

		/**
		 * Returns the id of current blog.
		 *
		 * If the ID was read previously, uses the value stored in class property.
		 *
		 * @return int.
		 */
		public static function blog_id()
		{
			if(empty(self::$_wp_id)) self::$_wp_id = get_current_blog_id();
			return self::$_wp_id;
		} // End blog_id

		/**
		 * Returns the public URL of the current blog.
		 *
		 * If the URL was read previously, uses the value stored in class property.
		 *
		 * @since 1.0.167
		 * @return string.
		 */
		public static function site_url()
		{
			if(empty(self::$_site_url))
			{
				$blog = self::blog_id();
				self::$_site_url = get_home_url( $blog, '', is_ssl() ? 'https' : 'http');
			}
			return rtrim(self::$_site_url, '/');
		} // End site_url

		/**
		 * Returns the URL to the WordPress of the current blog.
		 *
		 * If the URL was read previously, uses the value stored in class property.
		 *
		 * @since 1.0.167
		 * @return string.
		 */
		public static function wp_url()
		{
			if(empty(self::$_wp_url))
			{
				$blog = self::blog_id();
				self::$_wp_url = get_admin_url( $blog );
			}
			return rtrim(self::$_wp_url, '/');
		} // End wp_url

		/**
		 * Removes Bom characters.
		 *
		 * @since 1.0.179
		 *
		 * @param string $str.
		 * @return string.
		 */
		public static function clean_bom($str)
		{
			$bom = pack('H*','EFBBBF');
			return preg_replace("/$bom/", '', $str);
		} // End clean_bom

		/**
		 * Converts some characters in a JSON string.
		 *
		 * @since 1.0.169
		 *
		 * @param string $str JSON string.
		 * @return string.
		 */
		public static function clean_json($str)
		{
			return str_replace(
				array("	", "\n", "\r"),
				array(" ", '\n', ''),
				$str
			);
		} // End clean_json

		/**
		 * Decodes a JSON string.
		 *
		 * Decode a JSON string, and receive a parameter to apply strip slashes first or not.
		 *
		 * @since 1.0.169
		 *
		 * @param string $str JSON string.
		 * @param string $stripcslashes Optional. To apply a stripcslashes to the text before json_decode. Default 'unescape'.
		 * @return mixed PHP Oject or False.
		 */
		public static function json_decode($str, $stripcslashes = 'unescape')
		{
			try
			{
				$str = CPCFF_AUXILIARY::clean_json( $str );
				if( $stripcslashes == 'unescape')$str = stripcslashes( $str );
				$obj = json_decode( $str );
			}
			catch( Exception $err ){ self::write_log($err); }
			return ( !empty( $obj ) ) ? $obj : false;
		} // End unserialize

		/**
		 * Replaces recursively the elements in an array by the elements in another one.
		 *
		 * The method will use the PHP function: array_replace_recursive if exists.
		 *
		 * @since 1.0.169
		 *
		 * @param array $array1
		 * @param array $array2
		 * @return array
		 */
		public static function array_replace_recursive($array1, $array2)
		{
			// If the array_replace_recursive function exists, use it
			if(function_exists('array_replace_recursive')) return array_replace_recursive($array1, $array2);
			foreach( $array2 as $key1 => $val1 )
			{
				if( isset( $array1[ $key1 ] ) )
				{
					if( is_array( $val1 ) )
					{
						foreach( $val1 as $key2 => $val2)
						{
							$array1[ $key1 ][ $key2 ] = $val2;
						}
					}
					else
					{
						$array1[ $key1 ] = $val1;
					}
				}
				else
				{
					$array1[ $key1 ] = $val1;
				}
			}
			return $array1;
		} // End array_replace_recursive

		/**
		 * Applies stripcslashes to the array elements recursively.
		 *
		 * The method checks if parameter is an array a text. If it is an array the method is called recursively.
		 *
		 * @since 1.0.176
		 *
		 * @param mixed $v array or single value.
		 * @return mixed the array or value with the slashes stripped
		 */
		public static function stripcslashes_recursive( $v )
		{
			if(is_array($v))
			{
				foreach($v as $k => $s)
				{
					$v[$k] = self::stripcslashes_recursive($s);
				}
				return $v;
			}
			else
			{
				return stripcslashes($v);
			}
		} // End stripcslashes_recursive

		/**
		 * Checks if the website is being visited by a crawler.
		 *
		 * Returns true if the website is being visited by a search engine spider,
		 * and the plugin was configure for hidding the forms front them, else false.
		 *
		 * @since 1.0.169
		 *
		 * @return bool.
		 */
		public static function is_crawler()
		{
			return (isset( $_SERVER['HTTP_USER_AGENT'] ) &&
					preg_match( '/bot|crawl|slurp|spider/i', $_SERVER[ 'HTTP_USER_AGENT' ] ) &&
					get_option( 'CP_CALCULATEDFIELDSF_EXCLUDE_CRAWLERS', false )
				);
		} // End is_crawler

		/**
		 * Checks if the page is AMP or not
		 *
		 * Checks first for the existence of functions: "is_amp_endpoint" or "ampforwp_is_amp_endpoint",
		 * and if they don't exists, checks the URL.
		 *
		 * @since 1.0.190
		 *
		 * @return bool.
		 */
		public static function is_amp()
		{
			if( function_exists('ampforwp_is_amp_endpoint') ) return ampforwp_is_amp_endpoint();
			elseif( function_exists('is_amp_endpoint') )
			{
				if(defined('AMP_QUERY_VAR')) return is_amp_endpoint();
			}
			return false;
		} // End is_amp

		/**
		 * Returns an iframe tag for loading the a webpage with the form only, specially useful for AMP pages.
		 *
		 * @since 1.0.190
		 * @return string, the iframe tag's structure for loading a page with the form.
		 */
		public static function get_iframe( $atts )
		{
			$url = self::site_url();
			$url = preg_replace('/^http\:/i', 'https:', $url);
			$url .= (strpos($url, '?') === false) ? '?'	: ':';
			$url .= 'cff-form='.((!empty($atts['id']))?$atts['id'] : '');
			$height = '';
			foreach($atts as $attr_name => $attr_value)
			{
				if('amp_iframe_height' == $attr_name) $height = $attr_value;
				elseif('id' != $attr_name) $url .= '&cff-form-attr-'.$attr_name.'='.$attr_value;
			}

			if(empty($height))  $height = 500;

			$url .= '&cff-form-height='.$height;

			// Fixing the isseu with the origin policy in the amp-iframes
			if(preg_match('/^https:\/\/www\./i', $url)) $url = preg_replace('/^https:\/\/www\./i', 'https://', $url);
			else  $url = preg_replace('/^https:\/\//i', 'https://www.', $url);

			add_action('amp_post_template_css', array('CPCFF_AUXILIARY', 'amp_css') );
			add_filter( 'amp_post_template_data', array('CPCFF_AUXILIARY', 'amp_iframe') );

			return '<amp-iframe id="cff-form-iframe" src="'.esc_attr( esc_url($url)).'" layout="fixed-height" sandbox="allow-popups allow-forms allow-top-navigation allow-modals allow-scripts allow-same-origin" height="'.esc_attr($height).'"><amp-img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+PHN2ZyB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iIHhtbG5zOmNjPSJodHRwOi8vY3JlYXRpdmVjb21tb25zLm9yZy9ucyMiIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyIgeG1sbnM6c3ZnPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBpZD0ic3ZnOCIgdmVyc2lvbj0iMS4xIiB2aWV3Qm94PSIwIDAgMTMuMjI5MTY3IDEzLjIyOTE2NyIgaGVpZ2h0PSI1MCIgd2lkdGg9IjUwIj48ZGVmcyBpZD0iZGVmczIiIC8+PG1ldGFkYXRhIGlkPSJtZXRhZGF0YTUiPjxyZGY6UkRGPjxjYzpXb3JrIHJkZjphYm91dD0iIj48ZGM6Zm9ybWF0PmltYWdlL3N2Zyt4bWw8L2RjOmZvcm1hdD48ZGM6dHlwZSByZGY6cmVzb3VyY2U9Imh0dHA6Ly9wdXJsLm9yZy9kYy9kY21pdHlwZS9TdGlsbEltYWdlIiAvPjxkYzp0aXRsZT48L2RjOnRpdGxlPjwvY2M6V29yaz48L3JkZjpSREY+PC9tZXRhZGF0YT48ZyB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwLC0yODMuNzcwODMpIiBpZD0ibGF5ZXIxIiAvPjwvc3ZnPg==" placeholder layout="responsive" width="50" height="50" /></amp-iframe>';
		}

		/**
		 * Includes the CSS rules for the amp version of form
		 *
		 * @sinze 1.0.190
		 *
		 * @param object, template.
		 */
		public static function amp_css($template)
		{
			print '#cff-form-iframe{margin:0;}';
		} // End amp_css

		/**
		 * Checks if the amp-iframe.js was included, and includes it if not.
		 *
		 * @since 1.0.193
		 * @param $data, associative array.
		 * @return $data, associative array.
		 */
		public static function amp_iframe($data)
		{
			if ( empty( $data['amp_component_scripts']['amp-iframe'] ) )
			{
				$data['amp_component_scripts']['amp-iframe'] = 'https://cdn.ampproject.org/v0/amp-iframe-0.1.js';
			}
			return $data;
		} // End amp_iframe

		/**
		 * Converts the corresponding parameters in an associative array.
		 *
		 * The parameters with the name cff-form is converted in the id attribute,
		 * and the parameteres with the name:  cff-form-attr-<param>, are converted in the attributes <param>
		 *
		 * @since 1.0.190
		 * @return array $attrs.
		 */
		public static function params_to_attrs()
		{
			$attrs = array();
			if(!empty($_GET))
			{
				foreach($_GET as $param => $value)
				{
					if( $param == 'cff-form')
						$attrs['id'] = @intval($value);
					elseif(preg_match('/^cff\-form\-attr\-/i', $param))
						$attrs[preg_replace('/^cff\-form\-attr\-/i', '', $param)] = $value;
				}
			}
			return $attrs;
		} // End params_to_attrs

		/**
		 * Adds the attribute: property="stylesheet" to the link tag to validate the link tags into the pages' bodies.
		 *
		 * Checks if it is an stylesheet and adds the property if has not been included previously.
		 *
		 * @since 1.0.178
		 *
		 * @param string $tag the link tag.
		 * @return string.
		 */
		public static function complete_link_tag( $tag )
		{
			if(
				preg_match('/stylesheet/i', $tag) &&
				!preg_match('/property\s*=/i', $tag)
			)
			{
				return str_replace( '/>', ' property="stylesheet" />', $tag );
			}
			return $tag;
		} // End complete_link_tag

		/**
		 * Creates a new entry in the PHP Error Logs.
		 *
		 * @since 1.0.167
		 *
		 * @param mixed $log Log message, as text, array or plain object.
		 * @return void.
		 */
		public static function write_log($log)
		{
			try{
				if(
					defined('WP_DEBUG') &&
					true == WP_DEBUG
				)
				{
					if(
						is_array( $log ) ||
						is_object( $log )
					)
					{
						error_log( print_r( $log, true ) );
					}
					else
					{
						error_log( $log );
					}
				}
			}catch(Exception $err){}
		} // End write_log

	} // End CPCFF_AUXILIARY
}