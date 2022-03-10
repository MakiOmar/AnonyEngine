<?php
/**
 * Woocommerce custom registration fields.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makior.com>
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine_elements.
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_Woocommerce_Registration_Fields' ) ) {

    /**
     * Woocommerce custom registration fields.
     *
     * @package    AnonyEngine Woocommerce
     * @author     Makiomar <info@makior.com>
     * @license    https:// makiomar.com AnonyEngine Licence.
     * @link       https:// makiomar.com.
     */
    class ANONY_Woocommerce_Registration_Fields {


        /**
         * Registration fields.
         * 
         * @var array
         */  
        public $registration_fields;

        /**
         * Field position.
         *
         * Use 'end' for woocommerce_register_form and 'start' for woocommerce_register_form_start . Default is 'end'.
         * 
         * @var array
         */  
        public $field_position = 'end';

        /**
         * Filter registration fields.
         *
         * @return array An array of registratio fields
         */ 
        public function __construct( array $fields = [] ){
            
            $this->registration_fields = $this->registration_fields( $fields );

            $this->init();


            add_action( 'woocommerce_register_post', array( $this, 'validate' ), 10, 3 );
        }

        /**
         * Filter registration fields.
         *
         * @return array An array of registratio fields
         */ 
        protected function registration_fields( $fields ){
            return apply_filters( 'anony_woocommerce_registraion_fields', $fields );
        }

        protected function decide_position(){

            switch ( $this->field_position ) {
                case 'end':
                    return 'woocommerce_register_form';
                    break;

                case 'start':
                    return 'woocommerce_register_form_start';
                    break;
                
                default:
                    return 'woocommerce_register_form';
                    break;
            }
        }

        protected function init() {

            if ( [] === $this->registration_fields ) {
                return;
            }

            foreach ($this->registration_fields as $field_name => $field_data) {

                if ( isset ( $field_data['position'] ) ) {
                    $this->field_position = $field_data['position'];
                }
                
                add_action( $this->decide_position(), function() use( $field_name, $field_data ) {

                    $render_field = new ANONY_Input_Field( $field_data, null, 'form' );

                    echo $render_field->field_init();

                } );
                

            }
        }

        /**
         * Validate field value
         *
         * @param array $field     Field's data array
         * @param mixed $new_value Field's new value
         * @return object          Validation object
         */
        public function validate_field( $field, $new_value ) {
            $args     = array(
                'field'     => $field,
                'new_value' => $new_value,
            );
            $validate = new ANONY_Validate_Inputs( $args );

            return $validate;
        }

        public function validate( $username, $email, $validation_errors ) {

            if ( [] === $this->registration_fields ) {
                return;
            }


            foreach ($this->registration_fields as $field_name => $field) {

                $value = $_POST[$field_name];

                if ( !isset( $field['validate'] ) ) {
                    continue;
                }

                $validated = $this->validate_field( $field, $value );

                if ( !empty( $validated->errors ) ) {
                    
                    foreach ($validated->errors as $id => $codes) {

                        foreach ($codes as $code) {

                            $validation_errors->add( $id . '_error', $validated->get_error_msg($code, $id) );

                        }
                    }
                    
                }

                return $validation_errors;
                //error_log(print_r($validated, true));

                //die();
            }


        }
    }
}