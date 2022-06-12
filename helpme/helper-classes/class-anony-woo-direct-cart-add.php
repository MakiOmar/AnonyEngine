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
         * Add product directly to cart, then redirect to checkout
         * 
         */ 
        public function direct_add_to_cart( int $product_id ){            
            WC()->cart->empty_cart();

            // This adds the product with the ID; we can also add a second variable which will be the variation ID.
            WC()->cart->add_to_cart( $product_id );

            // Redirects to the checkout page.
            wp_safe_redirect( wc_get_checkout_url() );
            
            // Safely closes the function.
            exit();


        }
    }

}