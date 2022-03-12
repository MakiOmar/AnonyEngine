<?php
/**
 * Callback for WordPress 'post_edit_form_tag' action.
 *
 * Append enctype - multipart/form-data and encoding - multipart/form-data
 * to allow image uploads for post type 'post'
 *
 * @global type $post
 * @return type
 */

add_action(
	'post_edit_form_tag',
	function () {

		global $post;

		// if invalid $post object, return
		if ( ! $post ) {
			return;
		}

		/*
		//  get the current post type
		$post_type = get_post_type($post->ID);

		//  if post type is not 'keyword', return
		if('keyword' != $post_type)
		return;

		//  append our form attributes
		printf(' enctype="multipart/form-data" encoding="multipart/form-data" ');
		*/

	}
);
/**
 * Add required scripts in footer
 */
add_action(
	'wp_print_footer_scripts',
	function() {
		$scripts  = apply_filters( 'anony_print_footer_scripts', '' );
		$domReady = apply_filters( 'anony_document_ready_jquery', '' );// For jQuery document ready scripts

		if ( empty( $scripts ) && empty( $domReady ) ) {
			return;
		}
		?>
	<script type="text/javascript">
		jQuery(document).ready(function($){
			"use strict";
			<?php echo $domReady; ?>
		});

		<?php echo $scripts; ?>

	</script>

		<?php
	}
);

/**
* Link all post thumbnails to the post permalink and remove width and height atrr from img
*
* @param  string $html          Post thumbnail HTML.
* @param  int    $post_id       Post ID.
* @param  int    $post_image_id Post image ID.
* @return string                Filtered post image HTML.
*/
add_filter(
	'post_thumbnail_html',
	function ( $html, $post_id, $post_image_id ) {

		$html = '<a href="' . esc_url( get_permalink( $post_id ) ) . '" title="' . esc_attr( get_the_title( $post_id ) ) . '">' . $html . '</a>';

		return preg_replace( '/(width|height)="\d+"\s/', '', $html );
	},
	10,
	3
);

/**
 * Define new rewrite tag, permastruct and rewrite rule for cross parent post type.
 */
add_action(
	'init',
	function() {
		$post_parents = apply_filters( 'anony_cross_parent_rewrite', array() );

		if ( empty( $post_parents ) || ! is_array( $post_parents ) ) {
			return;
		}

		foreach ( $post_parents as $post_type => $parent_post_type ) {

			add_rewrite_tag( '%' . $post_type . '%', '([^/]+)', $post_type . '=' );

			add_permastruct( $post_type, '/' . $post_type . '/%' . $parent_post_type . '%/%' . $post_type . '%', false );
			add_rewrite_rule( '^' . $post_type . '/([^/]+)/([^/]+)/?', 'index.php?' . $parent_post_type . '=$matches[2]', 'top' );

		}
	}
);

/**
 * Rewrite the permalink for cross parent post type.
 *
 * @return string Post's permalink
 */
add_filter(
	'post_type_link',
	function ( $permalink, $post, $leavename ) {

		/**
		 * Should be array of post_type => parent_post_type
		 */
		$post_parents = apply_filters( 'anony_cross_parent_permalink', array() );

		if ( empty( $post_parents ) || ! is_array( $post_parents ) ) {
			return $permalink;
		}

		if ( ! in_array( $post->post_type, array_keys( $post_parents ) ) || empty( $permalink ) || in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft' ) ) ) {
			return $permalink;
		}

		$parent_post_type = $post_parents[ $post->post_type ];

		$parent = $post->post_parent;

		$parent_post = get_post( $parent );

		$permalink = str_replace( '%' . $parent_post_type . '%', $parent_post->post_name, $permalink );

		return $permalink;
	},
	10,
	3
);
/**
 * Force https connection.
 *
 * Adds rewrite rules to htaccess to force using https.
 */
