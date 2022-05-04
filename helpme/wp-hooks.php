<?php
/**
 * Helpers hooks.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine.
 * @author   Makiomar <info@makior.com>.
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

/**
 * Append enctype and encoding - multipart/form-data to allow image uploads for a post type's metabox.
 *
 * @param object $post Post type's object.
 */
function anony_post_metabox_form_multipart( $post ) {

	$add_to = apply_filters( 'anony_post_metabox_form_multipart', array() );

	if ( in_array( get_post_type( $post->ID ), $add_to, true ) ) {
		// Append multipart/form-data form attributes.
		printf( ' enctype="multipart/form-data" encoding="multipart/form-data" ' );
	}

}
add_action( 'post_edit_form_tag', 'anony_post_metabox_form_multipart' );

/**
 * Remove Width and height attributes from post's thumbnail.
 *
 * @param  string $html          Post thumbnail HTML.
 * @param  int    $post_id       Post ID.
 * @param  int    $post_image_id Post image ID.
 * @return string                Filtered post image HTML.
 */
function anony_remove_thumb_style_dimensions( $html, $post_id, $post_image_id ) {
	$html = sprintf(
		'<a href="%1$s" title="%2$s">%3$s</a>',
		esc_url( get_permalink( $post_id ) ),
		esc_attr( get_the_title( $post_id ) ),
		$html
	);

	return preg_replace( '/(width|height)="\d+"\s/', '', $html );
}
add_filter( 'post_thumbnail_html', 'anony_remove_thumb_style_dimensions', 10, 3 );


/**
 * Display only children terms.
 * **Description** To exclude top level parents, we can now pass <code>'anony_exclude_top' => 1</code> with <code>get_terms</code> array of arguments.
 *
 * @param array $pieces An array of query SQL clauses.
 * @param array $taxonomies An array of taxonomy names.
 * @param array $args An array of term query arguments.
 * @return array
 */
function anony_exclude_top_level_terms( $pieces, $taxonomies, $args ) {
	// Check if our custom arguments is set and set to 1, if not bail.
	if ( ! isset( $args['anony_exclude_top'] )
		|| 1 !== $args['anony_exclude_top']
		) {
		return $pieces;
	}

	// Everything checks out, lets remove parents.
	$pieces['where'] .= ' AND tt.parent > 0';

	return $pieces;
}
add_filter( 'terms_clauses', 'anony_exclude_top_level_terms', 10, 3 );

/**
 * Remove items from the admin bar.
 *
 * @param WP_Admin_Bar $wp_admin_bar The WP_Admin_Bar instance, passed by reference.
 * @param string       $admin_also Flag to determine weather to also remove within admin. Default is 'no'.
 */
function remove_admin_bar_items( $wp_admin_bar, $admin_also = 'no' ) {

	$items = apply_filters( 'remove_admin_bar_items', array() );

	if ( empty( $items ) ) {
		return;
	}

	if ( ! is_admin() || 'yes' === $admin_also ) {

		foreach ( $items as $item ) {

			$wp_admin_bar->remove_node( $item );
		}
	}
}

add_action( 'admin_bar_menu', 'remove_admin_bar_items', 999 );

/**
 * Dequeue not required styles.
 */
function anony_deprint_styles() {
	$dequeued_styles = apply_filters( 'anony_deprint_styles', array() );

	foreach ( $dequeued_styles as $style ) {
		wp_dequeue_style( $style );
		wp_deregister_style( $style );
	}
}
add_action( 'wp_print_styles', 'anony_deprint_styles', 99 );

/**
 * Dequeue not required scripts.
 */
function anony_deprint_scripts() {
	$dequeued_stcripts = apply_filters( 'anony_deprint_scripts', array() );

	foreach ( $dequeued_stcripts as $stcript ) {
		wp_dequeue_script( $stcript );
		wp_deregister_script( $stcript );
	}
}
add_action( 'wp_print_scripts', 'anony_deprint_scripts', 99 );


/**
 * Load proper thumbnail size within loops. should be hooked to post_thumbnail_html filter.
 *
 * @param string       $html The post thumbnail HTML.
 * @param int          $post_id The post ID.
 * @param int          $post_thumbnail_id The post thumbnail ID, or 0 if there isn't one.
 * @param string|int[] $size Requested image size. Can be any registered image size name, or an array of width and height values in pixels (in that order).
 * @param string|array $attr Query string or array of attributes.
 */
function anony_loop_proper_thumb_size( $html, $post_id, $post_thumbnail_id, $size, $attr ) {

	$post_type = 'post';

	$init = apply_filters( "anony_{$post_type}_loop_proper_thumb_size", false );

	if ( ! $init ) {
		return $html;
	}

	// gets the image url specific to the passed in size (aka. custom image size).
	$src = wp_get_attachment_image_src( $post_thumbnail_id, $size );

	$img_size = getimagesize( $src[0] );

	if ( false === $img_size ) {
		return $html;
	}

	// gets the post thumbnail title.
	$alt = get_the_title( $post_thumbnail_id );

	$html = sprintf(
		'<img src="%1$s" alt="%2$s" class="%3$s" %4$s/>',
		esc_url( $src[0] ),
		esc_attr( $alt ),
		esc_attr( $attr['class'] ),
		$img_size[3]
	);

	return $html;
}

add_filter( 'post_thumbnail_html', 'anony_loop_proper_thumb_size', 10, 5 );

/**
 * Search products by meta key OR title.
 *
 * @param WP_Query $query The WP_Query instance (passed by reference).
 */
function anony_search_products_by_metakey_or_title( $query ) {
	if ( ! is_admin() && is_search() ) {

		$init = apply_filters( 'anony_search_products_by_metakey_or_title', false );

		$meta_key = apply_filters( 'meta_key', '' );

		if ( ! $init || '' === $meta_key ) {
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

		$title = $query->get( '_meta_or_title' );

		if ( $title ) {
			add_filter(
				'get_meta_sql',
				function( $sql ) use ( $title ) {
					global $wpdb;

					// Only run once.
					static $nr = 0;
					// phpcs:disable WordPress.PHP.StrictComparisons.LooseComparison
					if ( 0 != $nr++ ) {
						return $sql;
					}
					// phpcs:enable.

					// Modify WHERE part.
					$sql['where'] = sprintf(
						' AND ( %s OR %s ) ',
						$wpdb->prepare( "{$wpdb->posts}.post_title LIKE %s", $title ),
						mb_substr( $sql['where'], 5, mb_strlen( $sql['where'] ) )
					);
					return $sql;
				}
			);
		}
	}
}

add_action( 'pre_get_posts', 'anony_search_products_by_metakey_or_title' );


/**
 * Get related products by meta.
 * Has the ability to get results from same category or other categories.
 */
function anony_get_related_products_by_meta_key() {
	$init = apply_filters( 'anony_related_products_meta_key', '' );
	$relation_meta_key = apply_filters( 'anony_related_products_meta_key', '' );

	if ( ! empty( $relation_meta_key ) ) {
		// Stop loading from same tags.
		add_filter( 'woocommerce_product_related_posts_relate_by_tag', '__return_false', 100 );

		// Force loading from categories/terms.
		add_filter( 'woocommerce_product_related_posts_relate_by_category', '__return_true', 100 );

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
			// Load related from any category.
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
add_action( 'init', 'anony_get_related_products_by_meta_key' );
