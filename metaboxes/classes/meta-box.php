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
			
			$this->id            = apply_filters( 'anony_mb_frontend_id', $meta_box['id'] );
			$this->label         = $meta_box['title'];
			$this->context       = $meta_box['context'];
			$this->priority      = $meta_box['priority'];
			$this->hook_priority = isset($meta_box['hook_priority']) ? $meta_box['hook_priority'] : $this->hook_priority;
			$this->post_type     = $meta_box['post_type'];
			$this->fields        = apply_filters( 'anony_mb_frontend_fields', $meta_box['fields'] );

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
		 * Add metaboxes.
		 */
		public function add_meta_box($postType, $post){

			$this_post_metaboxes = apply_filters( 'anony_post_specific_metaboxes', '', $post );
			

			if (!empty($this_post_metaboxes) && (in_array($post->post_type, $this_post_metaboxes['post_type']) || $this_post_metaboxes['post_type'] === $post->post_type)) {

				$this->set_metabox_data($this_post_metaboxes);
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
		 * Update metabox inputs in database.
		 */
		public function update_post_meta($post_ID){
			if(!in_array(get_post_type($post_ID), $this->post_type)) return;
				
			if ( ! current_user_can( 'edit_post', $post_ID )) return;
			
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE()) return;
			
			if( ( wp_is_post_revision( $post_ID) || wp_is_post_autosave( $post_ID ) ) ) return;

			$parent_id = get_post_meta( $post_ID, 'parent_id', true );

			//Sometime we change metaboxes through a hook, but still an object of this class holds the old metabox data, which will always make the nonce check fails. So we will check if this metabox has changed and get the new id if it has.
			if(!empty($parent_id)){
				$metaboxes =  get_post_meta( intval($parent_id), 'anony_this_project_metaboxes', true );
				if (!empty($metaboxes) && is_array($metaboxes) && isset($metaboxes['id']) && !empty($metaboxes['id'])) {
					$this->id = $metaboxes['id'];
				}
			}

			//To avoid undefined index for other meta boxes
			if(!isset($_POST[$this->id.'_nonce'])) return;

			//One nonce for a metabox
			if (!wp_verify_nonce( $_POST[$this->id.'_nonce'], $this->id.'_action' )) return;

			$this->start_update($_POST, $post_ID);
			
		}

		/**
		 * Adds a shortcode for the metabox
		 * @param  array  $atts An array of shortcode attributes
		 * @return string The shortcode output
		 */
		public function metabox_shortcode($atts){

			$render = '';

			/**
			 * This parameter $this->id_as_hook is the shortcode name to be used as filter.
			 * Filter name will be shortcode_atts_{$shortcode}
			 */

			$atts = shortcode_atts(
						[], 
						$atts, 
						$this->id_as_hook );

			if (isset($_GET['action']) && $_GET['action'] == 'insert' && isset($_GET['_wpnonce']) && wp_verify_nonce( $_GET['_wpnonce'] , 'anonyinsert' ) ) {

				$this->insert_post_in_frontend();

				do_action($this->id_as_hook.'_before_form');

				$render .= '<form method="post">';

				/**
				 * Usefull when neccessary hidden inputs are needed for the form
				 */ 
				$hiddens = apply_filters( $this->id_as_hook.'_shortcode_hiddens' , '', $atts );


				$render .= $hiddens;

				/**
				 * Start metabox render
				 */ 

				$render .= $this->return_meta_fields();

				$render .= '<input name="save" type="submit" class="button button-primary button-large" id="publish" value="'.esc_html__( 'Save changes' ).'">';

				$render .= '</form>';

				do_action($this->id_as_hook.'_after_form');
			}else{

				$render .= $this->return_meta_fields();
			}

			

			return $render;
		}
		public function render_frontend_form(){

			global $post;

			$render = '<form method="post">';

			$render .= apply_filters( $this->id_as_hook.'_hiddens' , '');

			$render .= '<input type="hidden" id="post_type" name="postType" value="'.$post->post_type.'">';

			$render .= '<input type="hidden" id="user_ID" name="user_ID" value="'.get_current_user_id().'">';

			$render .= '<input type="hidden" id="post_ID" name="post_ID" value="'.$post->ID.'">';


			$render .= $this->return_meta_fields();

			$render .= '<input name="save" type="submit" class="button button-primary button-large" id="publish" value="'.esc_html__( 'Save changes' ).'">';

			$render .= '</form>';

			return $render;
		}
		public function front_add(){
			global $post;
			$render = '';
			if (!is_user_logged_in() && is_single() && !is_admin()) {
				$render .= esc_html__( 'Sorry, you have to login first', ANOE_TEXTDOM  );
			}

			if ( is_single() && in_array($post->post_type, $this->post_type) ) {
				
				if (isset($_GET['action']) && isset($_GET['_wpnonce']) && wp_verify_nonce( $_GET['_wpnonce'], 'anonyinsert_'.$post->ID )) {

					switch ($_GET['action']) {
						case 'insert':
							$render .= $this->render_frontend_form();
							break;

						case 'edit':

							if(get_current_user_id() == $post->post_author){

								$render .= $this->render_frontend_form();
							}
						break;
						
						default:
							$render .= $this->return_meta_fields();

							$render .= sprintf('<a href="%1$s?action=edit&_wpnonce=%2$s" class="button button-primary button-large">%3$s</a>', get_permalink( ) ,wp_create_nonce( 'anonyinsert_'.$post->ID ), esc_html__( 'Edit' ));
							break;
					}

				}else{
					$render .= $this->return_meta_fields();

					$render .= sprintf('<a href="%1$s?action=edit&_wpnonce=%2$s" class="button button-primary button-large">%3$s</a>', get_permalink( ) ,wp_create_nonce( 'anonyinsert_'.$post->ID ), esc_html__( 'Edit' ));
				}
				
			}

			return $render;
		}
		/**
		 * Renders metabox in front end. hooked to the_content filter
		 * @param  string $content 
		 * @return string
		 */
		public function show_on_front($content){

			global $post;

			$render = '';

			if ( is_single() && in_array($post->post_type, $this->post_type) ) {

				$this->update_post_in_frontend();

				$render .= $this->getNotices();

				do_action($this->id_as_hook.'_show_on_front');


				$render .= $this->front_add();

				return $content.'<br/>'.$render;
			}
				
			return $content;	
		}

		/**
		 * Returns metabox fields
		 * @return string
		 */
		function return_meta_fields(){
			ob_start();

			$this->meta_fields_callback();

			$render = ob_get_contents();

			ob_end_clean();

			return $render;
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

			wp_nonce_field( $this->id.'_action', $this->id.'_nonce', false );
			
			//Loop through inputs to render
			foreach($this->fields as $field){
				if (!is_admin() && (!isset($field['show_on_front']) || !$field['show_on_front']) ) continue;
				
						
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
				$insert_args = [
					'post_title'   => wp_strip_all_tags( $_POST['post_title'] ), //required
					'post_content' => '', //required
					'post_type'    => $_POST['postType'],
					'post_status'  => 'publish',
				];


				if (isset($_POST['parent_id'])) $insert_args['post_parent'] = $_POST['parent_id'];

				$insert = wp_insert_post( $insert_args );

				if (!is_wp_error( $insert )) {

				    do_action( $this->id_as_hook.'_after_insert' );

				    if (isset($set_parent_meta) && $set_parent_meta) {
				    	$test = update_post_meta( $insert, 'parent_id',  $post_parent);
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

				if ($post->post_type != $_POST['postType']) return;

				if(($post->post_author != $_POST['user_ID']) || !current_user_can( 'administrator' ) ) return;

				if($post->ID != $_POST['post_ID'] ) return;
			
			}


			if (!isset($_POST[$this->id.'_nonce'])) return;
			
					
			if (isset($_POST[$this->id.'_nonce']) && !wp_verify_nonce( $_POST[$this->id.'_nonce'], $this->id.'_action' )) return;


			//Can be used to validate $_POST data befoore insertion
			do_action( $this->id_as_hook.'_before_update' );

			$this->start_update($_POST, $_POST['post_ID']);
				
		}

		/**
		 * Validate field value
		 * @param array $field     Field's data array
		 * @param mixed $new_value Field's new value
		 * @return object          Validation object
		 */
		public function validate_field($field, $new_value){
			$args = array(
						'field'         => $field,
						'new_value'     => $new_value,
					);
			$validate = new ANONY_Validate_Inputs($args);

			return $validate;
		}

		/**
		 * Updates post meta
		 * @param array $sent_data An Array of sent data. Upon POST Request
		 * @param int   $post_ID   Should be the id of being updated post
		 */
		public function start_update($sent_data, $post_ID = null){
			$postType = get_post_type( $post_ID );

			if(empty($sent_data) || !is_array($sent_data) || is_null($post_ID)) return;

			//Can be used to validate $sent_data data before insertion
			do_action( $this->id_as_hook.'_before_meta_insert' );

			foreach($this->fields as $field){

				if(!isset($sent_data[$field['id']])) continue;

				$chech_meta = get_post_meta($post_ID , $field['id'], true);

				if ($chech_meta === $sent_data[$field['id']]) continue;

				//If this field is an array of other fields values
				if(isset($field['fields'])){

					foreach ($field['fields'] as  $nested_field) {

						foreach ($sent_data[$field['id']] as $field_index => $posted_field) {

							foreach ($posted_field as $fieldID => $value) {

								if ($nested_field['id'] == $fieldID) {

									$this->validate = $this->validate_field($nested_field, $value);

									if(!empty($this->validate->errors)){
								
										$this->errors =  array_merge((array)$this->errors, (array)$this->validate->errors);

										$sent_data[$field_id][$field_index][$fieldID] = (
											$chech_meta !== '' && 
											isset($chech_meta[$field_index][$nested_field['id']])
										) ? 
										
										$chech_meta[$field_index][$nested_field['id']] : ''; 

										continue;
									}

									$sent_data[$field['id']][$field_index][$fieldID] = $this->validate->value;
								}
							}

						}		
					}

					//For now this deals with multi values, which have been already validated individually, so the only validation required is to remove all value are empty in one row.
					$this->validate = $this->validate_field($field, $sent_data[$field['id']]);

				}else{

					$this->validate = $this->validate_field($field, $sent_data[$field['id']]);

					if(!empty($this->validate->errors)){
					
						$this->errors =  array_merge((array)$this->errors, (array)$this->validate->errors);

						continue;
					}
					
				}

				update_post_meta( $post_ID, $field['id'], $this->validate->value);
			}

			if(!empty($this->errors)){
				set_transient('ANONY_errors_'.$postType.'_'.$post_ID, $this->errors);
			}
			
		}

		/**
		 * Return notices
		 * @return string
		 */
		public function getNotices(){
			ob_start();

			$this->notices();

			$render = ob_get_contents();

			ob_end_clean();

			return $render;
		}

		/**
		 * Render notices
		 * @return string
		 */
		public function notices(){
			global $post;

			$postType = get_post_type();

			if (is_single() && !in_array($postType, $this->post_type)) return;

			$errors   = get_transient('ANONY_errors_'.$postType.'_'.$post->ID);
			
			if( $errors ){	

				foreach($errors as $field => $data){?>

					<div class="error <?php echo $field ?>">

						<p><?php echo ANONY_Validate_Inputs::get_error_msg($data['code'], $field);?>

					</div>


				<?php  }
			
				delete_transient('ANONY_errors_'.$postType.'_'.$post->ID);
			}
		}
		
		/**
		 * Show error messages in admin side
		 */
		public function admin_notices(){
			if (isset($_GET['post'])) $this->notices();	
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