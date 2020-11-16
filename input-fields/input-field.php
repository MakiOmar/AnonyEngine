<?php

if( ! class_exists( 'ANONY_Input_Field' )){
	/**
	 * A class that renders input fields according to context
	 */
	class ANONY_Input_Field
	{
		/**
		 * @var array An array of inputs that have same HTML markup
		 */
		public $mixed_types = ['text','number','email', 'password','url', 'hidden'];

		/**
		 * @var string Field php class name
		 */
		public $field_class;

		/**
		 * @var string input field name attribute value
		 */
		public $input_name;

		/**
		 * @var array An array of field's data
		 */
		public $field;

		/**
		 * @var int Post id for field that should be shown inside a post
		 */
		public $object_id;

		/**
		 * @var string The context of where the field is used
		 */
		public $context;

		/**
		 * @var object an object from the options class
		 */
		public $options;

		/**
		 * @var mixed Field value
		 */
		public $value;

		/**
		 * @var mixed Default field value
		 */
		public $default;

		/**
		 * @var string HTML class attibute value
		 */
		public $class_attr;

		/**
		 * @var bool Wheather field will be used as template or real input
		 */
		public $as_template;
		
		/**
		 * @var mixed input field value
		 */
		public $field_value;

		/**
		 * @var int index of multi value fields in multi value array 
		 */
		public $index;

		/**
		 * Inpud field constructor That decides field context
		 * @param array    $field    An array of field's data
		 * @param string   $context  The context of where the field is used
		 * @param int|null $object_id  Should be an integer if the context is meta box
		 */
		function __construct($field, $metabox_id = null, $context = 'option', $object_id = null, $as_template = false, $field_value = null, $index = null)
		{
			
			$this->as_template = $as_template;

			$this->field_value = $field_value;

			$this->index       = $index;

			$this->options     = (isset($field['option_name']) && class_exists('ANONY_Options_Model')) ? ANONY_Options_Model::get_instance( $field['option_name']) : '';

			$this->field       = $field;

			$this->metabox_id  = $metabox_id;

			$this->object_id     = $object_id;

			$this->context     = $context;

			$this->default     = isset($this->field['default']) ? $this->field['default'] : '';

			$this->class_attr  = ( isset($this->field['class']) ) ? $this->field['class'] : 'anony-input-field';

			$this->set_field_data();

			$this->select_field();

			$this->enqueue_scripts();
		}


		/**
		 * Set field data depending on the context
		 */
		public function set_field_data(){
			switch ($this->context) {
				case 'option':
						$this->opt_field_data();
					break;

				case 'meta':
				case 'term':
						$this->meta_field_data();
					break;
				
				default:
					$this->input_name = $this->field['id'];
					break;
			}
		}



		/**
		 * Set options field data
		 */
		public function opt_field_data(){
			$this->input_name = isset($this->field['name'])  ? $this->field['option_name'].'['.$this->field['name'].']' : $this->field['option_name'].'['.$this->field['id'].']';

			$fieldID      = $this->field['id'];
			
			$this->value = (isset($this->options->$fieldID) && !empty($this->options->$fieldID)) ? $this->options->$fieldID : $this->default;
			
		}

		/**
		 * Set metabox field data
		 */
		public function meta_field_data(){
			if (isset($this->field['nested-to']) && !empty($this->field['nested-to'])) {
				$index = (is_integer($this->index)) ? $this->index : 0;

				$this->input_name = $this->metabox_id.'['.$this->field['nested-to'].']'.'['.$index.']'.'['.$this->field['id'].']';

				$this->field['id'] = $this->field['id'].'-'.$index;
			}else{
				$this->input_name =  $this->metabox_id.'['.$this->field['id'].']';
			}

			$single = (isset($this->field['multiple']) && $this->field['multiple']) ? false : true;
			
			//This should be field value to be passed to input field object.
			//Now within the multi value input field
			if(!is_null($this->field_value)){

				$meta = $this->field_value;

			}else{

				if ($this->context == 'term') {
					$metabox_options = get_term_meta( $this->object_id, $this->metabox_id, true);
				}else{
					$metabox_options = get_post_meta( $this->object_id, $this->metabox_id, $single);
				}
		
				$meta = (is_array($metabox_options) && isset($metabox_options[$this->field['id']])) ? $metabox_options[$this->field['id']] : '';
			}

			$this->value = ($meta  != '') ? $meta : $this->default;
			
		}

		/**
		 * Set the desired class name for input field
		 * @return string Input field class name
		 */
		function select_field()
		{
			if(isset($this->field['type']))
			{
				//Static class name for inputs that have same HTML markup
				if(in_array($this->field['type'], $this->mixed_types))
				{
					$this->field_class = 'ANONY_Mixed';
				}else
				{
					$this->field_class = 'ANONY_'.ucfirst($this->field['type']);

				}
				
			}
			return $this->field_class;
		}

		/**
		 * Initialize options field
		 */
		function field_init(){
			if(!is_null($this->field_class) && class_exists($this->field_class))
			{
				
				$field_class = $this->field_class;
				
							
				$field = new $field_class($this);

				//Options fields can't be on frontend
				if($this->context == 'option') return $field->render();
				
				if($this->context == 'meta' && !is_admin()){
					//If there is an insert Or edit front end action
					if (isset($_GET['action']) && !empty($_GET['action']) && isset($_GET['_wpnonce'] )&& !empty($_GET['_wpnonce'])) {
						
						switch ($_GET['action']) {
							case 'insert':

								if (wp_verify_nonce( $_GET['_wpnonce'] , 'anonyinsert' )) {
									return $field->render();
								}
								break;

							case 'edit':
								if (wp_verify_nonce( $_GET['_wpnonce'] , 'anonyinsert_'.$this->object_id )) {
									return $field->render();
								}
								break;
							
							default:
								if(method_exists($field, 'renderDisplay')) return $field->renderDisplay();
								break;
						}
					}
	 

					if(method_exists($field, 'renderDisplay')) return $field->renderDisplay();
				}else{
					return $field->render();
				}

			}else{
				return sprintf(esc_html__('%s class doesn\'t exist'),$this->field_class);
			}
		}


		function enqueue_scripts(){
			wp_register_style( 'anony-inputs', ANONY_INPUT_FIELDS_URI.'assets/css/inputs-fields.css', array('farbtastic'), time(), 'all');	

			wp_enqueue_style( 'anony-inputs' );

			if(is_rtl()){
				wp_register_style( 'anony-inputs-rtl', ANONY_INPUT_FIELDS_URI.'assets/css/inputs-fields-rtl.css', array('anony-inputs'), time(), 'all');
				wp_enqueue_style( 'anony-inputs-rtl' );
			}

		}
		
	}
}