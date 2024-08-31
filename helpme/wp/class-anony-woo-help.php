<?php
/**
 * Woocommerce helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine.
 * @author   Makiomar <info@makiomar.com>.
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'ANONY_Woo_Help' ) ) {
	/**
	 * Woocommerce helpers class.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine.
	 * @author   Makiomar <info@makiomar.com>.
	 * @license  https:// makiomar.com AnonyEngine Licence...
	 * @link     https:// makiomar.com/anonyengine.
	 */
	class ANONY_Woo_Help extends ANONY_HELP {
		/**
		 * Recursive function to get category breadcrumbs
		 *
		 * @param int    $term_id Term ID.
		 * @param string $taxonomy Taxonomy slug.
		 * @return string
		 */
		public static function get_category_breadcrumbs( $term_id, $taxonomy ) {
			$term        = get_term( $term_id, $taxonomy );
			$breadcrumbs = '';

			if ( $term && ! is_wp_error( $term ) ) {
				// Check if the term has a parent.
				if ( $term->parent > 0 ) {
					$parent_breadcrumbs = self::get_category_breadcrumbs( $term->parent, $taxonomy );
					$breadcrumbs       .= $parent_breadcrumbs . ' / ';
				}

				$breadcrumbs .= '<a href="' . esc_url( get_term_link( $term->term_id ) ) . '" title="' . esc_attr( $term->name ) . '">' . esc_html( $term->name ) . '</a>';
			}

			return $breadcrumbs;
		}
		/**
		 * Render category breadcrumps
		 *
		 * @return void
		 */
		public static function render_category_breadcrumps() {
			global $post;
			// Get the product categories.
			$categories = get_the_terms( $post->ID, 'product_cat' );

			// Check if categories exist.
			if ( $categories && ! is_wp_error( $categories ) ) {
				$category_breadcrumbs = array();

				// Loop through each category.
				foreach ( $categories as $category ) {
					$category_breadcrumbs[] = self::get_category_breadcrumbs( $category->term_id, 'product_cat' );
				}
				echo '<nav class="woocommerce-breadcrumb anony-woocommerce-breadcrumb" aria-label="Breadcrumb">';
				// Output the breadcrumbs.
				//phpcs:disable
				echo implode( ' / ', apply_filters( 'anony_woocommerce_category_breadcrumps', $category_breadcrumbs ) );
				//phpcs:enable
				echo '</nav>';
			}
		}
		/**
		 * Search products by meta key OR title.
		 *
		 * @param WP_Query $query The WP_Query instance (passed by reference).
		 */
		public static function anony_search_products_by_metakey_or_title( $query ) {
			add_action(
				'pre_get_posts',
				function () {
					if ( ! is_admin() && is_search( $query ) ) {

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
								function ( $sql ) use ( $title ) {
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
			);
		}

		/**
		 * Get related products by meta.
		 * Has the ability to get results from same category or other categories.
		 */
		public static function anony_get_related_products_by_meta_key() {
			add_action(
				'init',
				function () {
					$init              = apply_filters( 'anony_related_products_meta_key', '' );
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
								function ( $ids, $product_id ) {
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
			);
		}


		public static function remove_order_status_change_notes() {
			add_action(
				'woocommerce_order_status_changed',
				function ( $order_id, $status_from, $status_to ) {
					$transition_note = sprintf( __( 'Order status changed from %1$s to %2$s.', 'woocommerce' ), wc_get_order_status_name( $status_from ), wc_get_order_status_name( $status_to ) );
					add_filter(
						'woocommerce_new_order_note_data',
						function ( $args ) use ( $transition_note ) {
							if ( $args['comment_content'] === $transition_note ) {
								return array();
							} else {
								return $args;
							}
						}
					);
				},
				10,
				3
			);
		}

		/**
		 * Get all approved WooCommerce order notes.
		 * wc_get_order_notes can be used instead. for more accurate results.
		 *
		 * @param  int|string $order_id The order ID.
		 * @return array      $notes    The order notes, or an empty array if none.
		 */
		public static function get_order_notes( $order_id ) {

			remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ) );

			$comments = get_comments(
				array(
					'post_id' => $order_id,
					'orderby' => 'comment_ID',
					'order'   => 'DESC',
					'approve' => 'approve',
					'type'    => 'order_note',
				)
			);

			$notes = wp_list_pluck( $comments, 'comment_content' );

			add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ) );

			return $notes;
		}
		/**
		 * Get customer's paid orders
		 *
		 * @param int $user_id Customer's ID.
		 * @return array An array of Customer's paid oders.
		 */
		public static function get_customer_paid_orders( $user_id ) {

			$customer_orders = array();
			foreach ( wc_get_is_paid_statuses() as $paid_status ) {
				$customer_orders += wc_get_orders(
					array(
						'type'        => 'shop_order',
						'limit'       => - 1,
						'customer_id' => $user_id,
						'status'      => $paid_status,
					)
				);
			}

			return $customer_orders;
		}

		/**
		 * Get customer's paid orders
		 *
		 * @param int    $user_id Customer's ID.
		 * @param string $meta_key Meta key.
		 * @param string $meta_value Meta value.
		 * @return array An array of Customer's paid oders.
		 */
		public static function get_customer_paid_orders_by_meta( $user_id, $meta_key, $meta_value ) {

			$customer_orders = array();
			foreach ( wc_get_is_paid_statuses() as $paid_status ) {
				$customer_orders += wc_get_orders(
					array(
						'type'           => 'shop_order',
						'limit'          => - 1,
						'customer_id'    => $user_id,
						'status'         => $paid_status,
						'_with_meta_key' => $meta_key . '|' . $meta_value,
					)
				);
			}

			return $customer_orders;
		}
		/**
		 * Get products by meta key/value
		 *
		 * @param string $meta_key Meta key.
		 * @param string $meta_vlaue Meta value.
		 * @return array
		 */
		public static function get_products_by_usermeta( $meta_key, $meta_vlaue ) {
			global$wpdb;
			$products_ids = ANONY_Wp_Db_Help::get_result(
				$wpdb->prepare(
					"SELECT p.ID FROM {$wpdb->prefix}posts AS p
                    INNER JOIN {$wpdb->prefix}users AS u ON p.post_author = u.ID
                    INNER JOIN {$wpdb->prefix}usermeta AS um ON u.ID = um.user_id
                    WHERE p.post_type = 'product'
                    AND p.post_status = 'publish'
                    AND um.meta_key = %s
                    AND CAST(um.meta_value AS CHAR CHARACTER SET utf8mb4) LIKE %s",
					$meta_key,
					'%' . $meta_vlaue . '%'
				),
				'anony_products_by_' . $meta_key
			);

			return $products_ids;
		}
		/**
		 * Handles order custom meta query var
		 *
		 * **Description** Should be hooked to used like this <code>add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', 'handle_order_custom_meta_query_var', 10, 2 );</code>
		 *
		 * @param array $query An Array of query parameters.
		 * @param array $query_vars An Array of query variables.
		 *
		 * @return array An Array of query parameters.
		 */
		public static function handle_order_custom_meta_query_var( $query, $query_vars ) {
			if ( ! empty( $query_vars['_with_meta_key'] ) ) {

				$meta_data = explode( '|', $query_vars['_with_meta_key'] );

				$query['meta_query'][] = array(
					'key'   => $meta_data[0],
					'value' => esc_attr( $meta_data[1] ),
				);
			}

			return $query;
		}
		/**
		 * Create product attribute.
		 *
		 * @param string $label_name Attribute name (Not sanitized).
		 */
		public static function create_product_attribute( $label_name ) {

			if ( ! class_exists( 'woocommerce' ) ) {
				return;
			}

			global $wpdb;

			$slug = sanitize_title( $label_name );

			$attr_exists = false;

			if ( strlen( $slug ) >= 28 ) {
				// translators: %s is attribute slug.
				return new WP_Error( 'invalid_product_attribute_slug_too_long', sprintf( __( 'Name "%s" is too long (28 characters max). Shorten it, please.', 'woocommerce' ), $slug ), array( 'status' => 400 ) );

			} elseif ( wc_check_if_attribute_name_is_reserved( $slug ) ) {
				// translators: %s is attribute slug.
				return new WP_Error( 'invalid_product_attribute_slug_reserved_name', sprintf( __( 'Name "%s" is not allowed because it is a reserved term. Change it, please.', 'woocommerce' ), $slug ), array( 'status' => 400 ) );

			} elseif ( taxonomy_exists( wc_attribute_taxonomy_name( $slug ) ) ) {
				// translators: %s is attribute label name.
				return new WP_Error( 'invalid_product_attribute_slug_already_exists', sprintf( __( 'Name "%s" is already in use. Change it, please.', 'woocommerce' ), $label_name ), array( 'status' => 400 ) );

			} elseif ( function_exists( 'wc_get_attribute_taxonomies' ) && wc_get_attribute_taxonomies() ) {

				foreach ( wc_get_attribute_taxonomies() as $key => $value ) {
					if ( $value->attribute_name === $slug ) {
						// translators: %s is attribute label name.
						return new WP_Error( 'invalid_product_attribute_slug_already_exists', sprintf( __( 'Name "%s" is already in use. Change it, please.', 'woocommerce' ), $label_name ), array( 'status' => 400 ) );
					}
				}
			}

			$data = array(
				'attribute_label'   => $label_name,
				'attribute_name'    => $slug,
				'attribute_type'    => 'select',
				'attribute_orderby' => 'menu_order',
				'attribute_public'  => 0, // Enable archives ==> true (or 1).
			);

			// phpcs:disable
			$results = $wpdb->insert( "{$wpdb->prefix}woocommerce_attribute_taxonomies", $data );
			// phpcs:enable.

			if ( is_wp_error( $results ) ) {
				return new WP_Error( 'cannot_create_attribute', $results->get_error_message(), array( 'status' => 400 ) );
			}

			$id = $wpdb->insert_id;

			do_action( 'woocommerce_attribute_added', $id, $data );

			wp_schedule_single_event( time(), 'woocommerce_flush_rewrite_rules' );

			delete_transient( 'wc_attribute_taxonomies' );
		}

		/**
		 * Add product review.
		 *
		 * @param array $comment_data Array of arguments for inserting a new comment (Required).
		 * @param int   $rating Rating value.
		 */
		public static function add_product_review( $comment_data = '', $rating = 3 ) {
			global $post, $product;

			if ( 'product' !== $post->post_type ) {
				return;
			}
			$agent        = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
			$comment_data = array(
				'comment_post_ID'      => $post->ID,
				'comment_author'       => 'أحمد',
				'comment_author_email' => 'dave@domain.com',
				'comment_author_url'   => 'http://www.someiste.com',
				'comment_content'      => 'أكثر من رائع',
				'comment_author_IP'    => '127.3.1.1',
				'comment_agent'        => $agent,
				'comment_type'         => '',
				'comment_date'         => gmdate( 'Y-m-d H:i:s' ),
				'comment_date_gmt'     => gmdate( 'Y-m-d H:i:s' ),
				'comment_approved'     => 1,

			);

			if ( ! get_comment_meta( 'rating' ) ) {

				$comment_id = wp_insert_comment( $comment_data );

				// HERE inserting the rating (an integer from 1 to 5).
				update_comment_meta( $comment_id, 'rating', $rating );
			}
		}

		/**
		 * Replace coupon's form inside order's details.
		 */
		public static function coupon_form_in_order_details() {
			// Just hide default woocommerce coupon field.
			add_action( 'woocommerce_before_checkout_form', array( 'ANONY_Woo_Help', 'hide_checkout_coupon_form' ), 5 );

			// Add a custom coupon field before checkout payment section.
			add_action( 'woocommerce_review_order_before_payment', array( 'ANONY_Woo_Help', 'custom_checkout_coupon_form' ) );

			// jQuery code.
			add_action( 'wp_footer', array( 'ANONY_Woo_Help', 'custom_checkout_jquery_script' ) );
		}

		/**
		 * Hides checkout coupon form.
		 */
		public static function hide_checkout_coupon_form() {
			echo '<style>.woocommerce-form-coupon-toggle, .coupon-form {display:none;}</style>';
		}

		/**
		 * Custom coupon form markup.
		 */
		public static function custom_checkout_coupon_form() {

			// phpcs:disable

			echo '<div class="checkout-coupon-toggle"><div class="woocommerce-info">' . esc_html__( 'Have a coupon?', 'woocommerce' ) . ' <a href="#" class="show-coupon">' . esc_html__( 'Click here to enter your code', 'woocommerce' ) . '</a>' . '</div></div>';

			// phpcs:enable.

			printf(
				'

					<div class="coupon-form-wrapper">
						<div class="coupon-form" style="margin-bottom:20px;">
							<p>%1$s</p>
							<p class="form-row form-row-first woocommerce-validated">
								<input type="text" name="coupon_code" class="input-text" placeholder="%2$s" id="coupon_code" value="">
							</p>
							<p class="form-row form-row-last">
								<button type="button" class="button" name="apply_coupon" value="%3$s">%4$s</button>
							</p>
							<div class="clear"></div>
						</div>
					</div>

					',
				esc_html__( 'If you have a coupon code, please apply it below.', 'woocommerce' ),
				esc_attr__( 'Coupon code', 'anonyengine' ),
				esc_attr__( 'Apply coupon', 'anonyengine' ),
				esc_html__( 'Apply coupon', 'anonyengine' )
			);
		}

		/**
		 * Custom jquery for coupon form.
		 */
		public static function custom_checkout_jquery_script() {
			if ( is_checkout() && ! is_wc_endpoint_url() ) :?>
					<script type="text/javascript">
					jQuery( function($){
						$('form.checkout_coupon input[name="coupon_code"]').val($('.coupon-form input[name="coupon_code"]').val());
						// Show or Hide coupon field.
						$('.checkout-coupon-toggle .show-coupon').on( 'click', function(e){
							$('.coupon-form').toggle(200);
							e.preventDefault();
						})

						// Copy the inputed coupon code to WooCommerce hidden default coupon field.
						$('.coupon-form input[name="coupon_code"]').on( 'input change', function(){
							$('form.checkout_coupon input[name="coupon_code"]').val($(this).val());
							// console.log($(this).val()); // Uncomment for testing.
						});

						// On button click, submit WooCommerce hidden default coupon form.
						$('.coupon-form button[name="apply_coupon"]').on( 'click', function(){
							$('form.checkout_coupon input[name="coupon_code"]').val($('.coupon-form input[name="coupon_code"]').val());
							$('form.checkout_coupon').submit();
							// console.log('click: submit form'); // Uncomment for testing.
						});
					});
					</script>
					<?php
				endif;
		}

		/**
		 * Show lowest variation price
		 */
		public static function show_only_lowest_variation_price() {
			add_filter( 'woocommerce_variable_sale_price_html', array( 'ANONY_Woo_Help', 'lowest_variation_price_format' ), 10, 2 );
			add_filter( 'woocommerce_variable_price_html', array( 'ANONY_Woo_Help', 'lowest_variation_price_format' ), 10, 2 );
		}

		/**
		 * Lowest variation price format.
		 *
		 * @param string $price Format HTML.
		 * @param object $product product object.
		 */
		public static function lowest_variation_price_format( $price, $product ) {
			// Main Price.
			$prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
			// translators: %1$s is variation price.
			$price = $prices[0] !== $prices[1] ? sprintf( __( 'Starts from: %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

			// Sale Price.
			$prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
			sort( $prices );

			// translators: %1$s is variation sale price.
			$saleprice = $prices[0] !== $prices[1] ? sprintf( __( 'Starts from: %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

			if ( $price !== $saleprice ) {
				$price = '<del>' . $saleprice . $product->get_price_suffix() . '</del> <ins>' . $price . $product->get_price_suffix() . '</ins>';
			}
			return $price;
		}


		/**
		 * Show highest variation price
		 */
		public static function show_only_highest_variation_price() {
			add_filter( 'woocommerce_variable_sale_price_html', array( 'ANONY_Woo_Help', 'highest_variation_price_format' ), 10, 2 );
			add_filter( 'woocommerce_variable_price_html', array( 'ANONY_Woo_Help', 'highest_variation_price_format' ), 10, 2 );
		}

		/**
		 * Highest variation markup.
		 */
		public static function highest_variation_price_format() {

			// Main Price.
			$prices = array( $product->get_variation_price( 'max', true ), $product->get_variation_price( 'min', true ) );

			// translators: %1$s is variation price.
			$price = $prices[0] !== $prices[1] ? sprintf( __( 'Up To: %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

			// Sale Price.
			$prices = array( $product->get_variation_regular_price( 'max', true ), $product->get_variation_regular_price( 'min', true ) );
			sort( $prices );

			// translators: %1$s is variation sale price.
			$saleprice = $prices[0] !== $prices[1] ? sprintf( __( 'Up To: %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

			if ( $price !== $saleprice ) {
				$price = '<del>' . $saleprice . $product->get_price_suffix() . '</del> <ins>' . $price . $product->get_price_suffix() . '</ins>';
			}
			return $price;
		}

		/**
		 * Disable customer repeat purchase.
		 */
		public static function disable_customer_repeat_purchase() {
			add_filter( 'woocommerce_is_purchasable', array( 'ANONY_Woo_Help', 'disable_repeat_purchase' ), 10, 2 );
			add_action( 'woocommerce_single_product_summary', array( 'ANONY_Woo_Help', 'purchase_disabled_message' ), 31 );
		}

		/**
		 * Disables repeat purchase for products / variations.
		 *
		 * @param bool        $purchasable true if product can be purchased.
		 * @param \WC_Product $product the WooCommerce product.
		 * @return bool $purchasable the updated is_purchasable check.
		 */
		public static function disable_repeat_purchase( $purchasable, $product ) {

			// Don’t run on parents of variations,.
			// function will already check variations separately.
			if ( $product->is_type( 'variable' ) ) {
				return $purchasable;
			}

			// Get the ID for the current product (passed in).
			$product_id = $product->is_type( 'variation' ) ? $product->variation_id : $product->id;

			// return false if the customer has bought the product / variation.
			if ( wc_customer_bought_product( wp_get_current_user()->user_email, get_current_user_id(), $product_id ) ) {
				$purchasable = false;
			}

			// Double-check for variations: if parent is not purchasable, then variation is not.
			if ( $purchasable && $product->is_type( 'variation' ) ) {
				$purchasable = $product->parent->is_purchasable();
			}

			return $purchasable;
		}


		/**
		 * Shows a “purchase disabled” message to the customer
		 */
		public static function purchase_disabled_message() {

			// Get the current product to see if it has been purchased.
			global $product;

			if ( $product->is_type( 'variable' ) ) {

				foreach ( $product->get_children() as $variation_id ) {
					// Render the purchase restricted message if it has been purchased.
					if ( wc_customer_bought_product( wp_get_current_user()->user_email, get_current_user_id(), $variation_id ) ) {
						self::render_variation_non_purchasable_message( $product, $variation_id );
					}
				}
			} elseif ( wc_customer_bought_product( wp_get_current_user()->user_email, get_current_user_id(), $product->id ) ) {
					echo '<div class=”woocommerce”><div class=”woocommerce-info wc-nonpurchasable-message”>You\’ve already purchased this product! It can only be purchased once.</div></div>';
			}
		}

		/**
		 * Generates a “purchase disabled” message to the customer for specific variations.
		 *
		 * @param \WC_Product $product the WooCommerce product.
		 * @param int         $no_repeats_id the id of the non-purchasable product.
		 */
		public static function render_variation_non_purchasable_message( $product, $no_repeats_id ) {

			// Double-check we're looking at a variable product.
			if ( $product->is_type( 'variable' ) && $product->has_child() ) {

				$variation_purchasable = true;

				foreach ( $product->get_available_variations() as $variation ) {

					// only show this message for non-purchasable variations matching our ID.
					if ( $no_repeats_id === $variation['variation_id'] ) {
						$variation_purchasable = false;
						echo '<div class=”woocommerce”><div class=”woocommerce-info wc-nonpurchasable-message js-variation-' . sanitize_html_class( $variation['variation_id'] ) . '">You\'ve already purchased this product! It can only be purchased once.</div></div>';
					}
				}
			}

			if ( ! $variation_purchasable ) {
				wc_enqueue_js(
					"
					jQuery('.variations_form')
					.on( 'woocommerce_variation_select_change', function( event ) {
					jQuery('.wc-nonpurchasable-message').hide();
					})
					.on( 'found_variation', function( event, variation ) {
					jQuery('.wc-nonpurchasable-message').hide();
					if ( ! variation.is_purchasable ) {
					jQuery( '.wc-nonpurchasable-message.js-variation-' + variation.variation_id ).show();
					}
					})
					.find( '.variations select' ).change();
					"
				);
			}
		}
		/**
		 * Remove customer details from email.
		 */
		public static function removing_customer_details_in_emails() {
			add_action( 'woocommerce_email_customer_details', array( 'ANONY_Woo_Help', 'removing_customer_details_in_emails_cb' ), 5, 4 );
			add_action( 'woocommerce_email_after_order_table', array( 'ANONY_Woo_Help', 'removing_customer_details_in_emails_cb' ), 10, 2 );
		}

		/**
		 * Remove customer details from email call back.
		 */
		public static function removing_customer_details_in_emails_cb() {
			$wmail = WC()->mailer();
			remove_action( 'woocommerce_email_customer_details', array( $wmail, 'email_addresses' ), 20 );
		}

		/**
		 * Get customer orders' ids.
		 *
		 * @param array $product_ids An array of products' IDs.
		 * @param int   $customer_id Customer's ID.
		 */
		public static function get_customer_orders_ids( $product_ids = 0, $customer_id = 0 ) {
			global $wpdb;

			$customer_id = ( 0 === $customer_id || '' === $customer_id ) ? get_current_user_id() : $customer_id;

			$statuses = array_map( 'esc_sql', wc_get_is_paid_statuses() );
			if ( is_array( $product_ids ) ) {
				$product_ids = implode( ',', $product_ids );
			}

			if ( ( 0 || '' ) !== $product_ids ) {
				$meta_query_line = "AND woim.meta_value IN ($product_ids)";
			} else {
				$meta_query_line = 'AND woim.meta_value !== 0';
			}

			array_walk(
				$statuses,
				function ( &$x ) {
					$x = "'mark_$x'";
				}
			);

			$cache_key = "get_customer_orders_ids_{$customer_id}";

			$results = wp_cache_get( $cache_key );

			if ( false === $results ) {
				// Get Orders IDs.
				// phpcs:disable
				$results = $wpdb->get_col(
					$wpdb->prepare(
						"SELECT DISTINCT p.ID FROM {$wpdb->prefix}posts AS p
							INNER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id
							INNER JOIN {$wpdb->prefix}woocommerce_order_items AS woi ON p.ID = woi.order_id
							INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS woim ON woi.order_item_id = woim.order_item_id

							WHERE p.post_status IN ( " . implode( ',', $statuses ) . " )
							AND pm.meta_key = '_customer_user'
							AND pm.meta_value = %d
							AND woim.meta_key IN ( '_product_id', '_variation_id' )
							$meta_query_line
							", 
						$customer_id
					)
				);
				// phpcs:enable.
				wp_cache_set( $cache_key, $results );
			}

			// Return an array of Order IDs or an empty array.
			return count( $results ) > 0 ? $results : array();
		}

		/**
		 * Allow remove items within checkout.
		 */
		public static function checkout_remove_items() {
			add_filter( 'woocommerce_cart_item_name', array( 'ANONY_Woo_Help', 'checkout_remove_items_cb' ), 10, 3 );
		}
		/**
		 * Allows to remove products in checkout page.
		 *
		 * @param string $product_name Product's name.
		 * @param array  $cart_item Cart item's data.
		 * @param string $cart_item_key Cart item's key.
		 * @return string
		 */
		public static function checkout_remove_items_cb( $product_name, $cart_item, $cart_item_key ) {
			if ( is_checkout() ) {
				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				$remove_link = apply_filters(
					'woocommerce_cart_item_remove_link',
					sprintf(
						'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">×</a>',
						esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
						__( 'Remove this item', 'woocommerce' ),
						esc_attr( $product_id ),
						esc_attr( $_product->get_sku() )
					),
					$cart_item_key
				);

				return '<span>' . $remove_link . '</span> <span>' . $product_name . '</span>';
			}

			return $product_name;
		}

		/**
		 * Add product duplicate.
		 *
		 * @param  int $product_id ID of product to be duplicate.
		 * @return WC_Product The duplicate.
		 */
		public static function duplicate_product( $product_id ) {

			$wc_adp = new WC_Admin_Duplicate_Product();
			return $wc_adp->product_duplicate( wc_get_product( $product_id ) );
		}

		/**
		 * Get IDs of products about to expire.
		 *
		 * @param  string $expiry_meta_key The meta key that stores expiry date.
		 * @param  string $before_expiry The period remaining to consider a product is about to expire after.
		 * @return array An array of IDs of products about to expire.
		 */
		public static function products_about_to_expire( $expiry_meta_key, $before_expiry = '180 days' ) {
			$args = array(
				'post_type'      => 'product',
				'posts_per_page' => 30,
				'post_status'    => 'publish',
				'order'          => 'DESC',
				// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'meta_query'     => array(
					array(
						'key'     => $expiry_meta_key,
						'value'   => array( gmdate( 'Y-m-d' ), gmdate( 'Y-m-d', strtotime( '180 days' ) ) ),
						'compare' => 'BETWEEN',
						'type'    => 'DATE',
					),
				),
				// phpcs:enable.
			);
			$query = new WP_Query( $args );
			$data  = array();
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();

					$data[] = get_the_ID();
				}

				wp_reset_postdata();
			}

			return $data;
		}

		/**
		 * Get states of base country.
		 *
		 * @return array An array of country's states.
		 */
		public static function get_base_country_states() {
			global $woocommerce;

			$countries_obj = new WC_Countries();

			$countries = $countries_obj->__get( 'countries' );

			$default_country = $countries_obj->get_base_country();

			return $countries_obj->get_states( $default_country );
		}

		/**
		 * Get best selling products.
		 *
		 * @return Object WP_Query object.
		 */
		public static function best_sellers( $posts_per_page = 4 ) {
			$args = array(
				'post_type'      => 'product',
				'meta_key'       => 'total_sales',
				'orderby'        => 'meta_value_num',
				'posts_per_page' => $posts_per_page,
			);

			$query = new WP_Query( $args );
			wp_reset_query();
			return $query;
		}
		public static function anony_custom_sale_badge( $html, $post, $product ) {

			$anony_options = ANONY_Options_Model::get_instance();

			$custom_sale_badge = get_post_meta( $post->ID, 'custom-sale-badge', true );

			if ( $custom_sale_badge && ! empty( $custom_sale_badge ) ) {
				return sprintf( '<span class="onsale on-sale-text">%s</span>', $custom_sale_badge );
			}
			if ( $product->is_type( 'variable' ) ) {
				$percentages    = array();
				$regular_prices = array();

				// Get all variation prices
				$prices = $product->get_variation_prices();

				// Loop through variation prices
				foreach ( $prices['price'] as $key => $price ) {
					// Only on sale variations
					if ( $prices['regular_price'][ $key ] !== $price ) {
						// Calculate and set in the array the percentage for each variation on sale
						$percentages[] = round( 100 - ( floatval( $prices['sale_price'][ $key ] ) / floatval( $prices['regular_price'][ $key ] ) * 100 ) );

						$sale_prices[] = floatval( $prices['sale_price'][ $key ] );

						$regular_prices[] = floatval( $prices['regular_price'][ $key ] );
					}
				}
				// We keep the highest value
				$percentage    = max( $percentages ) . '%';
				$regular_price = max( $regular_prices );
				$sale_price    = max( $sale_prices );

				$saved = $regular_price - $sale_price;

			} elseif ( $product->is_type( 'grouped' ) ) {
				$percentages    = array();
				$regular_prices = array();

				// Get all variation prices
				$children_ids = $product->get_children();

				// Loop through variation prices
				foreach ( $children_ids as $child_id ) {
					$child_product = wc_get_product( $child_id );

					$regular_price = (float) $child_product->get_regular_price();
					$sale_price    = (float) $child_product->get_sale_price();

					if ( $sale_price != 0 || ! empty( $sale_price ) ) {
						// Calculate and set in the array the percentage for each child on sale
						$percentages[] = round( 100 - ( $sale_price / $regular_price * 100 ) );

						$regular_prices[] = $regular_price;
						$sale_prices[]    = $sale_price;
					}
				}
				// We keep the highest value
				$percentage = max( $percentages ) . '%';

				$regular_price = max( $regular_prices );

				$sale_price = max( $sale_prices );

				$saved = $regular_price - $sale_price;

			} else {
				$regular_price = (float) $product->get_regular_price();
				$sale_price    = (float) $product->get_sale_price();

				if ( $sale_price != 0 || ! empty( $sale_price ) ) {
					$percentage = round( 100 - ( $sale_price / $regular_price * 100 ) ) . '%';

					$saved = $regular_price - $sale_price;
				} else {
					return $html;
				}
			}

			$sale_badge_type = 'percentage';

			$sale_badge_text = $percentage;

			$class = 'on-sale-percent';

			if ( 'text' === $anony_options->sale_badge_type ) {
				$sale_badge_text = sprintf( esc_html__( 'Save %1$s %2$s', 'smartpage' ), round( $saved ), get_woocommerce_currency_symbol() );

				$class = 'on-sale-text';
			}

			return sprintf( '<span class="onsale %1$s">%2$s</span>', $class, $sale_badge_text );
		}

		public static function display_sales_report( $atts ) {
			$user_id = get_current_user_id();

			// Retrieve all products for the user
			$products = wc_get_products(
				array(
					'limit'  => -1,
					'status' => 'publish',
					'author' => $user_id,
				)
			);

			$sales_data = array();

			// Loop through each product and get the sales data
			foreach ( $products as $product ) {
				$product_id        = $product->get_id();
				$product_name      = $product->get_name();
				$product_permalink = $product->get_permalink();

				$quantity_sold = 0;
				$total_sales   = 0;
				$pending_sales = 0; // Initialize pending sales

				$orders = wc_get_orders(
					array(
						'limit'    => -1,
						'status'   => 'completed',
						'customer' => $user_id,
					)
				);

				// Loop through each order and get the sales data for the product
				foreach ( $orders as $order ) {
					$items = $order->get_items( 'line_item' );

					foreach ( $items as $item ) {
						$product = $item->get_product();

						if ( $product->get_id() == $product_id ) {
							$quantity_sold += $item->get_quantity();
							$total_sales   += $item->get_total();

							// Check if the order is pending
							if ( $order->get_status() == 'pending' ) {
								$pending_sales += $item->get_total();
							}
						}
					}
				}

				// Subtract 10% from the total sales
				$total_sales   *= 0.9;
				$pending_sales *= 0.9; // Subtract 10% from pending sales

				// Get the current currency symbol
				$currency_symbol = get_woocommerce_currency_symbol();

				// Add the sales data to the sales_data array
				$sales_data[] = array(
					'product_name'      => $product_name,
					'product_permalink' => $product_permalink,
					'quantity_sold'     => $quantity_sold,
					'pending_sales'     => $pending_sales, // Add pending sales value before total sales
					'total_sales'       => $total_sales,
					'currency_symbol'   => $currency_symbol,
				);
			}

			// Sort the sales data array by total sales in descending order
			usort(
				$sales_data,
				function ( $a, $b ) {
					return $b['total_sales'] - $a['total_sales'];
				}
			);

			// Display the sales data as a table
			$output  = '<table>';
			$output .= '<tr><th>Product Name</th><th>Quantity Sold</th><th>Pending Sales</th><th>Total Sales</th></tr>'; // Change column order
			foreach ( $sales_data as $product ) {
				$output .= '<tr><td><a href="' . $product['product_permalink'] . '">' . $product['product_name'] . '</a></td><td>' . $product['quantity_sold'] . '</td><td>' . number_format( $product['pending_sales'], 2 ) . ' ' . $product['currency_symbol'] . '</td><td>' . number_format( $product['total_sales'], 2 ) . ' ' . $product['currency_symbol'] . '</td></tr>'; // Change column order
			}
			$output .= '</table>';
			return $output;
		}

		public static function current_user_sales_orders_table( $atts ) {
			$order = wc_get_order( $order_id );
			if ( ! $order ) {
				return false;
			}

				$items = array();
			foreach ( $order->get_items() as $item_id => $item ) {
				$product = $item->get_product();
				$items[] = array(
					'name'        => $item->get_name(),
					'quantity'    => $item->get_quantity(),
					'price'       => $item->get_total(),
					'product_id'  => $product ? $product->get_id() : '',
					'product_sku' => $product ? $product->get_sku() : '',
				);
			}

				$billing = array();
			if ( $order->get_billing_first_name() ) {
				$billing['first_name'] = $order->get_billing_first_name();
			}
			if ( $order->get_billing_last_name() ) {
				$billing['last_name'] = $order->get_billing_last_name();
			}
			if ( $order->get_billing_email() ) {
				$billing['email'] = $order->get_billing_email();
			}
			if ( $order->get_billing_phone() ) {
				$billing['phone'] = $order->get_billing_phone();
			}

				$shipping = array();
			if ( $order->get_shipping_first_name() ) {
				$shipping['first_name'] = $order->get_shipping_first_name();
			}
			if ( $order->get_shipping_last_name() ) {
				$shipping['last_name'] = $order->get_shipping_last_name();
			}
			if ( $order->get_shipping_address_1() ) {
				$shipping['address_1'] = $order->get_shipping_address_1();
			}
			if ( $order->get_shipping_address_2() ) {
				$shipping['address_2'] = $order->get_shipping_address_2();
			}
			if ( $order->get_shipping_city() ) {
				$shipping['city'] = $order->get_shipping_city();
			}
			if ( $order->get_shipping_state() ) {
				$shipping['state'] = $order->get_shipping_state();
			}
			if ( $order->get_shipping_postcode() ) {
				$shipping['postcode'] = $order->get_shipping_postcode();
			}
			if ( $order->get_shipping_country() ) {
				$shipping['country'] = $order->get_shipping_country();
			}

				$order_data = array(
					'order_id'     => $order->get_id(),
					'order_number' => $order->get_order_number(),
					'status'       => $order->get_status(),
					'date_created' => $order->get_date_created(),
					'billing'      => $billing,
					'shipping'     => $shipping,
					'items'        => $items,
					'total'        => $order->get_total(),
				);

				return $order_data;
		}

		public static function single_order( $order_id ) {

			?>
				<table>
					<thead>
					<tr>
						<th>Order Number</th>
						<th>Status</th>
						<th>Date Created</th>
						<th>Billing Information</th>
						<th>Shipping Information</th>
						<th>Line Items</th>
						<th>Total</th>
					</tr>
					</thead>
					<tbody>
				<?php
					$order_details = get_order_details( $order_id );

				if ( $order_details ) {
					echo '<tr>';
					echo '<td>' . $order_details['order_number'] . '</td>';
					echo '<td>' . $order_details['status'] . '</td>';
					echo '<td>' . $order_details['date_created']->format( 'Y-m-d H:i:s' ) . '</td>';
					echo '<td>' . $order_details['billing']['first_name'] . ' ' . $order_details['billing']['last_name'] . '<br>' . $order_details['billing']['email'] . '<br>' . $order_details['billing']['phone'] . '</td>';
					echo '<td>' . $order_details['shipping']['first_name'] . ' ' . $order_details['shipping']['last_name'] . '<br>' . $order_details['shipping']['address_1'] . '<br>' . $order_details['shipping']['city'] . ', ' . $order_details['shipping']['state'] . ' ' . $order_details['shipping']['postcode'] . '<br>' . $order_details['shipping']['country'] . '</td>';
					echo '<td>';
					foreach ( $order_details['items'] as $item ) {
						echo $item['name'] . ' x ' . $item['quantity'] . '<br>';
					}
					echo '</td>';
					echo '<td>' . wc_price( $order_details['total'] ) . '</td>';
					echo '</tr>';
				} else {
					echo '<tr><td colspan="7">Order not found</td></tr>';
				}
				?>
					</tbody>
				</table>
				<?php
		}
		/**
		 * Adds a form after orders table, so we can get total savings per month of year.
		 * Should be hooked with `woocommerce_account_orders_endpoint`
		 *
		 * @return void
		 */
		public static function orders_display_total_savings_form() {
			if ( is_wc_endpoint_url( 'orders' ) ) {
				$selected_month = isset( $_POST['month'] ) ? sanitize_text_field( $_POST['month'] ) : false;
				$selected_year  = isset( $_POST['year'] ) ? sanitize_text_field( $_POST['year'] ) : false;
				?>
					<form method="post" action="">
						<label for="month">Select a month:</label>
						<select name="month" id="month">
						<?php
						for ( $i = 1; $i <= 12; $i++ ) {
							$month = date( 'F', mktime( 0, 0, 0, $i, 1 ) );
							echo '<option value="' . $month . '" ' . selected( $selected_month, $month, false ) . '>' . $month . '</option>';
						}
						?>
						</select>
						<label for="year">Select a year:</label>
						<select name="year" id="year">
						<?php
						$current_year = date( 'Y' );
						for ( $i = $current_year; $i >= 2020; $i-- ) {
							echo '<option value="' . $i . '" ' . selected( $selected_year, $i, false ) . '>' . $i . '</option>';
						}
						?>
						</select>
						<input type="submit" name="submit" value="Show Total Savings">
					</form>
					<?php
			}
		}
		/**
		 * Get savings based on regular and sale prices.
		 *
		 * @param object $order
		 * @return int
		 */
		public static function pricing_based_order_total_savings( $order ) {
			$total_savings = 0;
			foreach ( $order->get_items() as $item_id => $item ) {
				$product = $item->get_product();
				if ( $product->is_on_sale() ) {
					$regular_price  = $product->get_regular_price( 'edit' );
					$sale_price     = $product->get_sale_price( 'edit' );
					$quantity       = $item->get_quantity();
					$item_savings   = ( $regular_price - $sale_price ) * $quantity;
					$total_savings += $item_savings;
				}
			}

			return $total_savings;
		}

		public static function display_total_savings_for_customer_orders() {
			if ( is_wc_endpoint_url( 'orders' ) ) {
				$customer_id    = get_current_user_id();
				$total_savings  = 0;
				$selected_month = isset( $_POST['month'] ) ? sanitize_text_field( $_POST['month'] ) : false;
				$selected_year  = isset( $_POST['year'] ) ? sanitize_text_field( $_POST['year'] ) : false;

				$args = array(
					'customer' => $customer_id,
					'status'   => array( 'completed', 'delivered' ),
				);

				$result_msg = 'Total Savings for all orders: ';
				if ( $selected_month && $selected_year ) {
					$first_day_of_month = date( 'Y-m-01', strtotime( $selected_month . ' ' . $selected_year ) );
					$last_day_of_month  = date( 'Y-m-t', strtotime( $first_day_of_month ) );

					// To get products between to dates, use the following format.
					$args['date_created'] = $first_day_of_month . '...' . $last_day_of_month;

					$result_msg = 'Total Savings for all orders of ' . $selected_month . ' ' . $selected_year . ' is: ';
				}

				$orders = wc_get_orders( $args );
				foreach ( $orders as $order ) {
					$total_savings += self::pricing_based_order_total_savings( $order );
				}
				if ( $total_savings > 0 ) {
					echo '<p><strong>' . $result_msg . '</strong> ' . wc_price( $total_savings ) . '</p>';
				}
			}
		}

		/**
		 * Validate cart so as to have single seller per order.
		 */
		public static function cart_exclusive_seller() {
			/**
			 * @param boolean $passed True if the item passed validation.
			 * @param integer $product_id        Product ID being validated.
			 * @param integer $quantity          Quantity added to the cart.
			 * @return void
			 */
			add_action(
				'woocommerce_add_to_cart_validation',
				function ( $passed, $product_id, $quantity ) {
					// Get the author ID of the product being added
					$product_author_id = get_post_field( 'post_author', $product_id );

					// Loop through the cart items to check for products from a different author
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						// Get the product ID and author ID of the cart item
						$cart_item_product_id = $cart_item['product_id'];
						$cart_item_author_id  = get_post_field( 'post_author', $cart_item_product_id );

						// Check if the cart item has a different author than the product being added
						if ( $cart_item_author_id !== $product_author_id ) {
							// Set an error message
							wc_add_notice( __( 'You cannot add products from different seller to the cart.', 'anonyengine' ), 'error' );

							// Prevent the product from being added to the cart
							return false;
						}
					}

					// If no products from a different author were found, allow the product to be added to the cart
					return $passed;
				},
				10,
				3
			);
		}

		public static function product_type_column() {
			// Add a new column to the Products admin page
			add_filter(
				'manage_edit-product_columns',
				function ( $columns ) {
					$columns['product_type'] = __( 'Product Type', 'anonyengine' );
					return $columns;
				}
			);

			// Populate the Product Type column with the product type
			add_action(
				'manage_product_posts_custom_column',
				function ( $column, $post_id ) {
					if ( $column == 'product_type' ) {
						$product = wc_get_product( $post_id );
						echo $product->get_type();
					}
				},
				10,
				2
			);
		}

		public static function cart_prevent_add_out_of_stock() {
			add_filter(
				'woocommerce_add_to_cart_validation',
				function ( $passed, $product_id, $quantity ) {
					$product = wc_get_product( $product_id );

					if ( $product->is_type( 'variable' ) ) {
						// For variable products, we need to check the stock quantity of the selected variation.
						$variation_id = $_POST['variation_id'];
						$variation    = wc_get_product( $variation_id );

						if ( ! $variation->is_in_stock() || $quantity > $variation->get_stock_quantity() ) {
							wc_add_notice( 'This variation is out of stock or there is not enough stock quantity.', 'error' );
							$passed = false;
						}
					} else {
						// For simple and other product types, we can check the global stock quantity.
						if ( ! $product->is_in_stock() || $quantity > $product->get_stock_quantity() ) {
							wc_add_notice( 'This product is out of stock or there is not enough stock quantity.', 'error' );
							$passed = false;
						}
					}

					return $passed;
				},
				10,
				3
			);
		}

		public static function create_order( $customer_id, $product_id, $total = 0 ) {
			$product = wc_get_product( $product_id );

			$user = get_user_by( 'id', $customer_id );

			if ( ! $product && ! $user ) {
				return;
			}

			// Create the order.
			$order_data = array(
				'customer_id' => $customer_id,
				'status'      => 'processing',
			);

			// Create the order.
			$order = wc_create_order( $order_data );
			if ( $order ) {

				// add products.
				$order->add_product(
					$product,
					1,
					array(
						'subtotal' => $total,
						'total'    => $total,
					)
				);

				// Get an instance of the WC_Customer Object from the user ID.
				$customer = new WC_Customer( $customer_id );

				// add billing and shipping addresses.
				$address = array(
					'first_name' => $customer->get_billing_first_name(),
					'last_name'  => $customer->get_billing_last_name(),
					'email'      => $customer->get_email(),
					'phone'      => $customer->get_billing_phone(),
					'address_1'  => $customer->get_billing_address_1(),
					'address_2'  => $customer->get_billing_address_2(),
					'city'       => $customer->get_billing_city(),
					'country'    => $customer->get_billing_country(),
				);

				$order->set_address( $address, 'billing' );

				$order->set_address( $address, 'shipping' );

				// calculate and save
				$order->calculate_totals();

				$order->save();
			}
		}
		/**
		 * Process Order Refund through Code
		 *
		 * @param int    $order_id The ID of the order to refund.
		 * @param string $refund_reason The reason for the refund (optional).
		 *
		 * @return WC_Order_Refund|WP_Error
		 */
		public static function refund_order( $order_id, $refund_reason = '' ) {
			$order = wc_get_order( $order_id );

			// Ensure the provided ID corresponds to a valid WC_Order.
			if ( ! is_a( $order, 'WC_Order' ) ) {
				return new WP_Error( 'wc-order', esc_html__( 'Provided ID is not a WC Order', 'anony-jet-appointments' ) );
			}

			// Check if the order has already been refunded.
			if ( 'refunded' === $order->get_status() ) {
				return new WP_Error( 'wc-order', esc_html__( 'Order has already been refunded', 'anony-jet-appointments' ) );
			}

			// Get the order items.
			$order_items = $order->get_items();

			// Initialize variables for refund amount and line items.
			$refund_amount = 0;
			$line_items    = array();

			// Process each order item.
			foreach ( $order_items as $item_id => $item ) {
				$refund_tax = wc_get_order_item_meta( $item_id, '_line_tax' );
				$line_total = wc_get_order_item_meta( $item_id, '_line_total' );

				// Calculate the refund amount for each item.
				$refund_amount += wc_format_decimal( $line_total );

				// Prepare line items for refund.
				$line_items[ $item_id ] = array(
					'qty'          => 1, // You can adjust the quantity as needed.
					'refund_total' => wc_format_decimal( $line_total ),
					'refund_tax'   => $refund_tax,
				);
			}

			// Create the refund.
			$refund = wc_create_refund(
				array(
					'amount'         => $refund_amount,
					'reason'         => $refund_reason,
					'order_id'       => $order_id,
					'line_items'     => $line_items,
					'refund_payment' => false, // Set to true if you want to refund the payment as well.
				)
			);

			return $refund;
		}
		/**
		 * Eender products loop
		 *
		 * @param array $args Loop's arguments.
		 * @return void
		 */
		public static function products_loop( $args = array() ) {
			$default = array(
				'is_shop' => false,
			);

			$settings = wp_parse_args( $args, $default );

			if ( ! function_exists( 'wc_get_products' ) || is_admin() ) {
				return;
			}

			$products_per_page = apply_filters( 'loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page() );

			$loop_args = array(
				'status'   => 'publish',
				'limit'    => $products_per_page,
				'return'   => 'ids',
				'paginate' => true,
				'orderby'  => 'date',
				'order'    => 'DESC',
			);

			if ( $settings['is_shop'] ) {
				$ordering            = WC()->query->get_catalog_ordering_args();
				$ordering['orderby'] = array_shift( explode( ' ', $ordering['orderby'] ) );
				$ordering['orderby'] = stristr( $ordering['orderby'], 'price' ) ? 'meta_value_num' : $ordering['orderby'];
				/**
				 * If your custom loop is on a static front page then check for the query var 'page' instead of 'paged'.
				 * See https://developer.wordpress.org/reference/classes/wp_query/#pagination-parameters.
				 */
				$paged                = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
				$loop_args['page']    = $paged;
				$loop_args['orderby'] = $ordering['orderby'];
				$loop_args['order']   = $ordering['order'];
			} else {
				$loop_args['page'] = 1;
			}

			if ( ! empty( $settings['loop_args'] ) ) {
				$loop_args = wp_parse_args( $loop_args, $settings['loop_args'] );
			}
			$products_ids = wc_get_products( $loop_args );

			if ( $settings['is_shop'] ) {
				wc_set_loop_prop( 'current_page', $paged );
				wc_set_loop_prop( 'is_paginated', wc_string_to_bool( true ) );
				wc_set_loop_prop( 'total', $products_ids->total );
				wc_set_loop_prop( 'total_pages', $products_ids->max_num_pages );
			}
			wc_set_loop_prop( 'page_template', get_page_template_slug() );
			wc_set_loop_prop( 'per_page', $products_per_page );

			if ( $products_ids ) {
				if ( $settings['is_shop'] ) {
					do_action( 'woocommerce_before_shop_loop' );
				}
				do_action( 'anony_woocommerce_before_products_loop', $settings );
				woocommerce_product_loop_start();
				foreach ( $products_ids->products as $featured_product ) {
					$post_object = get_post( $featured_product );
					setup_postdata( $GLOBALS['post'] =& $post_object );
					wc_get_template_part( 'content', 'product' );
				}
				wp_reset_postdata();
				woocommerce_product_loop_end();
				do_action( 'anony_woocommerce_after_products_loop', $settings );
				if ( $settings['is_shop'] ) {
					do_action( 'woocommerce_after_shop_loop' );
				}
			} else {
				do_action( 'woocommerce_no_products_found' );
			}
		}
	}
}