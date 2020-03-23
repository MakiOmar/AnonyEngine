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
    			'ajaxURL'   => ANONY_WPML_HELP:: getAjaxUrl(),
    			'textDir'   => (is_rtl() ? 'rtl' : 'ltr'),
    			'themeLang' => get_bloginfo('language'),
    			'MbUri'     => ANONY_MB_URI,
    			'MbPath'    => ANONY_MB_PATH,
    			
    		);
			
			//Set metabox's data
			$this->setMetaboxData($meta_box);
			
			new ANONY_Mb_Shortcode($this, $meta_box);

			new ANONY_Mb_Single($this, $meta_box);
			
			//add metabox needed hooks
			$this->hooks();

			
		}
		
		/**
		 * Set metabox properties.
		 * @param array $meta_box Array of meta box data
		 * @return void
		 */
		public function setMetaboxData($meta_box){
			
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
			
			add_action( 'admin_head', array(&$this, 'headStyles'));

			add_action( 'admin_enqueue_scripts', array(&$this, 'adminEnqueueScripts'));

			add_action( 'add_meta_boxes' , array( &$this, 'addMetaBox' ), $this->hook_priority, 2 );
			
			add_action( 'post_updated', array(&$this, 'updatePostMeta'));
			
			add_action( 'admin_notices', array(&$this, 'adminNotices') );

			add_action( 'admin_footer', array(&$this, 'wpFooter') );	
	
		}

		/**
		 * Add metaboxes.
		 */
		public function addMetaBox($postType, $post){

			$this_post_metaboxes = apply_filters( 'anony_post_specific_metaboxes', '', $post );
			

			if (!empty($this_post_metaboxes) && (in_array($post->post_type, $this_post_metaboxes['post_type']) || $this_post_metaboxes['post_type'] === $post->post_type)) {

				$this->setMetaboxData($this_post_metaboxes);
			}
			
			if( is_array( $this->post_type ) && in_array($postType, $this->post_type) ){
				
				foreach ( $this->post_type as $post_type ) {
					add_meta_box( $this->id, $this->label, array( $this, 'metaFieldsCallback' ), $post_type, $this->context, $this->priority );
				}
				
			}elseif($this->post_type == $postType){
				
				add_meta_box( $this->id, $this->label, array( $this, 'metaFieldsCallback' ), $this->post_type, $this->context, $this->priority );
				
			}
		}

		/**
		 * Update metabox inputs in database.
		 * 
		 * For admin side
		 */
		public function updatePostMeta($post_ID){
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

			$this->startUpdate($_POST, $post_ID);
			
		}

		
		public function renderFrontendForm(){

			global $post;

			$render = '<form method="post">';

			$render .= apply_filters( $this->id_as_hook.'_hiddens' , '');

			$render .= '<input type="hidden" id="post_type" name="postType" value="'.$post->post_type.'">';

			$render .= '<input type="hidden" id="user_ID" name="user_ID" value="'.get_current_user_id().'">';

			$render .= '<input type="hidden" id="post_ID" name="post_ID" value="'.$post->ID.'">';


			$render .= $this->returnMetaFields();

			$render .= '<input name="save" type="submit" class="button button-primary button-large" id="publish" value="'.esc_html__( 'Save changes' ).'">';

			$render .= '</form>';

			return $render;
		}

		/**
		 * Returns metabox fields
		 * @return string
		 */
		public function returnMetaFields(){
			ob_start();

			$this->metaFieldsCallback();

			$render = ob_get_contents();

			ob_end_clean();

			return $render;
		}
			
		/**
		 * Render metabox' fields.
		 */
		public function metaFieldsCallback(){

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

		/**
		 * Validate field value
		 * @param array $field     Field's data array
		 * @param mixed $new_value Field's new value
		 * @return object          Validation object
		 */
		public function validateField($field, $new_value){
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
		public function startUpdate($sent_data, $post_ID = null){
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

									$this->validate = $this->validateField($nested_field, $value);

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
					$this->validate = $this->validateField($field, $sent_data[$field['id']]);

				}else{

					$this->validate = $this->validateField($field, $sent_data[$field['id']]);

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
		public function adminNotices(){
			$screen = get_current_screen();

			if ($screen->base == 'post' && (in_array($screen->post_type, $this->post_type) || $screen->post_type == $this->post_type))
			{
				$this->notices();	
			} 
		}
		
		/**
		 * Enqueue needed scripts|styles
		 */
		public function enqueueMainScripts(){		
        		
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
		 * Enqueue scripts for admin side
		 * @return void
		 */
		public function adminEnqueueScripts(){
			$this->enqueueMainScripts();
		}

		/**
		 * Load fields scripts on front if `show_on_front` is set to true
		 */
		public function frontScripts(){

			wp_enqueue_script('metaboxes-front', ANONY_MB_URI. 'assets/js/metaboxes-front.js', ['jquery'], false, true);

			foreach($this->fields as $field){

				if(isset($field['scripts']) && !empty($field['scripts']) & isset($field['show_on_front']) && $field['show_on_front'] == true){
					
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

		public function headStyles(){
			if (is_single( )) {?>
				<style type="text/css">
					#anony_map{
						width:100%;
						height: 480px;
					}
				</style>
			<?php }
		}

		public function footerScripts(){

			?>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					'use strict';

					$('.meta-error').on('click', function(e){
						e.preventDefault();
						var obj = $.browser.webkit ? $('body') : $('html');
						var id = $(this).attr('rel-id');
						var elHeight = $('#fieldset_' + id).height();
						var paddingTop = parseInt($('#fieldset_' + id).css("padding-top"));
						var paddingBottom = parseInt($('#fieldset_' + id).css("padding-bottom"));

						var totalHeight = elHeight + paddingTop + paddingBottom;

						obj.animate(
								      {
								        scrollTop: $("#" + id).offset().top - totalHeight
								      },
								      1000 //speed
								    );
					});

					$('.meta-error').each(function(){
						var metaFieldId = $(this).attr('rel-id');
						$('#fieldset_' + metaFieldId).css('border', '1px solid red');
					});
				});
			</script>
		<?php }

		public function wpFooter(){
			$loadFooterScripts = false;
			if(is_admin()){
				$screen = get_current_screen();
				if( $screen->base == 'post'  &&  (in_array( $screen->post_type, $this->post_type) || $screen->post_type == $this->post_type)){
					$loadFooterScripts = true;
				}
			}else{
				if (is_single()) {

					$post_type = get_post_type();
					if( in_array( $post_type, $this->post_type) || $post_type == $this->post_type ){
						$loadFooterScripts = true;
					}
					
				}
			}

			
			if($loadFooterScripts) $this->footerScripts();
			
		} 
	}
}