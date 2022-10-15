<?php
/**
 * Register custom status for post type.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine.
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
if ( ! class_exists( 'ANONY_Register_Post_Status' ) ) {

	/**
	 * Register custom status for post type class.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine
	 * @author   Makiomar <info@makiomar.com>
	 * @license  https:// makiomar.com AnonyEngine Licence.
	 * @link     https:// makiomar.com/anonyengine.
	 */
	class ANONY_Register_Post_Status {

		/**
		 * Class instructor.
		 *
		 * @param string $status_name status's name.
		 * @param string $post_type Post's type's name.
		 */
		public function __construct( $status_name, $label, $post_type ) {

			$this->status_name = $status_name;
			$this->label       = $label;
			$this->post_type   = $post_type;

			add_action( 'init', array( $this, 'status_creation' ) );
			add_action( 'admin_footer-edit.php', array( $this, 'add_in_quick_edit' ) );
			add_filter( 'display_post_states', array( $this, 'display_post_state' ) );
			add_action( 'admin_footer-post.php', array( $this, 'selected' ) );
		}

		/**
		 * Create status
		 */
		public function status_creation() {
			register_post_status(
				$this->status_name,
				array(
					'label'                     => $this->label ,
					// Translators: Label count.
					'label_count'               => _n_noop( $this->label  . ' <span class="count">(%s)</span>', $this->label  . ' <span class="count">(%s)</span>'),
					'public'                    => true,
					'exclude_from_search'       => true,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
				)
			);
		}

		/**
		 * Add to post status dropdown.
		 */
		public function add_to_post_status_dropdown() {
			global $post;
			if ( $this->post_type !== $post->post_type ) {
				return false;
			}

			$status = ( $post->post_status === $this->status_name ) ? "jQuery( '#post-status-display' ).text( '" . $this->label  . "' );
			jQuery( 'select[name=\"post_status\"]' ).val('" . $this->status_name . "');" : '';

			echo "<script>
			jQuery(document).ready( function() {
			jQuery( 'select[name=\"post_status\"]' ).append( '<option value=\"" . $this->status_name . '">' . $this->label  . "</option>' );
			" . $status . '
			});
			</script>';
		}

		/**
		 * Add in quick edit
		 */
		public function add_in_quick_edit() {
			global $post;
			if ( $this->post_type !== $post->post_type ) {
				return false;
			}
			echo "<script>
			jQuery(document).ready( function() {
			jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"" . $this->status_name . '">' . $this->label  . "</option>' );
			});
			</script>";
		}

		/**
		 * Display post's state
		 *
		 * @param string $states Post's state.
		 *
		 * @return string Post's state.
		 */
		public function display_post_state( $states ) {
			global $post;
			$arg = get_query_var( 'post_status' );
			if ( $arg !== $this->status_name ) {

				if ( $post->post_status === $this->status_name ) {

					echo "<script>
					jQuery(document).ready( function() {
					jQuery( '#post-status-display' ).text( '" . $this->label  . "' );
					});
					</script>";

					return array( $this->label  );
				}
			}
			return $states;
		}

		/**
		 * Select state
		 */
		public function selected() {

			global $post;
			$complete = '';
			$label    = '';

			if ( $post->post_type === $this->post_type ) {

				if ( $post->post_status === $this->status_name ) {
					$complete = ' selected=\"selected\"';
					$label    = $this->label ;
				}
				$uppercase = $this->label ;

				ob_start();?>
					jQuery(document).ready(function($){
					$("select#post_status").append("<option value='<?php echo $this->status_name; ?>' <?php echo $complete; ?>><?php echo $uppercase; ?></option>");
					   
					if( '<?php echo $post->post_status; ?>' === '<?php echo $this->status_name; ?>' ){
						$("span#post-status-display").html("<?php echo $label; ?>");
						$("input#save-post").val("Save <?php echo $uppercase; ?>");
					}
					var jSelect = $("select#post_status");

					$("a.save-post-status").on("click", function(){

						if( jSelect.val() === '<?php echo $this->status_name; ?>' ){

							$("input#save-post").val("Save <?php echo $uppercase; ?>");
						}
					});
					});
				<?php

				$script = ob_get_clean();

				echo '<script type="text/javascript">' . $script . '</script>';
			}

		}

	}

}
