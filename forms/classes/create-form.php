<?php 
if( ! class_exists( 'ANONY_Create_Form' )){
	
	class ANONY_Create_Form{
		
		/**
		 * @var string form's ID. should be unique foreach form
		 */
		protected $id = null;
		
		/**
		 * @var string form's method
		 */
		protected $method = 'post';
		
		/**
		 * @var string form's action
		 */
		protected $action = '';
		
		/**
		 * @var string form's attributes
		 */
		protected $form_attributes = '';
		
		/**
		 * @var array form errors
		 */
		protected $errors = [];
		
		/**
		 * @var array form settings
		 */
		protected $settings = [];
		
		/**
		 * @var array Form mandatory fields
		 */
		protected $form_init = ['id', 'fields'];
		
		/**
		 * @var array fields that can't be validated
		 */
		protected $no_validation = ['heading', 'group-start', 'group-close'];
		
		
		/**
		 * @var array Form fields
		 */
		protected $fields;
		
		
		/**
		 * @var object Holds an object from ANONY_Validate_Inputs
		 */
		public $validate;
		
		/**
		 * @var object Holds validated form data
		 */
		public $validated = [];
		
		/**
		 * Constructor
		 */ 
		public function __construct( array $form){
			
			if(
				count(array_intersect($this->form_init, array_keys($form))) !== count($this->form_init)
				||
				$form['id'] == ''
			){
				$this->errors['missing-for-id'] = esc_html__('Form id is missing');
			}
			
			extract ($form);
			
			//Set form Settings
			if(isset($settings) && is_array($settings)) $this->formSettings($settings);
			
			$this->id =  $id;
			$this->fields =  $fields;
			
			//Submitted form
			$this->formSubmitted();
						
			$this->create($this->fields);
		}
		
		protected function formSettings(array $form_settings){
			$this->settings['inline_lable'] = true;
			
			$this->settings = ANONY_ARRAY_HELP::defaultsMapping($this->settings, $form_settings);

		}
		
		protected function create(array $fields){
			extract ($this->settings);
			if(false !== $this->error_msgs = get_transient('anony_form_errors_'.$this->id) ){
				echo '<ul>';
				foreach($this->error_msgs as $msg){
					echo '<li>'.$msg.'</li>';
				}
				echo '</ul>';
				
				delete_transient('anony_form_errors_'.$this->id);
			}
		?>
			<form id="<?= $this->id ?>" class="anony-form" action="<?= $this->action ?>" method="<?= $this->method ?>" <?= $this->form_attributes ?>>
			
				<?php 
					foreach($fields as $field):
						$render_field = new ANONY_Input_Field($field, $this->id, 'form');
						echo $render_field->field_init();
					endforeach;
					
					do_action('anony_form_fields', $fields);
				?>
				<p>
					<input type="submit" id="submit-<?= $this->id ?>" name="submit-<?= $this->id ?>" value="<?= esc_html__('submit', ANOE_TEXTDOM) ?>"/>
				</p>
				
			</form>
		<?php 
		
			do_action('anony_form_after', $fields);
		
		}
		
		protected function validateFormFields($fields){
			
			if($_SERVER['REQUEST_METHOD'] !== 'POST') return;
			foreach($fields as $field):
				$this->validate($field);
			endforeach;
			
		}
		protected function validate($field){
			
			$notValidated = $_POST;
			//Types that can't be validated
			if(in_array($field['type'], $this->no_validation)) return;
			
			
			//Check if validation required
			if(isset($field['validate'])){
				
				$fieldID = $field['id'];

				$args = array(
							'field'     => $field,
							'new_value' => $notValidated[$fieldID],
						);
				
				$this->validate = new ANONY_Validate_Inputs($args);
				
				//Add to errors if not valid
				if(!empty($this->validate->errors)){

					$this->errors =  array_merge((array)$this->errors, (array)$this->validate->errors);
					
					foreach($this->errors as $id => $arr){
						extract($arr);
						$this->error_msgs[] = $this->validate->get_error_msg($code, $id);
					}
					

					return ;//We will not add to $validated 
				}

				$this->validated[$fieldID] = is_null($this->validate->value) ? '' : $this->validate->value;

			}else{

				$this->validated[$fieldID] = $notValidated[$fieldID];
			}
		}
		
		protected function formSubmitted(){
			
			if($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_POST['submit-'. $this->id ])) return;
			
			//Validation 
			$this->validateFormFields($this->fields); //Validaion problem because fields' ids looks like field[key]
			
			/*
			
			if($this->id == 'award_form_2'){
				var_dump(isset($_POST['submit-'. $this->id ])); die();
			}
			*/
			if(isset($this->error_msgs)) set_transient('anony_form_errors_'.$this->id, $this->error_msgs);

			do_action('anony_form_submitted', $this->validated, $this->id);
			
		}
	}
}