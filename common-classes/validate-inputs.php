<?php
/**
 * Inputs validation
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

if(!class_exists('ANONY_Validate_Inputs')){
	class ANONY_Validate_Inputs{
		/**
		 * @var array Holds an array of fields and there corresponding error code as key/value pairs
		 */
		public $errors = array();
		
		/**
		 * @var array Holds an array of fields and there corresponding warning code as key/value pairs
		 */
		public $warnings = array();

		/**
		 * @var boolean Decide if valid input. Default is <code>false</code>
		 *
		 */
		public $valid = true;
		
		/**
		 * @var string Inputs value
		 *
		 */
		public $value;
		
		/**
		 * @var string validations limits
		 *
		 */
		public $limits = '';

		/**
		 * @var array field data
		 *
		 */
		public $field;

		/**
		 * @var string Field's validation type
		 *
		 */
		public $validation;

		/**
		 * @var string Field's sanitization function name
		 *
		 */
		public $sanitization;
		
		/**
		 * Constructor.
		 * 
		 * @param string|array array of field's date required for validation.<br/>**Note: **Empty $args so the class can be instantiated without $args if needed
		 */
		public function __construct($args = ''){

			if(is_array($args) && !empty($args)){

				//Set field's value to the new value before validation
				$this->value = $args['new_value'];

				if(empty($this->value)) return;//if level 2-1


				$this->field = $args['field'];
				
				if(isset($this->field['validate'])){

					$this->select_sanitization();
					
					$this->validation = $this->field['validate'];
					
					$this->validate_inputs();
				}

			}//if level 1

		}//function

		/**
		 * Select sanitization function name for a field
		 * @return string Returns the name of sanitization function.
		 */
		public function select_sanitization(){

			switch ($this->field['type']) {
				case 'textarea':
					$this->sanitization = 'sanitize_textarea_field';
					break;

				case 'email':
					$this->sanitization = 'sanitize_email';
					break;

				case 'url':
					$this->sanitization = 'esc_url_raw';
					break;

				case 'upload':
					$this->sanitization = 'esc_url_raw';
					break;
				
				default:
					$this->sanitization = 'sanitize_text_field';
					break;
			}

		}//function
		
		/**
		 * Inputs validation base function
		 *
		 * **Description: **Invoke the corresponding validtion function according to the <code>$args['validation']</code>.<br>
		 * **Note: **<br/>
		 * * <code>$args['validation']</code> value can be equal to <code>'int|file_type:pdf,doc,docx'</code>.
		 * * validation types are separated with <code>|</code> and if the validation has any limits like supported file types, so sholud be followd by <code>:</code> then the limits.
		 * * Limits should be separated with <code>,</code>.
		 *
		 * @param  array $args array of fields's validation data
		 * @return void  Just set fields value afte validation
		 */
		public function validate_inputs(){
				
			//Start checking if validation is needed
			if(!is_null($this->validation) || !empty($this->validation)){
				
				//Check if need multiple validations
				if(strpos($this->validation, '|') !== FALSE){
					
					$this->multiple_validation($this->validation);
					
				}else{
					
					$this->single_validation($this->validation);
				}//if level 2
				
				
			}//if level 1	
		}//function

		/**
		 * Decide which validation method should be called and sets validation limits.
		 * 
		 * @param  string $value String that contains validation and its limits
		 * @return string Returns validation method name
		 */
		public function select_method($value ='' ){
			//Check if validation has limits
			if(strpos($value, ':') !== FALSE){

				$vald = explode(':', $value);

				//Set Validation limits
				$this->limits = $vald[1];

				//Validation method name
				return $method = 'valid_'.$vald[0];

			}else{

				//Validation method name
				return $method = 'valid_'.$value;

			}//if level 1
		}//function

		/**
		 * Call validation method if the validation is single. e.g. url
		 * @param string $validation Validation string. can be something like (file_type: pdf, docx).
		 * 
		 * @return void
		 */
		public function single_validation($validation = ''){

			$method = $this->select_method($validation);

			//Apply validation method
			if(method_exists($this, $method)) $this->$method();
		}//function
		
		/**
		 * Call validation method if the validation is multiple. e.g. url|file_type: pdf,docx.
		 * 
		 * @param  string $validation Validation string.
		 * @return void
		 */
		public function multiple_validation($validations = ''){
			
			//Array to hold validation types
			$_validations = explode('|', $validations);
			
			//Validate fore each validation type
			foreach($_validations as $validation){

				$this->single_validation($validation);

			}//forach
		}//function

		/**
		 * Sanitize field value dynamicaly
		 * @return string|array  Sanitized value/s
		 */
		public function sanitize(){
			$sanitization = $this->sanitization;

			if(is_array($this->value)){
				//Temporary array to hold sanitized values
				$temp_value = [];

				foreach ($this->value as $key => $value) {
					
					$temp_value[$key] = $sanitization($value);
				}

				return $this->value = $temp_value;

			}

			return $this->value  = $sanitization($this->value);
		}
		
		/**
		 * Check through multiple options (select, radio, multi-checkbox)
		 */
		public function valid_multiple_options(){
			
			$options_keys = array_keys($this->field['options']);
			
			//If checked/selected multiple options
			if(is_array($this->value)){
				
				//Get intersection between values array and the pre-set options array keys.
				$intersection = array_intersect($this->value, $options_keys);
				
				if(count($intersection) != count($this->value)) $this->valid = false;

			//If checked/selected one option e.g. radio
			}else{

				if(!in_array($this->value, $options_keys)) $this->valid = false;

			}//if level 1

			$this->set_error_code('strange-options');	
		}//function
		
		/**
		 * Accept html within input.
		 */
		public function valid_html(){

			$this->value =  wp_kses_post($this->value);
		}//function


		/**
		 * validate multi-value input
		 */
		public function valid_multi_value(){

			foreach ($this->value as $index => $value) {
				//Check if all supplied values are empty
				if(implode('', $value) == '') unset($this->value[$index]);
			}
			
		}//function


		/**
		 * Accept html within input.
		 */
		public function valid_tabs(){
			$count = array_shift($this->value);
			if(!ctype_digit($count)) {
				$count = count($this->value) + 1;
			}
			$temp = [];

			$temp['count'] = $count;
			foreach ($this->value as $name => $v) {
				foreach ($v as $key => $value) {
					$value = strip_tags( $value );

					$temp[$name][$key] = $value;
				
				}

				$temp_name_values = array_values($temp[$name]);

				//Check if all supplied values are empty
				if(implode('', $temp_name_values) == '') unset($temp[$name]);
			}
			$temp['count'] = empty($temp) ? 2 : count($temp) + 1;

			$this->value =  $temp;
		}//function

		/**
		 * Date validation.
		 */
		public function valid_date(){

			$timestamp = strtotime($this->value);

			if( $timestamp === false){
				$this->valid = false;
			    return $this->set_error_code('not-date');
			}
			
		}//function
		
		/**
		 * Remove html within input
		 */
		public function valid_no_html(){
			
			
			if(is_array($this->value)){

				foreach ($this->value as $value) {
					
					if(strip_tags($value) != $value){
						$this->valid = false;

						return $this->set_error_code('remove-html');
					}
				}
			}else{
				if(strip_tags($this->value) != $this->value){

					$this->valid = false;

					return $this->set_error_code('remove-html');
				}
			}
			

			$this->sanitize();

		}//function
		
		/**
		 * Check valid email
		 */
		public function valid_email(){

			if($this->value == '#') return;
							
			if(!is_email($this->value) ){

				$this->valid = false;

				return $this->set_error_code('not-email');
			}

			
			$this->sanitize();
		}//function
		
		/**
		 * check valid url
		 */
		public function valid_url(){
			
			if($this->value == '#' || empty($this->value)) return;
			
			if (filter_var($this->value, FILTER_VALIDATE_URL) !== FALSE ) {
				
				$this->valid = false;

				return $this->set_error_code('not-url');
				
			}
				
			$this->sanitize();
				
		}//function
		
		/**
		 * Check if valid number.
		 */
		public function valid_number(){
			
			if(preg_replace('/[0-9\.\-]/', '', $this->value) != '') {

				$this->valid = false;

				return $this->set_error_code('not-number');
			}
				
			$this->sanitize();

		}//function

		/**
		 * Check valid integer
		 */
		public function valid_abs(){
			
			if(!ctype_digit($this->value)) {

				$this->valid = false;

				return $this->set_error_code('not-abs');
			}
			
			$this->sanitize();
		}//function
		
		/**
		 * Check valid file type
		 */
		public function valid_file_type(){
			
			$limits = explode(',',$this->limits);
			
			$ext = pathinfo(esc_url($this->value), PATHINFO_EXTENSION);

			if(!empty($limits) && !in_array($ext, $limits)) {

				$this->valid = false;

				return $this->set_error_code('unsupported');;
			}
		
			$this->sanitize();	

		}//function
		
		/**
		 * Check valid hex color
		 */
		public function valid_hex_color(){
			
			if(is_array($this->value)){

				foreach ($this->value as $key => $hex) {

					if ( !$this->is_hex_color($hex) ){
						$this->valid = false;
						break; //Break if any of values is not a hex color
					}//if level 2
				}//foreach

			}else{

				if( !$this->is_hex_color($this->value) ){

					$this->valid = false;

				}//if level 2
			}
			

			if(!$this->valid) return $this->set_error_code('not-hex');

			$this->sanitize();
		}//function

		/**
		 * Check if a string is hex color.
		 *
		 * @param string $string String to be check
		 * @return bool  Returns true if is valid hex or false if not.
		 */
		public function is_hex_color($string){

			if(empty($string)) return true;

			$check_hex = preg_match( '/^#[a-f0-9]{3,6}$/i', $string );
					
			if ( !$check_hex || $check_hex === 0 ) return false;

			return true;
		}//function
		
		/**
		 * Set error message code
		 * @param string $code 
		 * @return void
		 */
		public function set_error_code($code){
			if(!$this->valid){

				$this->errors[$this->field['id']] = [
					'code'  => $code, 
				];
			}//if level 1
		}//function

		/**
		 * Gets the error message attached to $code
		 * @param string $code Message code
		 * @param string $field_id Field id to be shown with message
		 * @return string The error message
		 */		
		public static function get_error_msg($code, $field_id){

			if (empty($code)) return;

			$accepted_tags = array('strong'=>array(), 'a'=> array('href'=> array(), 'class' => array(), 'rel-id' => array()));

			switch($code){
				case "unsupported":
					
					return sprintf(
						wp_kses(
							__( '<a href="#%1$s" rel-id="%1$s" class="meta-error"><strong>%2$s:</strong></a> Sorry!! Please select another file, the selected file type is not supported. <a>', ANOE_TEXTDOM  ), 
							$accepted_tags
						), 
						$field_id,

						esc_html__( 'Here', ANOE_TEXTDOM  )
					);
					
					break;

				case "not-date":
					
					return sprintf(
						wp_kses(
							__( '<a href="#%1$s" rel-id="%1$s" class="meta-error"><strong>%2$s:</strong></a> Sorry!! The entered date is not valid', ANOE_TEXTDOM  ), 
							$accepted_tags
						), 
						$field_id,

						esc_html__( 'Here', ANOE_TEXTDOM  )
					);
					
					break;

				case "incorrect-date-format":
					
					return sprintf(
						wp_kses(
							__( '<a href="#%1$s" rel-id="%1$s" class="meta-error"><strong>%2$s:</strong></a> Sorry!! Date format is not supported', ANOE_TEXTDOM  ),
							$accepted_tags
						), 
						$field_id,

						esc_html__( 'Here', ANOE_TEXTDOM  )
					);
					
					break;
					
				case "not-number":
					
					return sprintf(
						wp_kses(
							__('<a href="#%1$s" rel-id="%1$s" class="meta-error"><strong>%2$s:</strong></a> Please enter a valid number (e.g. 1,2,-5)', ANOE_TEXTDOM ), 
							$accepted_tags
						), 
						$field_id,

						esc_html__( 'Here', ANOE_TEXTDOM  )
					);
					
					break;

				case "not-url":
					
					return sprintf(
						wp_kses(
							__('<a href="#%1$s" rel-id="%1$s" class="meta-error"><strong>%2$s:</strong></a> You must provide a valid URL', ANOE_TEXTDOM ),
							$accepted_tags
						),
						$field_id,

						esc_html__( 'Here', ANOE_TEXTDOM  )
					);
					
					break;
					
				case "not-email":
					
					return sprintf(
						wp_kses(
							__('<a href="#%1$s" rel-id="%1$s" class="meta-error"><strong>%2$s:</strong></a> You must enter a valid email address.', ANOE_TEXTDOM ), 
							$accepted_tags
						), 
						$field_id,

						esc_html__( 'Here', ANOE_TEXTDOM  )
					);
					
					break;
					
				case "remove-html":
					
					return sprintf(
						wp_kses(
							__('<a href="#%1$s" rel-id="%1$s" class="meta-error"><strong>%2$s:</strong></a> HTML is not allowed', ANOE_TEXTDOM ), 
							$accepted_tags
						), 
						$field_id,

						esc_html__( 'Here', ANOE_TEXTDOM  )
					);
					
					break;
					
				case "not-abs":
					
					return sprintf(
						wp_kses(
							__('<a href="#%1$s" rel-id="%1$s" class="meta-error"><strong>%2$s:</strong></a> You must enter an absolute integer', ANOE_TEXTDOM ), 
							$accepted_tags
							   ), 
						$field_id,

						esc_html__( 'Here', ANOE_TEXTDOM  )
					);
					
					break;
					
				case "not-hex":
					
					return sprintf(
						wp_kses(
							__('<a href="#%1$s" rel-id="%1$s" class="meta-error"><strong>%2$s:</strong></a> You must enter a valid hex color', ANOE_TEXTDOM ), 
							$accepted_tags
							   ), 
						$field_id,

						esc_html__( 'Here', ANOE_TEXTDOM  )
					);
					
					break;
					
				case "strange-options":
					
					return sprintf(
						wp_kses(
							__('<a href="#%1$s" rel-id="%1$s" class="meta-error"><strong>%2$s:</strong></a> Unvalid option/s', ANOE_TEXTDOM ), 
							$accepted_tags
							   ), 
						$field_id,

						esc_html__( 'Here', ANOE_TEXTDOM  )
					);
					
					break;
					
				default:
					return wp_kses(
						__( '<strong>Sorry!! Something wrong:</strong> Please make sure all your inputs are correct', ANOE_TEXTDOM  ), 
						$accepted_tags
					);
			}//switch
		}//function
	}
}