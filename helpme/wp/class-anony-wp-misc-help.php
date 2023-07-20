<?php
/**
 * WP miscellaneous helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine.
 * @author   Makiomar <info@makiomar.com>.
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_Wp_Misc_Help' ) ) {

	/**
	 * WP miscellaneous helpers.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine.
	 * @author   Makiomar <info@makiomar.com>.
	 * @license  https:// makiomar.com AnonyEngine Licence.
	 * @link     https:// makiomar.com/anonyengine
	 */
	class ANONY_Wp_Misc_Help extends ANONY_HELP {
		/**
		 * Gets revolution slider list of silders.
		 *
		 * @return array  Associative array of sliders (id = name).
		 */
		public static function get_rev_sliders() {
			$sliders = array();

			if ( class_exists( 'RevSlider' ) ) {

				$rev_slider = new RevSlider();

				foreach ( $rev_slider->getAllSliderAliases() as $slider ) {

					$sliders[ $slider ] = ucfirst( str_replace( '-', ' ', $slider ) );

				}
			}

			return $sliders;
		}

		/**
		 * Get timestamp of remaining time for WordPress transient to be expired.
		 *
		 * @param  string $transient              the transient name you want to get.
		 * @var    object   $wpdb                   the WordPress database object.
		 * @var    array    $transient_timeout      array contains the transient time for expiry.
		 * @return string                           timestamp of transient expiry.
		 */
		public static function get_transient_timeout( $transient ) {
			// If the transient does not exist, does not have a value, or has expired, then get_transient will return false.
			if ( ! get_transient( $transient ) ) {
				return false;
			}

			global $wpdb;

			$prepared_query = $wpdb->prepare(
				"
				SELECT 
					option_value
			  	FROM 
			  		$wpdb->options
			  	WHERE 
			  		option_name
			  	LIKE %s
				",
				"%_transient_timeout_{$transient}%"
			);

			$cache_key = "get_transient_timeout_{$transient}";

			$transient_timeout = ANONY_Wp_Db_Help::get_col( $prepared_query, $cache_key, 0, '', 3600 );

			return $transient_timeout[0];
		}

		/**
		 * Lists all functions which are hooked to afilter.
		 *
		 * @param string $hook A substring of the hook name.
		 */
		public static function list_hook_filters( $hook ) {
			global $wp_filter;

			$filters = array();

			foreach ( $wp_filter as $key => $val ) {
				if ( false !== strpos( $key, $hook ) ) {
					// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_export
					$filters[ $key ][] = var_export( $val, true );
					// phpcs:enable.
				}
			}

			ANONY_Wp_Debug_Help::error_log( $filters );
		}

		/**
		 * Get current page url.
		 *
		 * **Description: ** Gets current page url and takes in account the ssl.
		 *
		 * @return string
		 */
		public static function get_current_request_url() {
			global $wp;
			return home_url( $wp->request );
		}

		/**
		 * Google map
		 *
		 * You have to get the lat&lang (for example stored into meta key). also you have to define the div id (e.g. googleMap).
		 * Also you have to replace the YOUR_API_KEY by your google map api key.
		 * To force map language you shoud use language&region query vars with the map url.
		 *
		 * @param array $args map_api,target_id,latitude,longitude  Required data for map to work.
		 */
		public static function anony_google_map_init( array $args ) {

			if ( empty( $args['target_id'] ) || empty( $args['latitude'] ) || empty( $args['longitude'] ) ) {
				ANONY_Wp_Debug_Help::error_log( 'Map arguments is not complete' );
			}

			$lat  = $args['latitude'];
			$long = $args['longitude'];

			$region   = ! empty( $args['region'] ) ? $args['region'] : 'EG';
			$language = ! empty( $args['language'] ) ? $args['language'] : 'ar';

			$engine_options = ANONY_Options_Model::get_instance( ANONY_ENGINE_OPTIONS );

			if ( ! empty( $engine_options->google_maps_api_key ) && '1' === $engine_options->enable_google_maps_script ) {

				$script_src = sprintf(
					'https://maps.googleapis.com/maps/api/js?v=3.exp&key=%1$s&ver=4.9.10&language=%2$s&region=%3$s',
					$engine_options->google_maps_api_key,
					$language,
					$region
				);
				?>
				<!--API should be always before map script-->
				<?php // phpcs:disable  ?>
				<script type='text/javascript' src='<?php echo esc_url( $script_src ); ?>'></script>
				<?php // phpcs:enable.  ?>
			<?php } ?>

			<script type="text/javascript">
				var Gisborne = new google.maps.LatLng(
					<?php echo esc_html( $lat ); ?>,
					<?php echo esc_html( $long ); ?>
				);

				function initialize(){
					var mapProp = { center:Gisborne, zoom:13, scrollwheel: false, mapTypeId:google.maps.MapTypeId.ROADMAP };
					   
					var map = new google.maps.Map(document.getElementById("<?php echo esc_html( $args['target_id'] ); ?>"),mapProp);

					var marker=new google.maps.Marker({ position:Gisborne, icon:"https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png" });
					marker.setMap(map);
				}
				google.maps.event.addDomListener(window, "load", initialize);
			</script>
			<?php
		}

		/**
		 * Force https connection.
		 *
		 * Adds rewrite rules to htaccess to force using https.
		 */
		public static function anony_htaccess_https() {
			// phpcs:disable Generic.CodeAnalysis.EmptyStatement.DetectedIf
			if ( is_multisite() ) {

				// I'm not going to go here.

			} else {
				// phpcs: enable.
				// Ensure get_home_path() is declared.
				require_once ABSPATH . 'wp-admin/includes/file.php';

				$home_path = get_home_path();

				$htaccess_file = $home_path . '.htaccess';

				$lines = array(
					'RewriteEngine On',
					'RewriteCond %{HTTPS} !=on',
					'RewriteRule ^.*$ https://%{SERVER_NAME}%{REQUEST_URI} [R,L]',
				);

				// If your rules are for rewrites, then WP also has a helper function to check you are using Apache with mod_rewrite enabled.
				if ( got_mod_rewrite() ) {

					// insert_with_markers() is a built in function, that checks whether the file exists & is writable, creating it if necessary. It inserts your data into the file between # BEGIN and # END markers, replacing any lines that already exist between those markers. You can pass it an array of lines or a string with line breaks. Make sure your marker name is unique to avoid clashing with WP core or other plugins.

					insert_with_markers( $htaccess_file, 'anony_force_https', $lines );
				}
			}
		}

		/**
		 * Get lis to js files loaded with a post/page.
		 * 
		 * @param int $post_id Post's ID.
		 * 
		 * @return array An array of js files urls.
		 */ 
		public static function get_post_scripts( $post_id ) {
			
		    // Let's get the content of post number 123
		    $response = wp_remote_get( get_the_permalink($post_id) .'?list_scripts=1' );
		   
		    // An empty array to store all the 'srcs'
			$scripts_array = [];

		    if ( is_array( $response ) ) {

		      $content = $response['body'];

		      if( !$content || empty( $content ) )
		      {
		      	return $scripts_array;
		      }
		       
		      $document = new DOMDocument();

		      @$document->loadHTML( $content );

		      // Store every script's source inside the array.
		      foreach( $document->getElementsByTagName('script') as $script ) {

		        if( $script->hasAttribute('src') ) {

		          $scripts_array[$script->getAttribute('src')] =  $script->getAttribute('src');

		        }

		      }

		    }

		    return $scripts_array;

		}

		public static function list_post_scripts () {
			if (defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				return;
			}
			

			if ( !empty( $_GET['post'] ) ) {

				$scripts = self::get_post_scripts( $_GET['post'] );
				
				if( !empty( $scripts ) )
				{
					return $scripts;
				}
				
				
			}

			return array();
			
		}

		/**
		 * Get the current archive post type name (e.g: post, page, product).
		 *
		 * @return String|Boolean  The archive post type name or false if not in an archive page.
		 */
		public static function get_archive_post_type() {
		    return is_archive() ? get_queried_object()->name : false;
		}

		public static function anony_get_current_permalink_shortcode() {
			global $post;
		
			$permalink = '';
		
			if (is_singular()) {
				$permalink = get_permalink($post->ID);
			} elseif (is_post_type_archive()) {
				$post_type = get_post_type();
				$permalink = get_post_type_archive_link($post_type);
			} elseif (is_tax() || is_category() || is_tag()) {
				$term = get_queried_object();
				$permalink = get_term_link($term);
			} elseif (is_archive()) {
				$permalink = get_the_archive_link();
			}
		
			return $permalink;
		}
		public static function current_object_title() {
			
		
			$permalink = '';
			$title = '';
		
			if (is_singular()) {
				global $post;
				$permalink = get_permalink($post->ID);
				$title = '<a href="'.$permalink.'">'.$post->post_title.'</a>';
			} elseif (is_post_type_archive()) {
				$post_type = get_post_type();
				$permalink = get_post_type_archive_link($post_type);
				$post_type_object = get_post_type_object($post_type);
				if ($post_type_object) {
					$post_type_label = $post_type_object->labels->name; // or use 'singular_name' for the singular label
					$title = '<a href="'.$permalink.'">'.$post_type_label.'</a>';
				}
				
			} elseif (is_tax() || is_category() || is_tag()) {
				$term = get_queried_object();
				$permalink = get_term_link($term);
				$title = '<a href="'.$permalink.'">'.$term->name.'</a>';
			} elseif (is_archive()) {
				$permalink = get_the_archive_link();
				$queried_object = get_queried_object();
				if ($queried_object && isset($queried_object->label)) {
					$archive_label = $queried_object->label;
					$title = '<a href="'.$permalink.'">'.$archive_label.'</a>';
				}
			}
		
			return $title;
		}

	}
}
