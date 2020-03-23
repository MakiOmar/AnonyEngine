<?php
/**
 * Metabox for frontend single post
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
 
 if (! class_exists( 'ANONY_Mb_Single' )) {
 	class ANONY_Mb_Single extends ANONY_Meta_Box{

 		/**
		 * Constructor
		 */ 
		public function __construct($parent, $meta_box = array()){

			$this->parent = $parent;

			$this->hooks();
		}

		public function hooks(){
			add_action( 'wp_enqueue_scripts', array($this->parent, 'frontScripts'));

			add_action( 'wp_head', array($this, 'headStyles'));

			add_filter( 'the_content', array($this, 'showOnFront') );

			add_action( 'wp_footer', array($this, 'singleFooterScripts') );

			add_filter( 'anony_mb_loc_scripts', array($this, 'localizeScripts'));
		}

		/**
		 * Renders metabox in frontend. hooked to the_content filter
		 * @param  string $content 
		 * @return string
		 */
		public function showOnFront($content){

			global $post;

			if (!is_single() || !in_array($post->post_type, $this->parent->post_type) || (!is_array($this->parent->post_type) && $post->post_type !== $this->parent->post_type) ) return $content;

			$this->updatePost();

			$render = $this->parent->getNotices();

			do_action($this->parent->id_as_hook.'_show_on_front');


			$render .= $this->renderForAction();

			return $content.'<br/>'.$render;	
				
		}


		/**
		 * Render frontend form according to action
		 * @return type
		 */
		public function renderForAction(){
			global $post;

			if (!is_user_logged_in() && !is_admin()) {
				return esc_html__( 'Sorry, you have to login first', ANOE_TEXTDOM  );
			}
				
			if (isset($_GET['action']) && isset($_GET['_wpnonce']) && wp_verify_nonce( $_GET['_wpnonce'], 'anonyinsert_'.$post->ID ) && get_current_user_id() == $post->post_author) {


					$render = $this->parent->renderFrontendForm();

			}else{
				$render = $this->parent->returnMetaFields();

				$render .= sprintf('<a href="%1$s?action=edit&_wpnonce=%2$s" class="button button-primary button-large">%3$s</a>', get_permalink( ) ,wp_create_nonce( 'anonyinsert_'.$post->ID ), esc_html__( 'Edit' ));
			}
				

			return $render;
		}

		/**
		 * Update metafields if updated from frontend. (in a single post)
		 */
		public function updatePost(){
			/**
			 * Check if there are any posted data return if empty
			 */ 
			if (empty($_POST)) return;

			global $post;

			if ($post->post_type !== $_POST['postType']) return;

			if(($post->post_author != $_POST['user_ID']) || !current_user_can( 'administrator' ) ) return;

			if($post->ID != $_POST['post_ID'] ) return;

			if (!isset($_POST[$this->parent->id.'_nonce'])) return;
			
					
			if (isset($_POST[$this->parent->id.'_nonce']) && !wp_verify_nonce( $_POST[$this->parent->id.'_nonce'], $this->parent->id.'_action' )) return;


			//Can be used to validate $_POST data befoore insertion
			do_action( $this->parent->id_as_hook.'_before_update' );

			$this->parent->startUpdate($_POST, $_POST['post_ID']);
				
		}

		public function singleFooterScripts(){
			
			if (is_single()) {

				$post_type = get_post_type();
				if( in_array( $post_type, $this->parent->post_type) || $post_type == $this->parent->post_type ){
					$this->parent->footerScripts();
				}
				
			}

						
		}

		/**
		 * Add style tag to head
		 * @return type
		 */
		public function headStyles(){
			if (is_single( ) && !is_admin()) {?>
				<style type="text/css">
					#anony_map{
						width:100%;
						height: 480px;
					}
				</style>
			<?php }
		}

		/**
		 * Filter metabox localize scripts
		 * @param  array $locScripts 
		 * @return array
		 */
		public function localizeScripts($locScripts){
			if (is_single()) {
				$post_id = get_the_ID();
		        $anony__entry_lat  = get_post_meta($post_id,'anony__entry_lat',true);
		        $anony__entry_long = get_post_meta($post_id,'anony__entry_long',true);

		        if(!empty($anony__entry_lat) && !empty($anony__entry_long)){

		        	$locScripts['geolat'] = $anony__entry_lat;
					$locScripts['geolong']= $anony__entry_long;
		        }
			}

			return $locScripts;
				
		}

 	}
 }