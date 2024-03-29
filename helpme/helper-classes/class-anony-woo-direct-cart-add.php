<?php
/**
 * Woocommerce direct cart addition.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine.
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_Woo_Direct_Cart_Add' ) ) {

	/**
	 * Woocommerce direct cart addition class.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine
	 * @author   Makiomar <info@makiomar.com>
	 * @license  https:// makiomar.com AnonyEngine Licence.
	 * @link     https:// makiomar.com/anonyengine.
	 */
	class ANONY_Woo_Direct_Cart_Add {

		/**
		 * Class instructor.
		 *
		 * @param integer $product_id Product ID.
		 * @param array   $session Array of data that should be stored into cart session.
		 */
		public function __construct( $product_id, array $session = array() ) {

			if ( ! class_exists( 'woocommerce' ) ) {
				return;
			}

			// phpcs:disable Squiz.PHP.CommentedOutCode.Found

			/*
				$session = array(
					'field_name' => array(
						'field_value'           => 'value' ,
						'as_cart_item_data'     => 'yes' ,
						'as_order_item_meta'    => 'yes' ,
						'order_visible'         => 'yes' ,
						'custom_price'          => 'no' ,
						'checkout_target_field' => 'no' , // e.g. billing_last_name
					)
				);
			*/
			// phpcs:enable.
			$this->product_id = $product_id;
			$this->session    = $session;

			add_action( 'template_redirect', array( $this, 'direct_add_to_cart' ) );

			add_filter( 'woocommerce_checkout_get_value', array( $this, 'pre_populate_checkout' ), 10, 2 );

			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_item_data' ), 1, 2 );

			add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_items_from_session' ), 1, 3 );

			add_action( 'woocommerce_before_calculate_totals', array( $this, 'update_custom_price' ), 1, 1 );

			add_action( 'woocommerce_add_order_item_meta', array( $this, 'add_values_to_order_item_meta' ), 1, 2 );

			add_filter( 'woocommerce_order_item_display_meta_key', array( $this, 'order_item_display_meta_key' ), 20, 2 );
		}

		/**
		 * Add product directly to cart, then redirect to checkout
		 *
		 * @param integer $product_id Product ID.
		 */
		protected function add_to_cart( $product_id ) {

			WC()->cart->empty_cart();

			// This adds the product with the ID; we can also add a second variable which will be the variation ID.
			WC()->cart->add_to_cart( $product_id );
		}

		/**
		 * Now add to cart.
		 */
		public function direct_add_to_cart() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( empty( $_GET['direct_add_to_cart'] ) ) {
				return;
			}
			// phpcs:enable.

			$this->set_session_data();

			$this->add_to_cart( $this->product_id );

			// Redirects to the checkout page.
			wp_safe_redirect( wc_get_checkout_url() );

			// Safely closes the function.
			exit();
		}

		/**
		 * Set session data.
		 */
		protected function set_session_data() {

			global $woocommerce;

			if ( ! empty( $this->session ) ) {

				foreach ( $this->session as $key => $args ) {
					$woocommerce->session->set( $key, $args['field_value'] );
				}
			}
		}

		/**
		 * Pre populate checkout.
		 *
		 * @param string $input Input.
		 * @param string $key Key.
		 *
		 * @return string Field's value.
		 */
		public function pre_populate_checkout( $input, $key ) {
			global $woocommerce;
			global $current_user;

			if ( ! empty( $this->session ) ) {

				foreach ( $this->session as $session_key => $args ) {

					if ( empty( $args['checkout_target_field'] ) || 'no' === $args['checkout_target_field'] ) {
						continue;
					}

					if ( $key === $args['checkout_target_field'] ) {

						return esc_attr( $woocommerce->session->get( $session_key ) );

					}
				}
			}
		}


		/**
		 * This captures additional posted information (all sent in one array).
		 *
		 * @param array   $cart_item_data Cart's item's data.
		 * @param integer $product_id Product's ID.
		 *
		 * @return array An array of cart's item's data.
		 */
		public function add_item_data( $cart_item_data, $product_id ) {

			global $woocommerce;
			$new_value = array();

			$custom_options = array();

			if ( ! empty( $this->session ) ) {

				foreach ( $this->session as $session_key => $args ) {

					if ( empty( $args['as_cart_item_data'] ) || 'yes' !== $args['as_cart_item_data'] ) {

						continue;

					}

					$custom_options[ $session_key ] = $args;

				}
			}

			$new_value['_custom_options'] = $custom_options;

			if ( empty( $cart_item_data ) ) {

				$v = $new_value;

			} else {

				$v = array_merge( $cart_item_data, $new_value );
			}

			return $v;
		}


		/**
		 * This captures the information from the previous function and attaches it to the item.
		 *
		 * @param array  $item Cart's item'.
		 * @param array  $values Item's values.
		 * @param string $key Key.
		 *
		 * @return array An array of cart's item's data.
		 */
		public function get_cart_items_from_session( $item, $values, $key ) {

			if ( array_key_exists( '_custom_options', $values ) ) {
				$item['_custom_options'] = $values['_custom_options'];
			}

			return $item;
		}

		/**
		 * Override the price you can use information saved against the product to do so
		 *
		 * @param object $cart_object Cart's object.
		 */
		public function update_custom_price( $cart_object ) {
			foreach ( $cart_object->cart_contents as $cart_item_key => $value ) {

				// Version 2.x.
				// $value['data']->price = $value['_custom_options']['custom_price'];.
				// Version 3.x / 4.x.

				$custom_price = 0;
				if ( ! empty( $value['_custom_options'] ) && is_array( $value['_custom_options'] ) ) {
					foreach ( $value['_custom_options'] as $session_key => $session_value ) {
						if ( ! empty( $session_value['custom_price'] ) && 'no' !== $session_value['custom_price'] && is_numeric( $session_value['custom_price'] ) ) {

								$custom_price += $session_value['custom_price'];

						}
					}

					$value['data']->set_price( $custom_price );
				}
			}
		}

		/**
		 * This adds the information as meta data so that it can be seen as part of the order (to hide any meta data from the customer just start it with an underscore)
		 *
		 * @param string $item_id Item's ID.
		 * @param array  $values Item's values.
		 */
		public function add_values_to_order_item_meta( $item_id, $values ) {
			global $woocommerce, $wpdb;

			if ( ! empty( $this->session ) && ! empty( $values['_custom_options'] ) ) {

				foreach ( $this->session as $session_key => $args ) {

					$val = $values['_custom_options'][ $session_key ]['field_value'];

					if ( empty( $args['as_order_item_meta'] ) || 'yes' !== $args['as_order_item_meta'] ) {
						continue;
					}

					if ( empty( $args['order_visible'] ) || 'yes' !== $args['order_visible'] ) {

						$item_meta_key = '_' . $session_key;

					} else {
						$item_meta_key = $session_key;
					}

					wc_add_order_item_meta( $item_id, $item_meta_key, $val );

				}
			}
		}

		/**
		 * Change displayed label for specific order item meta key.
		 *
		 * @param string $display_key Meta's display key.
		 * @param object $meta Meta's object.
		 *
		 * @return string Meta's display key.
		 */
		public function order_item_display_meta_key( $display_key, $meta ) {

			if ( ! empty( $this->session ) ) {

				foreach ( $this->session as $session_key => $args ) {

					if ( $meta->key === $session_key ) {

						if ( ! empty( $args['order_meta_label'] ) ) {
							return $args['order_meta_label'];
						}

						return $display_key;

					}
				}
			}

			return $display_key;
		}
	}

}
