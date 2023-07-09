<?php
/**
 * Woocommerce cart helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine.
 * @author   Makiomar <info@makiomar.com>.
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'ANONY_Woo_Cart_Help' ) ) {
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
	class ANONY_Woo_Cart_Help extends ANONY_Woo_Help {

		public static function get_cart_items($user_id, $_product_id = false) {
			global $woocommerce;
			
			$cart = $woocommerce->cart->get_cart();
			$items = array();
			$parent_product_id = 0;
			$variation_id = 0;
			foreach ($cart as $item) {
				$product = $item['data'];
				$product_id = $product->get_id();
				$quantity = $item['quantity'];
				$price = $product->get_price();
				
				// Check if product is a variation
				if ($product->is_type('variation')) {
					if( $_product_id && $_product_id !== $product->get_parent_id()  ){
						continue;
					}
					$parent_product_id = $product->get_parent_id();
					$variation_id = $product_id;
					$product_id = $parent_product_id;
				} else {
					if( $_product_id && $_product_id !== $product_id  ){
						continue;
					}
					$parent_product_id = 0;
					$variation_id = 0;
				}
				
				$items[] = array(
					'user_id' => $user_id,
					'product_id' => $product_id,
					'parent_product_id' => $parent_product_id,
					'variation_id' => $variation_id,
					'quantity' => $quantity,
					'price' => $price
				);
			}
			error_log(print_r($items, true));
			return $items;
		}
	}
}







