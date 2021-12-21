<?php
/**
 * Woocommerce helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

if ( ! class_exists( 'ANONY_WOO_HELP' ) ) {
	class ANONY_WOO_HELP extends ANONY_HELP{
		
		public static function createProductAttribute( $label_name ){
			
			if(!class_exists('woocommerce')) return;
			
		    global $wpdb;

		    $slug = sanitize_title( $label_name );
		    
		    $attr_exists = false;
		    
		    if ( strlen( $slug ) >= 28 ) {
		        return new WP_Error( 'invalid_product_attribute_slug_too_long', sprintf( __( 'Name "%s" is too long (28 characters max). Shorten it, please.', 'woocommerce' ), $slug ), array( 'status' => 400 ) );

		    } elseif ( wc_check_if_attribute_name_is_reserved( $slug ) ) {
		        return new WP_Error( 'invalid_product_attribute_slug_reserved_name', sprintf( __( 'Name "%s" is not allowed because it is a reserved term. Change it, please.', 'woocommerce' ), $slug ), array( 'status' => 400 ) );

		    } elseif ( taxonomy_exists( wc_attribute_taxonomy_name( $slug ) ) ) {
		        return new WP_Error( 'invalid_product_attribute_slug_already_exists', sprintf( __( 'Name "%s" is already in use. Change it, please.', 'woocommerce' ), $label_name ), array( 'status' => 400 ) );

		    }elseif ( function_exists( 'wc_get_attribute_taxonomies' ) && wc_get_attribute_taxonomies() ){
		    	
				foreach ( wc_get_attribute_taxonomies() as $key => $value ) {
					if ( $value->attribute_name == $slug ) return new WP_Error( 'invalid_product_attribute_slug_already_exists', sprintf( __( 'Name "%s" is already in use. Change it, please.', 'woocommerce' ), $label_name ), array( 'status' => 400 ) );
				}
		
			}
		   
		    $data = array(
		        'attribute_label'   => $label_name,
		        'attribute_name'    => $slug,
		        'attribute_type'    => 'select',
		        'attribute_orderby' => 'menu_order',
		        'attribute_public'  => 0, // Enable archives ==> true (or 1)
		    );

		    $results = $wpdb->insert( "{$wpdb->prefix}woocommerce_attribute_taxonomies", $data );

		    if ( is_wp_error( $results ) ) {
		        return new WP_Error( 'cannot_create_attribute', $results->get_error_message(), array( 'status' => 400 ) );
		    }

		    $id = $wpdb->insert_id;

		    do_action('woocommerce_attribute_added', $id, $data);

		    wp_schedule_single_event( time(), 'woocommerce_flush_rewrite_rules' );

		    delete_transient('wc_attribute_taxonomies');
		}
		
		public static function addProductReview($comment_data){
			global $post, $product;
			
			if($post->post_type !== "product") return;
			
			if(!get_comment_meta('rating')){
				
				$comment_id = wp_insert_comment($comment_data);

				// HERE inserting the rating (an integer from 1 to 5)
				update_comment_meta( $comment_id, 'rating', 3 );
			}
			
			
		}
		
		public static function CouponFormInOrderDetails(){
			// Just hide default woocommerce coupon field
			add_action( 'woocommerce_before_checkout_form', [self, 'hideCheckoutCouponForm'], 5 );
			
			// Add a custom coupon field before checkout payment section
			add_action( 'woocommerce_review_order_before_payment', [self, 'checkoutCouponFormCustom'] );
			
			// jQuery code
			add_action( 'wp_footer', [self, 'customCheckoutJqueryScript'] );
		}
		
		static function hideCheckoutCouponForm() {
		    echo '<style>.woocommerce-form-coupon-toggle {display:none;}</style>';
		}
		
		
		static function checkoutCouponFormCustom() {
		    /*echo '<div class="checkout-coupon-toggle"><div class="woocommerce-info">' . sprintf(
		        __("Have a coupon? %s"), '<a href="#" class="show-coupon">' . __("Click here to enter your code") . '</a>'
		    ) . '</div></div>';
			*/
		    echo '<div class="coupon-form-wrapper"><div class="coupon-form" style="margin-bottom:20px;">
		        <p>' . __("إذا كان لديك رمز قسيمة ، فيرجى تطبيقه أدناه.") . '</p>
		        <p class="form-row form-row-first woocommerce-validated">
		            <input type="text" name="coupon_code" class="input-text" placeholder="' . __("كود القسيمة") . '" id="coupon_code" value="">
		        </p>
		        <p class="form-row form-row-last">
		            <button type="button" class="button" name="apply_coupon" value="' . __("Apply coupon") . '">' . __("تطبيق الكود") . '</button>
		        </p>
		        <div class="clear"></div>
		    </div></div>';
		}
		
		static function customCheckoutJqueryScript() {
		    if ( is_checkout() && ! is_wc_endpoint_url() ) :?>
			    <script type="text/javascript">
			    jQuery( function($){
			        //$('.coupon-form').css("display", "none"); // Be sure coupon field is hidden
			        
			        // Show or Hide coupon field
			        $('.checkout-coupon-toggle .show-coupon').on( 'click', function(e){
			            $('.coupon-form').toggle(200);
			            e.preventDefault();
			        })
			        
			        // Copy the inputed coupon code to WooCommerce hidden default coupon field
			        $('.coupon-form input[name="coupon_code"]').on( 'input change', function(){
			            $('form.checkout_coupon input[name="coupon_code"]').val($(this).val());
			            // console.log($(this).val()); // Uncomment for testing
			        });
			        
			        // On button click, submit WooCommerce hidden default coupon form
			        $('.coupon-form button[name="apply_coupon"]').on( 'click', function(){
			            $('form.checkout_coupon').submit();
			            // console.log('click: submit form'); // Uncomment for testing
			        });
			    });
			    </script>
		    <?php endif;
		}
		
		/**
		 * Show lowest variation price
		 */
		static function showOnlyLowestVariationPrice(){
			add_filter( 'woocommerce_variable_sale_price_html', [self, 'lowestVariationPriceFormat'], 10, 2 );
			add_filter( 'woocommerce_variable_price_html', [self, 'lowestVariationPriceFormat'], 10, 2 );
		}
	
		static function lowestVariationPriceFormat($price, $product){
			// Main Price
			$prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
			$price = $prices[0] !== $prices[1] ? sprintf( __( '%1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
			 
			// Sale Price
			$prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
			sort( $prices );
			$saleprice = $prices[0] !== $prices[1] ? sprintf( __( '%1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
			 
			if ( $price !== $saleprice ) {
			$price = '<del>' . $saleprice . $product->get_price_suffix() . '</del> <ins>' . $price . $product->get_price_suffix() . '</ins>';
			}
			return $price;
		}

		
		/**
		 * Show highest variation price
		 */
		static function showOnlyHighestVariationPrice($price, $product){
			add_filter( 'woocommerce_variable_sale_price_html', [self, 'highesttVariationPriceFormat'], 10, 2 );
			add_filter( 'woocommerce_variable_price_html', [self, 'highesttVariationPriceFormat'], 10, 2 );
		}
			
		static function highesttVariationPriceFormat(){
			
			// Main Price
			$prices = array( $product->get_variation_price( 'max', true ), $product->get_variation_price( 'min', true ) );
			$price = $prices[0] !== $prices[1] ? sprintf( __( 'Up To: %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
			 
			// Sale Price
			$prices = array( $product->get_variation_regular_price( 'max', true ), $product->get_variation_regular_price( 'min', true ) );
			sort( $prices );
			$saleprice = $prices[0] !== $prices[1] ? sprintf( __( 'Up To: %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
			 
			if ( $price !== $saleprice ) {
			$price = '<del>' . $saleprice . $product->get_price_suffix() . '</del> <ins>' . $price . $product->get_price_suffix() . '</ins>';
			}
			return $price;
		}
		
		static function disableCustomerRepeatPurchase(){
			add_filter( 'woocommerce_is_purchasable', [self, 'disableRepeatPurchase'], 10, 2 );
			add_action( 'woocommerce_single_product_summary', [self, 'purchaseDisabledMessage'], 31 );
		}
		
		/**
		* Disables repeat purchase for products / variations
		*
		* @param bool $purchasable true if product can be purchased
		* @param \WC_Product $product the WooCommerce product
		* @return bool $purchasable the updated is_purchasable check
		*/
		static function disableRepeatPurchase( $purchasable, $product ) {
			
			// Don’t run on parents of variations,
			// function will already check variations separately
			if ( $product->is_type( 'variable' ) ) return $purchasable;
			
			
			// Get the ID for the current product (passed in)
			$product_id = $product->is_type( 'variation' ) ? $product->variation_id : $product->id;
			
			// return false if the customer has bought the product / variation
			if ( wc_customer_bought_product( wp_get_current_user()->user_email, get_current_user_id(), $product_id ) ) $purchasable = false;
			
			
			// Double-check for variations: if parent is not purchasable, then variation is not
			if ( $purchasable && $product->is_type( 'variation' ) ) $purchasable = $product->parent->is_purchasable();
			
			
			return $purchasable;
		}
		
		
		/**
		* Shows a “purchase disabled” message to the customer
		*/
		static function purchaseDisabledMessage() {
		
			// Get the current product to see if it has been purchased
			global $product;
			
			if ( $product->is_type( 'variable' ) ) {
			
				foreach ( $product->get_children() as $variation_id ) {
					// Render the purchase restricted message if it has been purchased
					if ( wc_customer_bought_product( wp_get_current_user()->user_email, get_current_user_id(), $variation_id ) ) {
					self::renderVariationNonPurchasableMessage( $product, $variation_id );
					}
				}
			
			} else {
				if ( wc_customer_bought_product( wp_get_current_user()->user_email, get_current_user_id(), $product->id ) ) {
				echo '<div class=”woocommerce”><div class=”woocommerce-info wc-nonpurchasable-message”>You\’ve already purchased this product! It can only be purchased once.</div></div>';
				}
			}
		}
		
		/**
		* Generates a “purchase disabled” message to the customer for specific variations
		*
		* @param \WC_Product $product the WooCommerce product
		* @param int $no_repeats_id the id of the non-purchasable product
		*/
		function renderVariationNonPurchasableMessage( $product, $no_repeats_id ) {
		
			// Double-check we're looking at a variable product
			if ( $product->is_type( 'variable' ) && $product->has_child() ) {
			
				$variation_purchasable = true;
				
				foreach ( $product->get_available_variations() as $variation ) {
				
					// only show this message for non-purchasable variations matching our ID
					if ( $no_repeats_id === $variation['variation_id'] ) {
						$variation_purchasable = false;
						echo '<div class=”woocommerce”><div class=”woocommerce-info wc-nonpurchasable-message js-variation-' . sanitize_html_class( $variation['variation_id'] ) . '">You\'ve already purchased this product! It can only be purchased once.</div></div>';
					}
				}
			}
			
			if ( ! $variation_purchasable ) {
				wc_enqueue_js("
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
				");
			}
		}
		/**
		 * Remove customer details from email
		 */
		static function removingCustomerDetailsInEmails(){
			add_action( 'woocommerce_email_customer_details', [self, 'removingCustomerDetailsInEmailsCb'], 5, 4 );
			add_action( 'woocommerce_email_after_order_table', [self, 'removingCustomerDetailsInEmailsCb'], 10, 2 );
		}
		
		static function removingCustomerDetailsInEmailsCb( $order, $sent_to_admin, $plain_text, $email ){
		    $wmail = WC()->mailer();
		    remove_action( 'woocommerce_email_customer_details', array( $wmail, 'email_addresses' ), 20, 3 );
		}
		
		static function getCustomerOrderIds( $product_ids = 0, $customer_id = 0 ) {
		    global $wpdb;

		    $customer_id = $customer_id == 0 || $customer_id == '' ? get_current_user_id() : $customer_id;
		    
		    $statuses    = array_map( 'esc_sql', wc_get_is_paid_statuses() );

		    if ( is_array( $product_ids ) ) $product_ids = implode(',', $product_ids);

		    if ( $product_ids !=  ( 0 || '' ) )
		        $meta_query_line = "AND woim.meta_value IN ($product_ids)";
		    else
		        $meta_query_line = "AND woim.meta_value != 0";

		    // Get Orders IDs
		    $results = $wpdb->get_col( "
		        SELECT DISTINCT p.ID FROM {$wpdb->prefix}posts AS p
		        INNER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id
		        INNER JOIN {$wpdb->prefix}woocommerce_order_items AS woi ON p.ID = woi.order_id
		        INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS woim ON woi.order_item_id = woim.order_item_id
		        WHERE p.post_status IN ( 'wc-" . implode( "','wc-", $statuses ) . "' )
		        AND pm.meta_key = '_customer_user'
		        AND pm.meta_value = $customer_id
		        AND woim.meta_key IN ( '_product_id', '_variation_id' )
		        $meta_query_line
		    " );

		    // Return an array of Order IDs or an empty array
		    return sizeof($results) > 0 ? $results : array();
		}
		
		static function checkoutRemoveItems(){
			add_filter( 'woocommerce_cart_item_name', [self, 'checkoutRemoveItemsCb'], 10, 3 );
		}
		/**
		 * Allows to remove products in checkout page.
		 * 
		 * @param string $product_name 
		 * @param array $cart_item 
		 * @param string $cart_item_key 
		 * @return string
		 */
		static function checkoutRemoveItemsCb( $product_name, $cart_item, $cart_item_key ) {
			if ( is_checkout() ) {
				$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				$remove_link = apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
					'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">×</a>',
					esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
					__( 'Remove this item', 'woocommerce' ),
					esc_attr( $product_id ),
					esc_attr( $_product->get_sku() )
		        ), $cart_item_key );

				return '<span>' . $remove_link . '</span> <span>' . $product_name . '</span>';
			}

			return $product_name;
		}
}
}

