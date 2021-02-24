<?php
/**
 * Append coupon form to order details in checkout page
 */
class AnonyAppendCouponForm{
	
	public function  __construct(){
		
		if(!function_exists('is_checkout')) return;
		
		// Just hide default woocommerce coupon field
		add_action( 'woocommerce_before_checkout_form', 'hide_checkout_coupon_form', 5 );
		
		// Add a custom coupon field before checkout payment section
		add_action( 'woocommerce_review_order_before_payment', 'woocommerce_checkout_coupon_form_custom' );
		
		// jQuery code
		add_action( 'wp_footer', 'custom_checkout_jquery_script' );
	}
	
	function hide_checkout_coupon_form() {
	    echo '<style>.woocommerce-form-coupon-toggle {display:none;}</style>';
	}
	
	
	function woocommerce_checkout_coupon_form_custom() {
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
	
	function custom_checkout_jquery_script() {
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