function anony_htaccess_https() {
	$rewrite = 'RewriteEngine On
	RewriteCond %{HTTPS} !=on
	RewriteRule ^.*$ https://%{SERVER_NAME}%{REQUEST_URI} [R,L]';

	return $rewrite;
}


/**
 * Google map
 *
 * You have to get the lat&lang (for example stored into meta key). also you have to define the div id (e.g. googleMap).
 * Also you have to replace the YOUR_API_KEY by your google map api key.
 * To force map language you shoud use language&region query vars with the map url
 */
add_action(
	'wp_print_footer_scripts',
	function() {
		if ( ! is_single() ) {
			return;
		}
		$lat  = get_post_meta( get_the_ID(), 'geolat', true );
		$lang = get_post_meta( get_the_ID(), 'geolong', true );

		if ( empty( $lat ) || empty( $long ) ) {
			return;
		}
		?>
	<!--API should be always before map script-->
	<script type='text/javascript' src='https://maps.googleapis.com/maps/api/js?v=3.exp&key=YOUR_API_KEY&ver=4.9.10&language=ar&region=EG'></script> 

	<script type="text/javascript">
		var Gisborne = new google.maps.LatLng(
			<?php echo $lat; ?>,
			<?php echo $long; ?>
		);
		
		function initialize(){
			var mapProp = {
					  center:Gisborne,
					  zoom:13,
					  scrollwheel: false,
					  mapTypeId:google.maps.MapTypeId.ROADMAP
				};  
			   
			var map = new google.maps.Map(document.getElementById("googleMap"),mapProp);
			
			var marker=new google.maps.Marker({
					  position:Gisborne,
					  icon:"https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png"
				});
			marker.setMap(map);
		}
		google.maps.event.addDomListener(window, "load", initialize);
	</script>
	

		<?php
	}
);

/**
 * Display only children terms
 *
 * @return array
 */
add_filter(
	'terms_clauses',
	function ( $pieces, $taxonomies, $args ) {
			// Check if our custom arguments is set and set to 1, if not bail
		if ( ! isset( $args['anony_exclude_top'] )
			 || 1 !== $args['anony_exclude_top']
			) {
			return $pieces;
		}

			// Everything checks out, lets remove parents
			$pieces['where'] .= ' AND tt.parent > 0';

			return $pieces;
	},
	10,
	3
);

// Remove items from the admin bar

add_action(
	'admin_bar_menu',
	function ( $wp_admin_bar ) {

		$items = apply_filters( 'remove_admin_bar_items', array() );

		if ( empty( $items ) ) {
			return;
		}

		/*
		* Placing items in here will only remove them from admin bar

		* when viewing the frontend of the site

		*/

		if ( ! is_admin() ) {

			foreach ( $items as $item ) {

				$wp_admin_bar->remove_node( $item );
			}
		}

	},
	999
);

/**
 * Dequeue not required styles
 *
 * @param string   'wp_print_styles' Hook to be used for dequeue
 * @param callback
 * @return void
 */
add_action(
	'wp_print_styles',
	function() {
		$dequeued_styles = apply_filters( 'anony_deprint_styles', array() );

		foreach ( $dequeued_styles as $style ) {
			wp_dequeue_style( $style );
			wp_deregister_style( $style );
		}

	},
	99
);

/**
 * Dequeue not required scripts
 *
 * @param string   'wp_print_scripts' Hook to be used for dequeue
 * @param callback
 * @return void
 */
add_action(
	'wp_print_scripts',
	function() {
		$dequeued_stcripts = apply_filters( 'anony_deprint_scripts', array() );

		foreach ( $dequeued_stcripts as $stcript ) {
			wp_dequeue_script( $stcript );
			wp_deregister_script( $stcript );
		}
	},
	99
);


/**
 * Load proper thumbnail size within loops. should be hooked to post_thumbnail_html filter
 */
