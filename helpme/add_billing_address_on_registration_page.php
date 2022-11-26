<?php
/**
 * Add billing fields to registration form
 *
 */
function anony_billing_registration_fields(){
		global $woocommerce;
		$checkout = $woocommerce->checkout();
		//print_r($checkout);
		$checkout_fields = $checkout->checkout_fields['billing'];
		unset( $checkout_fields['billing_email']);

		foreach ( $checkout_fields as $key => $field) :
			woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
		endforeach;
		echo '<div class="clear"></div>';	
}
add_action('woocommerce_register_form_start','anony_billing_registration_fields');

/**
 * Field Validation
 */
function anony_validate_extra_register_fields( $username, $email, $validation_errors ) {
       if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {

              $validation_errors->add( 'billing_first_name_error', __( 'First name is required.', 'woocommerce' ) );

       } 

       if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {

              $validation_errors->add( 'billing_last_name_error', __( 'Last name is required.', 'woocommerce' ) );

       } 

       if ( isset( $_POST['billing_phone'] ) && empty( $_POST['billing_phone'] ) ) {

              $validation_errors->add( 'billing_phone_error', __( 'Phone is required.', 'woocommerce' ) );

       }
       if ( isset( $_POST['billing_company'] ) && empty( $_POST['billing_company'] ) ) {

              $validation_errors->add( 'billing_billing_company', __( 'Company Name is required.', 'woocommerce' ) );

       }     
       if ( isset( $_POST['billing_postcode'] ) && empty( $_POST['billing_postcode'] ) ) {

              $validation_errors->add( 'billing_billing_postcode', __( 'Postcode is required.', 'woocommerce' ) );

       }
        if ( isset( $_POST['billing_city'] ) && empty( $_POST['billing_city'] ) ) {

              $validation_errors->add( 'billing_billing_city', __( 'City/Subub is required.', 'woocommerce' ) );

       }
       if ( isset( $_POST['billing_address_1'] ) && empty( $_POST['billing_address_1'] ) ) {

              $validation_errors->add( 'billing_billing_address_1', __( 'Street Address is required.', 'woocommerce' ) );

       }
       if ( isset( $_POST['billing_country'] ) && empty( $_POST['billing_country'] ) ) {

              $validation_errors->add( 'billing_billing_country', __( 'Country is required.', 'woocommerce' ) );

       }
       if ( isset( $_POST['billing_state'] ) && empty( $_POST['billing_state'] ) ) {

              $validation_errors->add( 'billing_billing_state', __( 'State is required.', 'woocommerce' ) );

       }
}
add_action( 'woocommerce_register_post', 'anony_validate_extra_register_fields', 10, 3 );

/**
 * Save the extra register fields.
 *
 * @paramint $customer_id Current customer ID.
 * @return void
 */
function anony_save_extra_register_fields( $customer_id ){

       if ( isset( $_POST['billing_first_name'] ) ) {
              // WordPress default first name field.
              update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
              // WooCommerce billing first name.
              update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
        }
       if ( isset( $_POST['billing_last_name'] ) ) {
              // WordPress default last name field.
              update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
              // WooCommerce billing last name.
              update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
          }
       if ( isset( $_POST['billing_phone'] ) ) {
              // WooCommerce billing phone
              update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
       }
       if ( isset( $_POST['billing_company'] ) ) {
              // WooCommerce billing_company
              update_user_meta( $customer_id, 'billing_company', sanitize_text_field( $_POST['billing_company'] ) );
       }
       if ( isset( $_POST['billing_postcode'] ) ) {
              // WooCommerce billing_postcode
              update_user_meta( $customer_id, 'billing_postcode', sanitize_text_field( $_POST['billing_postcode'] ) );
       }
       if ( isset( $_POST['billing_address_1'] ) ) {
              // WooCommerce billing_address_1
              update_user_meta( $customer_id, 'billing_address_1', sanitize_text_field( $_POST['billing_address_1'] ) );
       }
       if ( isset( $_POST['billing_address_2'] ) ) {
              // WooCommerce billing_address_2
              update_user_meta( $customer_id, 'billing_address_2', sanitize_text_field( $_POST['billing_address_2'] ) );
       }
       if ( isset( $_POST['billing_city'] ) ) {
              // WooCommerce billing_city
              update_user_meta( $customer_id, 'billing_city', sanitize_text_field( $_POST['billing_city'] ) );
       }
       if ( isset( $_POST['billing_state'] ) ) {
              // WooCommerce billing_state
              update_user_meta( $customer_id, 'billing_state', sanitize_text_field( $_POST['billing_state'] ) );
       }
       if ( isset( $_POST['billing_country'] ) ) {
              // WooCommerce billing_country
              update_user_meta( $customer_id, 'billing_country', sanitize_text_field( $_POST['billing_country'] ) );
       }
}
add_action( 'woocommerce_created_customer', 'anony_save_extra_register_fields' );