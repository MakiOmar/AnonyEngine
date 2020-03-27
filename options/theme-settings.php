<?php
/**
 * Theme options class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

if (!class_exists('ANONY_Theme_Settings')) {
	class ANONY_Theme_Settings{
		
		/**
		 * @var array Array of input fields errors. array('field_id' => 'error')
		 */
		public $errors = array();
		
		/**
		 * @var string Holds the resulting page's hook_suffix from add_theme_page
		 */
		public $page;

		/**
		 * @var string Holds the option group name for register_setting
		 */
		public $OptionGroup;

		/**
		 * @var array Holds An array of options page's sections
		 */
		public $sections = array();

		/**
		 * @var array Holds An array of options page's data
		 */
		public $args = array();

		/**
		 * @var array Holds An array of widgets to be registered
		 */
		public $widgets = array();

		/**
		 * @var object Holds an object from ANONY_Options_Model
		 */
		public $options;

		/**
		 * @var object Holds an object from ANONY_Validate_Inputs
		 */
		public $validate;

		/**
		 * @var array Holds an array of options page's menu items
		 */
		public $menu;

		/**
		 * @var array Holds an array of options default values
		 */
		public $defaultOptions;

		/**
		 * Used as a hack to prevent error messages duplication.
		 * 
		 * @var integer Holds a number that represents how many a function is called
		 */
		public static $called = 0;

		/**
		 * @var array Holds options page argument
		 */
		public $options_page;

		/**
		 * @var string Holds page add function name
		 */
		public $page_func_name = 'add_theme_page'; 

		/**
		 * Class Constructor. Defines the args for the theme options class
		 *
		 * @param array $menu array of options page's menu items
		 * @param array $sections array of options page's sections
		 * @param array $widgets array of widgets to be registered
		 */
		public function __construct($menu = array(), $sections = array(), $widgets = array(), $options_page = null){

			$this->options = ANONY_Options_Model::get_instance();

			$this->options_page = $options_page;
			
			$this->menu = $menu;

			//get page defaults
			$this->args = $this->opt_page_defaults();

			//Set option groups
			$this->OptionGroup = $this->args['opt_name'].'_group';
			
			//Options page sections
			$this->sections = $sections;
			
			//Options related widgets
			$this->widgets = $widgets;
			
			//set default values
			$this->default_values();
			
			//register widgets
			$this->register_widgets();
			
			$this->hooks();
			
		}
		
		/**
		 * Set options page defaults
		 * @return array An array of page's defaults e.g. [menu_title, page_title, menu_slug, etc]
		 */
		public function opt_page_defaults(){
			
			$defaults['opt_name'] = ANONY_OPTIONS;

			$defaults['parent_slug'] = '';

			$defaults['page_title'] = esc_html__('Anonymous Theme Options', ANOE_TEXTDOM );
			
			$defaults['menu_title'] = esc_html__('Anonymous Theme Options', ANOE_TEXTDOM );

			$defaults['page_cap'] = 'manage_options';
			
			$defaults['menu_slug'] = 'Anony_Options';

			$defaults['icon_url'] = 'dashicons-welcome-widgets-menus';
			
			$defaults['page_position'] = 100;

			$defaults['page_type'] = 'theme';

			if(!is_null($this->options_page) && is_array($this->options_page) && !empty($this->options_page)){
				if(!isset($this->options_page['menu_slug']) || $this->options_page['menu_slug'] == $defaults['menu_slug']) return $defaults;


				$defaults = wp_parse_args( $this->options_page, $defaults );
			}



			return $defaults;
		}

		/**
		 * Theme options hooks
		 */
		public function hooks(){
			//Styles for options in front end
			add_action('wp_head', array(&$this, 'frontend_styles'));
			
			//Load page scripts
			add_action('admin_enqueue_scripts', array(&$this, 'page_scripts'));
				
			//options page
			add_action('admin_menu', array(&$this, 'options_page'));
			
			//register settings_init to the admin_init action hook
			add_action('admin_init', array(&$this, 'settings_init'));
		
			//set option with defaults		
			add_action('after_setup_theme', array(&$this, 'set_default_options'));

			//Show admin notices
			add_action('admin_notices', array(&$this, 'admin_notices'));

			//Runs after the option with name "option_name" has been updated.
			add_action('update_option_'.$this->args['opt_name'], array($this, 'after_option_update'), 10, 2);

		}

		function after_option_update( $old_value, $value ){
			
		}
		
		/**
		 * Get default options into an array suitable for the settings API
		 */
		public function default_values(){		
			$defaults = array();
			
			foreach($this->sections as $secKey => $section){
				
				if(isset($section['fields'])){
					
					foreach($section['fields'] as $fieldk => $field){	
						if(!isset($field['default'])){
							$field['default'] = '';
						}
						$defaults[$field['id']] = $field['default'];
					}
					
				}
				
			}
			
			$this->defaultOptions = $defaults;
		}
		
		/**
		 * Set default options if option doesnt exist
		 */
		public function set_default_options(){
			
			if( empty(get_option($this->args['opt_name']))){
				delete_option($this->args['opt_name']);
			}
			
			if(!get_option($this->args['opt_name'])){
				
				add_option($this->args['opt_name'], $this->defaultOptions);
								
			}else{
				
				foreach(array_keys($this->defaultOptions)  as $defaultsKey){
					
					if(!in_array($defaultsKey, array_keys($this->options->get_all_current_options()))){
						
						$this->options->add_option($defaultsKey, $this->defaultOptions[$defaultsKey]);
						
					}
					
				}
				
			}	
		}
		
		/**
		 * Class Theme Options Page Function, creates main options page.
		 */
		public function options_page(){
			$screen = get_current_screen();

			if(!current_user_can( 'administrator' )) return;

			$page_func_name = $this->page_func_name;

			$args = $this->args;

			$args = ANONY_ARRAY_HELP::insertBeforeKey($args, 'icon_url', 'function', array(&$this, 'options_page_html') );

			if (isset($this->args['page_type'])) {

				unset($args['opt_name'], $args['page_type']);

				$page_func_name = 'add_'.$this->args['page_type'].'_page';


				switch ($this->args['page_type']) {

					case 'submenu':

						if(isset($this->args['parent_slug']) && !empty($this->args['parent_slug'])){
							
							unset($args['icon_url'], $args['page_position']);
						} 

						break;

					case 'menu':

						unset($args['parent_slug']);


						break;

					
					default:
						if(function_exists($page_func_name)){
							unset($args['icon_url'], $args['page_position'], $args['parent_slug']);
						}

						break;
				}

			}else{
				unset($args['opt_name'], $args['parent_slug'], $args['icon_url'], $args['page_position'], $args['page_type']);
			}

			$this->page = call_user_func_array($page_func_name, array_values($args));

			//Head styles
			add_action('admin_print_styles-'.$this->page, array(&$this, 'admin_styles'));
		}
		
		/*
		 * Class register settings function
		 */
		public function settings_init(){
			// register a new setting for "Anonymous" page
			register_setting(
				$this->OptionGroup,
				$this->args['opt_name'],
				array('sanitize_callback' => array(&$this,'options_validate'))
			);
			
			foreach($this->sections as $secKey => $section){
				
				add_settings_section(
					'anony_'.$secKey.'_section',
					$section['title'],
					array(&$this,'section_cb'),
					//Make sure to add the same in add_settings_field
					$this->args['menu_slug']
				);

				if(isset($section['fields'])){

					foreach($section['fields'] as $fieldKey => $field){

							if(isset($field['title'])){

								$fieldTitle = (isset($field['sub_desc'])) ? $field['title'].'<span class="description">'.$field['sub_desc'].'</span>' : $field['title'];

							}else{
								
								$fieldTitle = '';
								
							}
							
							add_settings_field(
								$fieldKey.'_field',
								$fieldTitle,
								array(&$this,'field_input'),
								//You should pass the page passed to add_settings_section
								$this->args['menu_slug'],
								'anony_'.$secKey.'_section',
								$field
							);

					}

				}
			}
		}
		
		/*
		 * class settings sections callback function
		 */
		public function section_cb($section){
			
			$id = preg_match('/anony_(.*)_section/', $section['id'], $m);
			
			$id = $m[1];
			
			if(isset($this->sections[$id]['note'])){
				echo '<p class=anony-section-warning>'.$this->sections[$id]['note'].'<p>';
			}	
		}
		
		/*
		 * class section fields callback function
		 */
		public function field_input($field){

			if(isset($field['type'])){

				$field['option_name'] = $this->args['opt_name'];
				
				$render_field = new ANONY_Input_Field($field);

				echo $render_field->field_init();
				
			}
 		}
		
		/**
		 * Validation function
		 * @param  array  $notValidated  array of not validated options sent by options form
		 * return  array  $validated     array of form values after validation
		 */
		public function options_validate($notValidated){

			self::$called++;

			//Make sure this code runs once to prevent error messages duplication
			if(self::$called <= 1){
			
				$validated = array();
			
				foreach($this->sections as $secKey => $section){
					
					if(isset($section['fields'])){
						
						foreach($section['fields'] as $fieldKey => $field){
							
							$fieldID = $field['id'];
															
							//Current value in database
							$currentValue = $this->options->$fieldID;
							
							//Something like a checkbox is not set if unchecked
							if(!isset($notValidated[$fieldID])) {
								$this->options->delete_option($fieldID);
								continue;
							}
							
							if($currentValue === $notValidated[$fieldID]) {
									
								$validated[$fieldID] = $currentValue;

								continue;
							}
							
							//Check if validation required
							if(isset($field['validate'])){
									
								$args = array(
											'field'     => $field,
											'new_value' => $notValidated[$fieldID],
										);
								
								$this->validate = new ANONY_Validate_Inputs($args);
								
								//Add to errors if not valid
								if(!empty($this->validate->errors)){

									$this->errors =  array_merge((array)$this->errors, (array)$this->validate->errors);

									continue;//We will not add to $validated 
								}
								
								$validated[$fieldID] = $this->validate->value;
								
							}else{

								$validated[$fieldID] = $notValidated[$fieldID];
							}
						}
						
					}
				}
				if(!empty($this->errors)){

					// add settings saved message with the class of "updated"
					add_settings_error( 
						$this->args['opt_name'], 
						esc_attr( $this->args['opt_name'] ), 
						esc_html__('Options are saved except those with the following errors', ANOE_TEXTDOM ), 
						'error' 
					);

					foreach($this->errors as $field_id => $data){
						
						add_settings_error( 
							$this->args['opt_name'], 
							esc_attr( $field_id ), 
							$this->validate->get_error_msg($data['code'], $data['title']), 
							'error'
						);

					}

				}else{
					// add settings saved message with the class of "updated"
					add_settings_error( 
						$this->args['opt_name'], 
						esc_attr( $this->args['opt_name'].'_updated' ), 
						esc_html__('Options saved', ANOE_TEXTDOM ), 
						'updated' 
					);
				}

				return $validated;
			}
			
			
		}
		
		/**
		 * HTML OUTPUT.
		 */
		public function options_page_html(){
			if(!isset($_GET['page']) || $_GET['page'] != $this->args['opt_name']) return;

			// check user capabilities
			if ( ! current_user_can( 'manage_options' ) ) return;?>

			<div id="anony-options-wrapper">
				<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

				<form action="options.php" method="post" enctype="multipart/form-data" autocomplete="off">

				<?php
				// output security fields for the registered setting
				settings_fields( $this->OptionGroup );
				if ( has_custom_logo() ) {
					$option_logo = get_custom_logo();
				}else{
					$option_logo = '<img src="'.ANONY_OPTIONS_URI.'/imgs/logo-orange.png"/>';
				}
			
				echo '<div id="options-wrap"><div id="anony-options-nav"><div id="anony-logo">'.$option_logo.'</div><ul>';
			
					foreach($this->menu as $nav => $details){
						if(isset($details['sections'])){
								echo '<li><div><a id="'.$nav.'-nav" href="#"  class="anony-nav-item nav-toggle" role="'.$nav.'">'.$details['title'].'<span class="icon" data-icon="y"></a></div>';
								echo '<ul id="'.$nav.'-dropdown" class="anony-dropdown">';

									foreach($details['sections'] as $sec){
										echo '<li class="anony-nav-item"><a id="'.$sec.'" href="#anony-section/'.$sec.'" class="anony-nav-link">'.(isset($this->sections[$sec]) ? $this->sections[$sec]['title'] : ucfirst(str_replace('-', ' ', $sec))).'</a><span class="icon" data-icon="'.$this->sections[$sec]['icon'].'"></span></li>';
									}

								echo '</ul></li>';

							 }else{
								echo '<li class="anony-nav-item"><a id="'.$nav.'" href="#anony-section/'.$nav.'" class="anony-nav-link">'.(isset($this->sections[$nav]) ? $this->sections[$nav]['title'] : ucfirst(str_replace('-', ' ', $nav))).'</a><span class="icon" data-icon="'.$this->sections[$nav]['icon'].'"></span></li>';	

							}
						}
					

					echo '</ul></div><div id="options-sections">';
					/**
					 * output setting sections and their fields
					 */ 
					ANONY_OPTS_HELP::DoSettingsSections($this->args['menu_slug']);

					submit_button( 'Save Settings', 'primary', 'submit', true, [ 'role' => 'anony-options']  );

					echo "</div></div>";

				?>

				</form>
			</div>
		<?php
		}
		
		/**
		 * Page scripts registration.
		 */		
		public function page_scripts(){
			if(isset($_GET['page']) && $_GET['page'] == $this->args['opt_name']){
				
				wp_enqueue_style( 'select2', ANONY_FIELDS_URI.'select2/css/select2.min.css' );

				wp_register_style( 'anony-options-css', ANONY_OPTIONS_URI.'css/options.css', array('farbtastic'), time(), 'all');	

				wp_enqueue_style( 'anony-options-css' );

				if(is_rtl()){
					wp_register_style( 'anony-options-rtl-css', ANONY_OPTIONS_URI.'css/options-rtl.css', array(), time(), 'all');
					wp_enqueue_style( 'anony-options-rtl-css' );
				}

				if(!is_rtl()){
					$enGoogleFonts = array(
						'Gugi'  => 'https://fonts.googleapis.com/css?family=Gugi', 
						'Anton' => 'https://fonts.googleapis.com/css?family=Anton',
						'Exo'   => 'https://fonts.googleapis.com/css?family=Exo',
					);

					foreach($enGoogleFonts as $name => $link){
						wp_enqueue_style( $name, $link, array(), time(), 'all');
					}

				}

				wp_enqueue_script( 'anony-options-js', ANONY_OPTIONS_URI.'js/options.js', array('jquery', 'backbone'), time(), true);

				foreach($this->sections as $k => $section){

					if(isset($section['fields'])){

						foreach($section['fields'] as $fieldKey => $field){

							if(isset($field['type'])){

								$field_class = 'ANONY_optf__'.ucfirst($field['type']);

								if(class_exists($field_class) && method_exists($field_class, 'enqueue')){
									$enqueue = new $field_class('','',$this);
									$enqueue->enqueue();
								}

							}

						}

					}

				}
			}
		}
		
		/**
		 * Registers option related widgets
		 */
		public function register_widgets(){
			
			foreach($this->widgets as $widget){
				
				add_action('widgets_init', function () use($widget){
	
					register_widget($widget);

				});
				
			}
		}
		
		/**
		 * Adds styles related to some options in front end
		 */
		public function frontend_styles(){ ?>
			<style type="text/css">
				#anony-ads{
					display: flex;
					justify-content: center;
					align-items: center;
				}
			</style>
		<?php }
		
		/**
		 * Adds styles related to some options in admin
		 */
		public function admin_styles(){ 
			if(get_current_screen()->id == "appearance_page_".$this->args['opt_name']){?>
				<style type="text/css">
					#setting-error-<?php echo $this->args['opt_name'] ?>{
						background-color: #d1354b;
						color: #fff;
					}
					#setting-error-<?php echo $this->args['opt_name'] ?> .notice-dismiss, #setting-error-<?php echo $this->args['opt_name'] ?> .notice-dismiss:before{
						color: #fff;
					}
				</style>
			<?php }

			
		}

		/**
		 * Display settings errors
		 */
		public function admin_notices(){
	
			settings_errors($this->args['opt_name']);
		}
	}
}
?>