<?php
/**
 * Metabox for admin
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
 
 if (! class_exists( 'ANONY_Mb_Admin' )) {
 	class ANONY_Mb_Admin extends ANONY_Meta_Box{

 		/**
		 * Constructor
		 */ 
		public function __construct($parent, $meta_box = array()){

			$this->parent = $parent;

			$this->hooks();
		}

		/**
		 * Add metabox hooks.
		 */
		public function hooks(){
			
			add_action( 'admin_head', array($this, 'headStyles'));

			add_action( 'admin_enqueue_scripts', array($this, 'adminEnqueueScripts'));

			add_action( 'add_meta_boxes' , array( $this, 'addMetaBox' ), $this->hook_priority, 2 );
			
			add_action( 'post_updated', array($this, 'updatePostMeta'));
			
			add_action( 'admin_notices', array($this, 'adminNotices') );

			add_action( 'admin_footer', array($this, 'adminFooterScripts') );	
	
		}

		/**
		 * Add metaboxes.
		 */
		public function addMetaBox($postType, $post){
			if( is_array( $this->parent->post_type ) && in_array($postType, $this->parent->post_type) ){

			$mbID = $this->parent->id;

			$this_post_metaboxes = apply_filters( 'anony_post_specific_metaboxes', $this->parent->metabox, $post );

			$this_post_metaboxes['id'] = $mbID;

			if (!empty($this_post_metaboxes) && (in_array($post->post_type, $this_post_metaboxes['post_type']) || $this_post_metaboxes['post_type'] === $post->post_type)) {

				$this->parent->setMetaboxData($this_post_metaboxes);
			}
				
				foreach ( $this->parent->post_type as $post_type ) {
					add_meta_box( $this->parent->id, $this->parent->label, array( $this->parent, 'metaFieldsCallback' ), $post_type, $this->parent->context, $this->parent->priority );
				}
				
			}elseif($this->parent->post_type == $postType){
				
				add_meta_box( $this->parent->id, $this->parent->label, array( $this->parent, 'metaFieldsCallback' ), $this->parent->post_type, $this->parent->context, $this->parent->priority );
				
			}
		}

		/**
		 * Update metabox inputs in database.
		 * 
		 * For admin side
		 */
		public function updatePostMeta($post_ID){
			if(!in_array(get_post_type($post_ID), $this->parent->post_type)) return;
				
			if ( ! current_user_can( 'edit_post', $post_ID )) return;
			
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
			
			if( ( wp_is_post_revision( $post_ID) || wp_is_post_autosave( $post_ID ) ) ) return;

			$parent_id = get_post_meta( $post_ID, 'parent_id', true );

			//Sometime we change metaboxes through a hook, but still an object of this class holds the old metabox data, which will always make the nonce check fails. So we will check if this metabox has changed and get the new id if it has.
			if(!empty($parent_id)){
				$metaboxes =  get_post_meta( intval($parent_id), 'anony_this_project_metaboxes', true );
				if (!empty($metaboxes) && is_array($metaboxes) && isset($metaboxes['id']) && !empty($metaboxes['id'])) {
					$this->parent->id = $metaboxes['id'];
				}
			}

			//To avoid undefined index for other meta boxes
			if(!isset($_POST[$this->parent->id.'_nonce'])) return;

			//One nonce for a metabox
			if (!wp_verify_nonce( $_POST[$this->parent->id.'_nonce'], $this->parent->id.'_action' )) return;

			$this->parent->startUpdate($_POST, $post_ID);
			
		}

		/**
		 * Show error messages in admin side
		 */
		public function adminNotices(){
			$screen = get_current_screen();

			if ($screen->base == 'post' && (in_array($screen->post_type, $this->parent->post_type) || $screen->post_type == $this->parent->post_type))
			{
				$this->parent->notices();	
			} 
		}

		/**
		 * Enqueue scripts for admin side
		 * @return void
		 */
		public function adminEnqueueScripts(){
			$this->parent->enqueueMainScripts();
		}

		/**
		 * loads footer scripts
		 * @return type
		 */
		public function adminFooterScripts(){
			$screen = get_current_screen();
			if( $screen->base == 'post'  &&  (in_array( $screen->post_type, $this->parent->post_type) || $screen->post_type == $this->parent->post_type)){
				$this->parent->footerScripts();
			}
			
		}

		/**
		 * Add style tag to head
		 * @return type
		 */
		public function headStyles(){
			if (is_single( ) && is_admin()) {?>
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