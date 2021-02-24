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
			add_action( 'woocommerce_before_checkout_form', [self, 'hide_checkout_coupon_form'], 5 );
			
			// Add a custom coupon field before checkout payment section
			add_action( 'woocommerce_review_order_before_payment', [self, 'checkout_coupon_form_custom'] );
			
			// jQuery code
			add_action( 'wp_footer', [self, 'custom_checkout_jquery_script'] );
		}
		
		static function hide_checkout_coupon_form() {
		    echo '<style>.woocommerce-form-coupon-toggle {display:none;}</style>';
		}
		
		
		static function checkout_coupon_form_custom() {
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
		
		static function custom_checkout_jquery_script() {
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
	}
}