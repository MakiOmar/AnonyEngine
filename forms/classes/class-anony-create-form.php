<?php
if ( ! class_exists( 'ANONY_Create_Form' ) ) {

	class ANONY_Create_Form {

		/**
		 * @var string form's ID. should be unique foreach form
		 */
		public $id = null;

		/**
		 * @var string form's method
		 */
		public $method = 'post';

		/**
		 * @var string form's action
		 */
		public $action = '';

		/**
		 * @var string form's attributes
		 */
		public $form_attributes = '';

		/**
		 * @var array form errors
		 */
		public $errors = array();

		/**
		 * @var array form settings
		 */
		public $settings = array();

		/**
		 * @var array Form mandatory fields
		 */
		public $form_init = array( 'id', 'fields' );

		/**
		 * @var array fields that can't be validated
		 */
		public $no_validation = array( 'heading', 'group-start', 'group-close' );


		/**
		 * @var array Form fields
		 */
		public $fields;


		/**
		 * @var object Holds an object from ANONY_Validate_Inputs
		 */
		public $validate;

		/**
		 * @var object Holds validated form data
		 */
		public $validated = array();

		/**
		 * Constructor
		 */
		public function __construct( array $form ) {

			if (
				count( array_intersect( $this->form_init, array_keys( $form ) ) ) !== count( $this->form_init )
				||
				$form['id'] == ''
			) {
				$this->errors['missing-for-id'] = esc_html__( 'Form id is missing' );
			}

			// Set form Settings
			if ( isset( $settings ) && is_array( $settings ) ) {
				$this->formSettings( $settings );
			}

			$this->id     = $form[ 'id' ];
			$this->fields = $form[ 'fields' ];
			$this->submit_label = isset( $form[ 'submit_label' ] ) && !empty( $form[ 'submit_label' ] ) ? $form[ 'submit_label' ] : esc_attr(__( 'Submit', 'anonyengine' ));

			add_shortcode( $this->id, array( $this, 'createShortcode' ) );

			// Submitted form
			add_action( 'init', array( $this, 'formSubmitted' ) );

		}

		function createShortcode() {
			ob_start();
			$this->create( $this->fields );
			return ob_get_clean();
		}

		public function formSettings( array $form_settings ) {
			$this->settings['inline_lable'] = true;

			$this->settings = ANONY_ARRAY_HELP::defaultsMapping( $this->settings, $form_settings );

		}

		public function create( array $fields ) {
			extract( $this->settings );
			if ( false !== $this->error_msgs = get_transient( 'anony_form_errors_' . $this->id ) ) {
				echo '<ul>';
				foreach ( $this->error_msgs as $msg ) {
					echo '<li>' . $msg . '</li>';
				}
				echo '</ul>';

				delete_transient( 'anony_form_errors_' . $this->id );
			}
			?>
			<form id="<?php echo $this->id; ?>" class="anony-form" action="<?php echo $this->action; ?>" method="<?php echo $this->method; ?>" <?php echo $this->form_attributes; ?>>
			
				<?php
				foreach ( $fields as $field ) :
					$render_field = new ANONY_Input_Field( $field, $this->id, 'form' );
					echo $render_field->field_init();
					endforeach;

					do_action( 'anony_form_fields', $fields );
				?>
				<p>
					<button type="submit" id="submit-<?php echo $this->id; ?>" name="submit-<?php echo $this->id; ?>" value="submit-<?php echo $this->id; ?>"><?php echo $this->submit_label ?></button>
				</p>
				
			</form>
			<?php

			do_action( 'anony_form_after', $fields );

		}

		public function validateFormFields( $fields ) {

			if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
				return;
			}
			foreach ( $fields as $field ) :
				if ( ! isset( $_POST[ $field['id'] ] ) ) {
					continue;
				}
				$this->validate( $field );
			endforeach;

		}
		public function validate( $field ) {

			$notValidated = $_POST;
			// Types that can't be validated
			if ( in_array( $field['type'], $this->no_validation ) ) {
				return;
			}

			// Check if validation required
			if ( isset( $field['validate'] ) ) {

				$fieldID = $field['id'];

				$args = array(
					'field'     => $field,
					'new_value' => $notValidated[ $fieldID ],
				);

				$this->validate = new ANONY_Validate_Inputs( $args );

				// Add to errors if not valid
				if ( ! empty( $this->validate->errors ) ) {

					$this->errors = array_merge( (array) $this->errors, (array) $this->validate->errors );

					foreach ( $this->errors as $id => $arr ) {
						extract( $arr );
						$this->error_msgs[] = $this->validate->get_error_msg( $code, $id );
					}

					return;// We will not add to $validated
				}

				$this->validated[ $fieldID ] = is_null( $this->validate->value ) ? '' : $this->validate->value;

			} else {

				$this->validated[ $fieldID ] = $notValidated[ $fieldID ];
			}
		}

		public function formSubmitted() {

			if ( $_SERVER['REQUEST_METHOD'] !== 'POST' && ! isset( $_POST[ 'submit-' . $this->id ] ) ) {
				return;
			}

			// Validation
			$this->validateFormFields( $this->fields ); // Validaion problem because fields' ids looks like field[key]

			/*
			if($this->id == 'award_form_2'){
				var_dump(isset($_POST['submit-'. $this->id ])); die();
			}
			*/
			if ( isset( $this->error_msgs ) ) {
				set_transient( 'anony_form_errors_' . $this->id, $this->error_msgs );
			}

			do_action( 'anony_form_submitted', $this->validated, $this->id );

		}
	}
}