function anony_loop_proper_thumb_size( $html, $post_id, $post_thumbnail_id, $size, $attr ) {

	$thumb_size = '';

	$post_type = '';

	if ( is_single() || empty( $thumb_size ) || empty( $post_type ) || get_post_type() !== $post_type ) {
		return $html;
	}

	// gets the id of the current post_thumbnail (in the loop)
	$id = get_post_thumbnail_id();

	 // gets the image url specific to the passed in size (aka. custom image size)
	$src = wp_get_attachment_image_src( $id, $thumb_size );

	// gets the post thumbnail title
	$alt = get_the_title( $id );

	// gets classes passed to the post thumbnail, defined here for easier function access
	$class = $attr['class'];

		$html = '<img src="' . $src[0] . '" alt="' . $alt . '" class="' . $class . '" width="277" height="316" />';

	return $html;
}

/**
 * Search products by meta value OR title
 */
add_action(
	'pre_get_posts',
	function ( $query ) {
		if ( ! is_admin() && is_search() ) {
			$meta_or_title = apply_filters( 'meta_key_or', false );
			$meta_key      = apply_filters( 'meta_key_or', '' );

			if ( ! $meta_or_title || $meta_key == '' ) {
				return;
			}

			$query->set( '_meta_or_title', get_search_query() );
			$query->set( 'meta_key', $meta_key );
			$query->set(
				'meta_query',
				array(
					'relation' => 'OR',
					array(
						'key'     => $meta_key,
						'compare' => 'LIKE',
						'value'   => get_search_query(),
					),
				)
			);
			if ( $title = $query->get( '_meta_or_title' ) ) {
				add_filter(
					'get_meta_sql',
					function( $sql ) use ( $title ) {
						global $wpdb;

						// Only run once:
						static $nr = 0;
						if ( 0 != $nr++ ) {
							return $sql;
						}

						// Modify WHERE part:
						$sql['where'] = sprintf(
							' AND ( %s OR %s ) ',
							$wpdb->prepare( "{$wpdb->posts}.post_title LIKE '%s'", $title ),
							mb_substr( $sql['where'], 5, mb_strlen( $sql['where'] ) )
						);
						return $sql;
					}
				);
			}
		}
	}
);


/**
 * Get related products by meta.
 * Has the ability to get results from same category or other categories.
 */
add_action(
	'init',
	function() {

		$related_products_by_cat = apply_filters( 'anony_related_products_by_cat', false );

		if ( $related_products_by_cat ) {
			// Stop loading from same tags
			add_filter( 'woocommerce_product_related_posts_relate_by_tag', '__return_false', 100 );

			// Force loading from categories/terms
			add_filter( 'woocommerce_product_related_posts_relate_by_category', '__return_true', 100 );

			$related_products_by_meta = apply_filters( 'anony_related_products_by_meta', false );

			$relation_meta_key = apply_filters( 'anony_relation_meta_key', '' );

			if ( $related_products_by_meta ) {

				if ( empty( $relation_meta_key ) ) {
					die( 'Meta key name is missing. please check filter {anony_relation_meta_key}' );
				}

				add_filter(
					'woocommerce_product_related_posts_query',
					function ( $query, $product_id ) use ( $relation_meta_key ) {

						global $wpdb;
						$relation_meta_value = get_post_meta( $product_id, $relation_meta_key, true );
						$query['join']      .= " INNER JOIN {$wpdb->postmeta} as pm ON p.ID = pm.post_id ";
						$query['where']     .= " AND pm.meta_key = '$relation_meta_key' AND meta_value LIKE '$relation_meta_value' ";
						return $query;
					},
					100,
					2
				);

				$related_products_by_meta_outsider = apply_filters( 'anony_related_products_by_meta_outsider', false );

				if ( $related_products_by_meta_outsider ) {
					// Load related from any category
					add_filter(
						'woocommerce_get_related_product_cat_terms',
						function( $ids, $product_id ) {
							$terms = get_terms(
								array(
									'taxonomy'   => 'product_cat',
									'hide_empty' => false,
									'fields'     => 'ids',
								)
							);

							return $terms;

						},
						11,
						2
					);
				}
			}
		}
	}
);
