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

		public static function get_cart_items($user_id, $product_id = false) {
			global $woocommerce;
			
			$cart = $woocommerce->cart->get_cart();
			$items = array();
			
			foreach ($cart as $item) {
		
				$product = $item['data'];
				if( $product_id && $product_id !== $product->get_id()  ){
					continue;
				}
				$quantity = $item['quantity'];
				$price = $product->get_price();
				$items[] = array(
					'user_id' => $user_id,
					'product_id' => $product->get_id(),
					'quantity' => $quantity,
					'price' => $price
				);
			}
			error_log(print_r($items,true));
			return $items;
		}
	}
}







