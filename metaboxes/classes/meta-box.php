<?php

if( ! class_exists( 'ANONY_Meta_Box' )){
	
	class ANONY_Meta_Box{
		/**
		 * @var array Array of input fields errors. array('field_id' => 'error')
		 */
		public $errors = array();
		
		/**
		 * @var string metabox's ID
		 */
		public $id;
		
		/**
		 * @var string metabox's label
		 */
		public $label;
		
		/**
		 * @var string metabox's context. side|normal|advanced
		 */
		public $context;
		
		/**
		 * @var string metabox's priority. High|low
		 */
		public $priority;
		
		/**
		 * @var int metabox's hook priority. Default 10
		 */
		public $hook_priority = 10;
		
		/**
		 * @var string|array metabox's post types.
		 */
		public $post_type;
		
		/**
		 * @var array metabox's fields array.
		 */
		public $fields;

		/**
		 * @var array metabox's localize scripts.
		 */
		public $localize_scripts;
		
		/**
		 * @var object inputs validation object.
		 */
		private $validate;
		
		/**
		 * Constructor
		 */ 
		public function __construct($meta_box = array()){

			if(empty($meta_box) || !is_array($meta_box)) return;

			$this->localize_scripts = array(
    			'ajaxURL'         => function_exists('anony_get_ajax_url') ? anony_get_ajax_url() : admin_url( 'admin-ajax.php' ),
    			'textDir'         => (is_rtl() ? 'rtl' : 'ltr'),
    			'themeLang'       => get_bloginfo('language'),
    			'MbUri'      => ANONY_MB_URI,
    			'MbPath'     => ANONY_MB_PATH,
    			
    		);
			
			//Set metabox's data
			$this->set_metabox_data($meta_box);
			
			//add metabox needed hooks
			$this->hooks();
			
		}
		
		/**
		 * Set metabox properties.
		 * @param array $meta_box Array of meta box data
		 * @return void
		 */
		public function set_metabox_data($meta_box){
			
			$this->id            = $meta_box['id'];
			$this->label         = $meta_box['title'];
			$this->context       = $meta_box['context'];
			$this->priority      = $meta_box['priority'];
			$this->hook_priority = isset($meta_box['hook_priority']) ? $meta_box['hook_priority'] : $this->hook_priority;
			$this->post_type     = $meta_box['post_type'];
			$this->fields        = $meta_box['fields'];

			//To use id for hooks definitions
			$this->id_as_hook    = str_replace('-', '_', $this->id);
		}

		/**
		 * Add metabox hooks.
		 */
		public function hooks(){
			add_shortcode($this->id_as_hook  , array($this, 'metabox_shortcode') );
			
			if(is_admin()){
				add_action( 'admin_head', array(&$this, 'head_styles'));

				add_action( 'admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));

				add_action( 'add_meta_boxes' , array( &$this, 'add_meta_box' ), $this->hook_priority, 2 );
				
				add_action( 'post_updated', array(&$this, 'update_post_meta'));
				
				add_action( 'admin_notices', array(&$this, 'admin_notices') );
			}else{
				add_action( 'wp_enqueue_scripts', array(&$this, 'wp_enqueue_scripts'));

				add_action( 'wp_head', array(&$this, 'head_styles'));

				add_filter( 'the_content', array(&$this, 'show_on_front') );
			}	
	
		}

		/**
		 * Adds a shortcode for the metabox
		 * @param  array  $atts An array of shortcode attributes
		 * @return string The shortcode output
		 */
		public function metabox_shortcode($atts){

			$this->insert_post_in_frontend();

			do_action($this->id_as_hook.'_before_form');

			/**
			 * This parameter $this->id_as_hook is the shortcode name to be used as filter.
			 * Filter name will be shortcode_atts_{$shortcode}
			 */

			$atts = shortcode_atts(
						[], 
						$atts, 
						$this->id_as_hook );


			$render = '<form method="post">';

			/**
			 * Usefull when neccessary hidden inputs are needed for the form
			 */ 
			$hiddens = apply_filters( $this->id_as_hook.'_shortcode_hiddens' , '', $atts );


			$render .= $hiddens;

			/**
			 * Start metabox render
			 */ 
			ob_start();
				
				$this->meta_fields_callback();

				$render .= ob_get_contents();

				$render .= '<input name="save" type="submit" class="button button-primary button-large" id="publish" value="'.esc_html__( 'Save changes' ).'">';

				$render .= '</form>';

			ob_end_clean();

			do_action($this->id_as_hook.'_after_form');

			return $render;
		}

		/**
		 * Renders metabox in front end. hooked to the_content filter
		 * @param  string $content 
		 * @return string
		 */
		public function show_on_front($content){
			if (!is_user_logged_in() && is_single() && !is_admin()) {
				esc_html_e( 'Sorry, you have to login first', ANOE_TEXTDOM  );
			}

			global $post;

			$render = '';

			if ( is_single() && in_array($post->post_type, $this->post_type) ) {
				

				$this->update_post_in_frontend();

				$render .= '<form method="post">';

				$render .= apply_filters( $this->id_as_hook.'_hiddens' , '');

				ob_start();

				$this->meta_fields_callback();

				$render .= ob_get_contents();


				$render .= '<input name="save" type="submit" class="button button-primary button-large" id="publish" value="'.esc_html__( 'Save changes' ).'">';

				$render .= '</form>';

				ob_end_clean();

				return $content.'<br/>'.$render;
			}
				
			return $content;	
		}
		/**
		 * Add metaboxes.
		 */
		public function add_meta_box($postType, $post){

			$this_post_metaboxes = apply_filters( 'anony_post_specific_metaboxes', '', $post );

			

			if (!empty($this_post_metaboxes) && (in_array($post->post_type, $this_post_metaboxes['post_type']) || $this_post_metaboxes['post_type'] === $post->post_type)) {
				$this->id            = $this_post_metaboxes['id'];
				$this->label         = $this_post_metaboxes['title'];
				$this->context       = $this_post_metaboxes['context'];
				$this->priority      = $this_post_metaboxes['priority'];
				$this->hook_priority = isset($this_post_metaboxes['hook_priority']) ? $this_post_metaboxes['hook_priority'] : $this->hook_priority;
				$this->post_type     = $this_post_metaboxes['post_type'];
				$this->fields        = $this_post_metaboxes['fields'];

				//To use id for hooks definitions
				$this->id_as_hook    = str_replace('-', '_', $this->id);
			}
			
			if( is_array( $this->post_type ) && in_array($postType, $this->post_type) ){
				
				foreach ( $this->post_type as $post_type ) {
					add_meta_box( $this->id, $this->label, array( $this, 'meta_fields_callback' ), $post_type, $this->context, $this->priority );
				}
				
			}elseif($this->post_type == $postType){
				
				add_meta_box( $this->id, $this->label, array( $this, 'meta_fields_callback' ), $this->post_type, $this->context, $this->priority );
				
			}
		}
		
		/**
		 * Render metabox' fields.
		 */
		public function meta_fields_callback(){

			if(!class_exists('ANONY_Input_Field')){
						esc_html_e( 'Input fields plugin is required', ANOE_TEXTDOM );
						return;
			}
			global $post;


			$pID = isset($_GET['post']) && !empty($_GET['post']) ? intval($_GET['post']) : $post->ID;


			$this->fields = apply_filters( 'anony_mb_frontend_fields', $this->fields);
			
			wp_nonce_field( $this->id.'_action', $this->id.'_nonce', false );

			//Array of inputs that have same HTML markup
			$mixed_types = ['text','number','email', 'password','url'];
			
			//Loop through inputs to render
			foreach($this->fields as $field){
				if (!is_admin() && (!isset($field['show_on_front']) || !$field['show_on_front']) ) continue;
				$array = [
						'date_time', 
						'upload',
						'tabs',
						'color', 
						'color_farbtastic',
						'color_gradient_farbtastic',
						'color_gradient', 
						'font_select',
						'info',
						'text',
						'hidden',
						'multi_text',
						'multi_value',
						'div',
						'select',
						'number',
						'checkbox',
						'radio',
						'radio_img',
					];
				if(in_array($field['type'], $array)){
						
						$render_field = new ANONY_Input_Field($field, 'meta', $pID);
					
						echo $render_field->field_init();

						if(isset($field['scripts']) && !empty($field['scripts'])){

					        foreach($field['scripts'] as $script){

					            $deps = (isset($script['dependancies']) && !empty($script['dependancies'])) ? $script['dependancies'] : [];

					            $deps[] = 'anony-metaboxs';

					            if(isset($script['file_name'])){

					                $url = ANONY_MB_URI. 'assets/js/'.$script['file_name'].'.js';

					            }elseif(isset($script['url'])){

					                $url = $script['url'];
					            }
					            
					            wp_enqueue_script($script['handle'], $url, $deps, false, true);
					        }
					    }
					
				}
			
					
			}
		}
		
		public function insert_post_in_frontend(){
			/**
			 * Check if there are any posted data return if empty
			 */ 
			if (empty($_POST)) return;

			if(!isset($_POST['postType'])) return;

			if(is_array($this->post_type) && !in_array($_POST['postType'], $this->post_type)) return;

			if( !is_array($this->post_type) && $_POST['postType'] !== $this->post_type) return;

			if(!isset($_POST['post_title']) ||empty('post_title')) return;
					
			if (isset($_POST[$this->id.'_nonce']) && !wp_verify_nonce( $_POST[$this->id.'_nonce'], $this->id.'_action' )) return;


			if (isset($_POST['parent_id']) && !empty($_POST['parent_id'])) {
				$post_parent = intval($_POST['parent_id']);

				$post_parent_data = get_post($post_parent);

				$set_parent_meta = ($post_parent_data instanceof WP_Post) ? true : false;

			}

			

				//Can be used to validate $_POST data befoore insertion
				do_action( $this->id_as_hook.'_before_insert' );
				/**
				 * wp_insert_post() passes data through sanitize_post(), 
				 * which itself handles all necessary sanitization and validation (kses, etc.).
				 */
				$title = wp_strip_all_tags( $_POST['post_title'] );

				global $wpdb;
				$post_id = $wpdb->get_col("select ID from $wpdb->posts where post_title LIKE '".$title."%' ");



				if(!empty($post_id)) {
					//esc_html_e( 'Sorry! but you already have posted the same data before', ANOE_TEXTDOM  );
					$this->start_update($_POST, intval($post_id[0]));

					$url = add_query_arg('post', $post_id[0], get_the_permalink($post_id[0]));

				}else{
					$insert = wp_insert_post( [
						'post_title'   => wp_strip_all_tags( $_POST['post_title'] ), //required
						'post_content' => '', //required
						'post_type'    => $_POST['postType'],
						'post_status'  => 'publish',
					] );

					if (!is_wp_error( $insert )) {

					    do_action( $this->id_as_hook.'_after_insert' );

					    if (isset($set_parent_meta) && $set_parent_meta) {
					    	update_post_meta( $insert, 'parent_id',  $post_parent);
					    }

					    $this->start_update($_POST, $insert);

					    $url = add_query_arg('post', $insert, get_the_permalink($insert)); 

					    $url = add_query_arg('insert', 'succeed', $url);   
					}else{

						$url = add_query_arg('insert', 'failed', get_the_permalink()); 
					}
				}

				if (isset($url)) {
					wp_redirect( $url );
					 exit();
				}
				
		}

		public function update_post_in_frontend(){
			/**
			 * Check if there are any posted data return if empty
			 */ 
			if (empty($_POST)) return;

			/**
			 * Metabox should be used within a single post. But
			 * Sometime may be used as a shortcode, which can be added to a singular page.
			 * So we do this check to be mede if it is a single post
			 */ 
			if (is_single()) {
				global $post;
				if (!in_array($post->post_type, $this->post_type) ) return;
			
			}
			
					
			if (isset($_POST[$this->id.'_nonce']) && !wp_verify_nonce( $_POST[$this->id.'_nonce'], $this->id.'_action' )) return;
			//Can be used to validate $_POST data befoore insertion
			do_action( $this->id_as_hook.'_before_update' );

			$this->start_update($_POST, $_POST['post_ID']);
				
		}

		/**
		 * Update metabox inputs in database.
		 */
		public function update_post_meta($post_ID){
				
			if ( ! current_user_can( 'edit_post', $post_ID )) return;
			
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE()) return;
			
			if( ( wp_is_post_revision( $post_ID) || wp_is_post_autosave( $post_ID ) ) ) return;

			//To avoid undefined index for other meta boxes
			if(!isset($_POST[$this->id.'_nonce'])) return;

			//One nonce for a metabox
			if (!wp_verify_nonce( $_POST[$this->id.'_nonce'], $this->id.'_action' )) return;

			$this->start_update($_POST, $post_ID);
			
		}

		public function start_update($sent_data, $post_ID){

			if(empty($sent_data) || !is_array($sent_data)) return;

			if (!isset($sent_data[$this->id.'_nonce'])) return;

			if (!wp_verify_nonce( $sent_data[$this->id.'_nonce'], $this->id.'_action' )) return;



			//Can be used to validate $_POST data before insertion
			do_action( $this->id_as_hook.'_before_meta_insert' );

			$postType = get_post_type( $post_ID );

			$this->fields = apply_filters( 'anony_mb_frontend_fields', $this->fields );
			foreach($this->fields as $field){

				$field_id   = $field['id'];

				$field_type = $field['type'];


				//Something like a checkbox is not set if unchecked
				if(!isset($_POST[$field_id])) {

					delete_post_meta( $post_ID, $field_id );
					continue;
				}

				$chech_meta = get_post_meta($post_ID , $field_id, true);

				if ($chech_meta === $_POST[$field_id]) continue;
				
				//If this field is an array of other fields values
				if(isset($field['fields'])){

					foreach ($field['fields'] as  $nested_field) {

						foreach ($_POST[$field_id] as $field_index => $posted_field) {

							foreach ($posted_field as $fieldID => $value) {
								if ($nested_field['id'] == $fieldID) {
									$args = array(
										'field'         => $nested_field,
										'new_value'     => $value,
									);

									$this->validate = new ANONY_Validate_Inputs($args);

									if(!empty($this->validate->errors)){
								
										$this->errors =  array_merge((array)$this->errors, (array)$this->validate->errors);

										$_POST[$field_id][$field_index][$fieldID] = (
											$chech_meta !== '' && 
											isset($chech_meta[$field_index][$nested_field['id']])
										) ? 
										
										$chech_meta[$field_index][$nested_field['id']] : ''; 

										continue;
									}

									$_POST[$field_id][$field_index][$fieldID] = $this->validate->value;
								}
							}

						}		
					}

					$args = array(
						'field'         => $field,
						'new_value'     => $_POST[$field_id],
					);
					$this->validate = new ANONY_Validate_Inputs($args);

					update_post_meta( $post_ID, $field_id, apply_filters( 'anony_nested_cf_validation', $this->validate->value ) );

				}else{
					$args = array(
							'field'            => $field,
							'new_value'     => $_POST[$field_id],
						);


					$this->validate = new ANONY_Validate_Inputs($args);

					if(!empty($this->validate->errors)){
					
						$this->errors =  array_merge((array)$this->errors, (array)$this->validate->errors);

						continue;
					}
					
					$update_meta = update_post_meta( $post_ID, $field_id, apply_filters( 'anony_cf_validation', $this->validate->value ) );
				}


			}

			if(!empty($this->errors)) set_transient('ANONY_errors_'.$postType.'_'.$post_ID, $this->errors);	
			
		}
		
		/**
		 * Show error messages
		 */
		public function admin_notices(){
			if (isset($_GET['post'])) {

				$postType = get_post_type();

				$errors   = get_transient('ANONY_errors_'.$postType.'_'.$_GET['post']);
				
				if( $errors ){	

					$validator = new ANONY_Validate_Inputs();

					if($errors){

						foreach($errors as $field => $data){?>

							<div class="error <?php echo $field ?>">

								<p><?php echo $validator->get_error_msg($data['code'], $field);?>

							</div>


						<?php  }
					
						delete_transient('ANONY_errors_'.$postType.'_'.$_GET['post']);
					}

				}
			}
			
		}
		
		/**
		 * Enqueue needed scripts|styles
		 */
		public function enqueue_main_scripts(){		
        		
        	if(in_array( get_current_screen()->base , array('post') ) &&  in_array( get_current_screen()->post_type, $this->post_type)){
				wp_enqueue_style( 
					'anony-metaboxs' , 
					ANONY_MB_URI. 'assets/css/metaboxes.css', 
					false, 
					filemtime(wp_normalize_path(ANONY_MB_PATH . 'assets/css/metaboxes.css')) 
				);
				
				wp_enqueue_script( 
					'anony-metaboxs' , 
					ANONY_MB_URI. 'assets/js/metaboxes.js', 
					false, 
					filemtime(wp_normalize_path(ANONY_MB_PATH . 'assets/js/metaboxes.js')) 
				);
				$post_id = get_the_ID();
				$this->localize_scripts['geolat'] = get_post_meta($post_id,'anony__entry_lat',true);
				$this->localize_scripts['geolong']= get_post_meta($post_id,'anony__entry_long',true);

				//Don't remove outside the if statement
        		wp_localize_script( 'anony-metaboxs', 'AnonyMB', $this->localize_scripts );
			}
	
		}
		/**
		 * Load fields scripts on front if `also_on_front_scripts` is set to true
		 */
		public function enqueue_front_scripts(){
			wp_enqueue_script('metaboxes-front', ANONY_MB_URI. 'assets/js/metaboxes-front.js', ['jquery'], false, true);
			foreach($this->fields as $field){

				if(isset($field['scripts']) && !empty($field['scripts'])){
					if(isset($field['show_on_front']) && $field['show_on_front'] == true){
				        foreach($field['scripts'] as $script){

				            $deps = (isset($script['dependancies']) && !empty($script['dependancies'])) ? $script['dependancies'] : [];

				            if(isset($script['file_name'])){

				                $url = ANONY_MB_URI. 'assets/js/'.$script['file_name'].'.js';

				            }elseif(isset($script['url'])){

				                $url = $script['url'];
				            }
				            
				            wp_enqueue_script($script['handle'], $url, $deps, false, true);
				        }
				        $post_id = get_the_ID();
				        $anony__entry_lat  = get_post_meta($post_id,'anony__entry_lat',true);
				        $anony__entry_long = get_post_meta($post_id,'anony__entry_long',true);

				        if($anony__entry_lat && $anony__entry_long){

				        	$this->localize_scripts['geolat'] = $anony__entry_lat;
							$this->localize_scripts['geolong']= $anony__entry_long;
				        }
						
						wp_localize_script( 'metaboxes-front', 'AnonyMB', $this->localize_scripts );
				    }
				}
			}
		}

		/**
		 * Enqueue scripts for admin side
		 * @return void
		 */
		public function admin_enqueue_scripts(){
			$this->enqueue_main_scripts();
		}

		/**
		 * Enqueue scripts for frontend side
		 * @return void
		 */
		public function wp_enqueue_scripts(){
			$this->enqueue_front_scripts();
		}

		public function head_styles(){
			if (is_single( )) {?>
				<style type="text/css">
					#anony_map{
						width:100%;
						height: 480px;
					}
				</style>
			<?php }
		}
		
	}
}