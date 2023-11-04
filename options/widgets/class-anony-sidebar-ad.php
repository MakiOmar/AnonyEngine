<?php
/**
 * Sidebar ADs widget class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

if ( ! class_exists( 'ANONY_Sidebar_Ad' ) ) {

	/**
	 * Sidebar ADs widget class
	 *
	 * @package Anonymous theme
	 * @author Makiomar
	 * @link http://makiomar.com
	 */
	class ANONY_Sidebar_Ad extends WP_Widget {

		/**
		 * Constructor
		 */
		public function __construct() {

			$parms = array(
				'description' => esc_html__( 'Displays the sidebar AD from theme options', 'anonyengine' ),
				'name'        => esc_html__( 'Sidebar ADs', 'anonyengine' ),
			);
			parent::__construct( 'ANONY_Sidebar_Ad', '', $parms );
		}
		/**
		 * Displays the form for this widget on the Widgets page of the WP Admin area.
		 *
		 * @param array $instance Widget instance array.
		 */
		public function form( $instance ) {
			?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'anonyengine' ); ?></label>

				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"  value="<?php echo ( isset( $instance['title'] ) && ! empty( $instance['title'] ) ) ? esc_attr( $instance['title'] ) : esc_attr__( 'ADs', 'anonyengine' ); ?>">

			</p>
			
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'anony_ad' ) ); ?>"><?php esc_html_e( 'AD:', 'anonyengine' ); ?></label><br/>
				<?php //phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<textarea rows="10" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'anony_ad' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'anony_ad' ) ); ?>"><?php echo ( isset( $anony_ad ) && ! empty( $instance['anony_ad'] ) ) ? ANONY_HELP::remove_tags_dom( $instance['anony_ad'], 'script', true ) : ''; ?></textarea>
				<?php //phpcs:enable ?>

			</p>

			<?php
		}

		/**
		 * Outputs the HTML for this widget.
		 *
		 * @param array $parms    Widget parameters.
		 * @param array $instance Widget instance.
		 */
		public function widget( $parms, $instance ) {
			//phpcs:disable

			$title = empty( $instance['title'] ) ? esc_html__( 'ADs', 'anonyengine' ) : $instance['title'];

			echo $instance['before_widget'];

			echo $instance['before_title'] . $instance['title'] . $instance['after_title'];

			echo '<div id="anony-ads">';
			echo ANONY_HELP::remove_tags_dom( $instance['anony_ad'], 'script', true );
			has_action( 'sidebar_ad' ) ? do_action( 'sidebar_ad' ) : '';
			echo '</div>';

			echo $instance['after_widget'];
			//phpcs:enable.
		}

		/**
		 * Deals with the settings when they are saved by the admin.
		 *
		 * @param  array $new_instance New instance.
		 * @param  array $old_instance Old instance.
		 * @return array New instance.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;

			$instance['title']    = wp_strip_all_tags( $new_instance['title'] );
			$instance['anony_ad'] = ANONY_HELP::remove_tags_dom( $new_instance['anony_ad'], 'script', true );

			return $instance;
		}
	}
}
