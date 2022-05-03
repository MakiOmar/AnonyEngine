<?php
/**
 * WP miscellaneous helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine.
 * @author   Makiomar <info@makior.com>.
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
	 * @author   Makiomar <info@makior.com>.
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

			if ( ! empty( $args['map_api'] ) ) {

				$script_src = sprintf(
					'https://maps.googleapis.com/maps/api/js?v=3.exp&key=%1$s&ver=4.9.10&language=%2$s&region=%3$s',
					$args['map_api'],
					$language,
					$region
				);
				?>
				<!--API should be always before map script-->
				<?php // phpcs:disable  ?>
				<script type='text/javascript' src='<?php echo esc_attr( $script_src ); ?>'></script>
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
	}
}
