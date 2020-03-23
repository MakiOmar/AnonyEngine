<?php
/**
 * Metabox shortcode
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
 
 if (! class_exists( 'ANONY_Mb_Shortcode' )) {
 	class ANONY_Mb_Shortcode extends ANONY_Meta_Box{
 		/**
		 * Constructor
		 */ 
		public function __construct($parent, $meta_box = array()){
			$this->parent = $parent;

			$this->hooks();
		}

		/**
		 * Shortcode hooks
		 */
		public function hooks(){
			add_shortcode($this->parent->id_as_hook  , array($this, 'metaboxShortcode') );

			add_action( 'wp_enqueue_scripts', array($this, 'enqueueShortcodeScripts'));

			add_action( 'wp_footer', array($this, 'shortcodeFooterScripts') );
		}

		/**
		 * Adds a shortcode for the metabox
		 * @param  array  $atts An array of shortcode attributes
		 * @return string The shortcode output
		 */
		public function metaboxShortcode($atts){

			$render = '';

			/**
			 * This parameter $this->id_as_hook is the shortcode name to be used as filter.
			 * Filter name will be shortcode_atts_{$shortcode}
			 */

			$atts = shortcode_atts(
						[], 
						$atts, 
						$this->parent->id_as_hook );

			if (isset($_GET['action']) && $_GET['action'] == 'insert' && isset($_GET['_wpnonce']) && wp_verify_nonce( $_GET['_wpnonce'] , 'anonyinsert' ) ) {

				$this->insertPostInFront();

				do_action($this->parent->id_as_hook.'_before_form');

				$render .= '<form method="post">';

				/**
				 * Usefull when neccessary hidden inputs are needed for the form
				 */ 
				$hiddens = apply_filters( $this->parent->id_as_hook.'_shortcode_hiddens' , '', $atts );


				$render .= $hiddens;

				/**
				 * Start metabox render
				 */ 

				$render .= $this->parent->returnMetaFields();

				$render .= '<input name="save" type="submit" class="button button-primary button-large" id="publish" value="'.esc_html__( 'Save changes' ).'">';

				$render .= '</form>';

				do_action($this->parent->id_as_hook.'_after_form');
			}

			

			return $render;
		}

		public function insertPostInFront(){
			/**
			 * Check if there are any posted data return if empty
			 */ 
			if (empty($_POST)) return;

			if(!isset($_POST['postType'])) return;

			if(is_array($this->parent->post_type) && !in_array($_POST['postType'], $this->parent->post_type)) return;

			if( !is_array($this->parent->post_type) && $_POST['postType'] !== $this->parent->post_type) return;

			if(!isset($_POST['post_title']) ||empty('post_title')) return;
					
			if (isset($_POST[$this->parent->id.'_nonce']) && !wp_verify_nonce( $_POST[$this->parent->id.'_nonce'], $this->parent->id.'_action' )) return;


			if (isset($_POST['parent_id']) && !empty($_POST['parent_id'])) {
				$post_parent = intval($_POST['parent_id']);

				$post_parent_data = get_post($post_parent);

				$set_parent_meta = ($post_parent_data instanceof WP_Post) ? true : false;

			}

			//Can be used to validate $_POST data befoore insertion
			do_action( $this->parent->id_as_hook.'_before_insert' );
			/**
			 * wp_insert_post() passes data through sanitize_post(), 
			 * which itself handles all necessary sanitization and validation (kses, etc.).
			 */
			$title = wp_strip_all_tags( $_POST['post_title'] );

			global $wpdb;
			$post_id = $wpdb->get_col("select ID from $wpdb->posts where post_title LIKE '".$title."%' ");



			if(!empty($post_id)) {
				//esc_html_e( 'Sorry! but you already have posted the same data before', ANOE_TEXTDOM  );
				$this->parent->startUpdate($_POST, intval($post_id[0]));

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

				    do_action( $this->parent->id_as_hook.'_after_insert' );

				    if (isset($set_parent_meta) && $set_parent_meta) {
				    	$test = update_post_meta( $insert, 'parent_id',  $post_parent);
				    }

				    $this->parent->startUpdate($_POST, $insert);

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

		public function enqueueShortcodeScripts(){
			global $post;

			if(ANONY_POST_HELP::isPageHasShortcode($post, $this->parent->id_as_hook)){
				$this->parent->frontScripts();
			}
		}

		/**
		 * Load footer scripts if page has metabox shortcode
		 */
		public function shortcodeFooterScripts () {
			global $post;

			if(ANONY_POST_HELP::isPageHasShortcode($post, $this->parent->id_as_hook)){
				$this->parent->footerScripts();
			}
		    
		}
 	}
 }
