<?php
/**
 * WP comments helpers.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_Wp_Comment_Help' ) ) {
	/**
	 * WP comments helpers.
	 *
	 * PHP version 7.3 Or Later.
	 *
	 * @package  AnonyEngine
	 * @author   Makiomar <info@makiomar.com>
	 * @license  https://makiomar.com AnonyEngine Licence
	 * @link     https://makiomar.com/anonyengine
	 */
	class ANONY_Wp_Comment_Help extends ANONY_HELP {
		/**
		 * Comments render
		 *
		 * @param  object  $comment Comment object.
		 * @param  array   $args Comment args.
		 * @param  integer $depth Comments list depth.
		 * @return void
		 */
		public static function render_comment( $comment, $args, $depth ) {
			// phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
			$GLOBALS['comment'] = $comment;
			// phpcs:enable.

			switch ( $comment->comment_type ) :
				case 'pingback':
				case 'trackback':
					// Display trackbacks differently than normal comments.
					?>
			<li <?php comment_class(); ?> id="anony-comment-<?php comment_ID(); ?>">
				<p><?php esc_html_e( 'Pingback:', 'anonyengine' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( esc_html__( '(Edit)', 'anonyengine' ), '<span class="edit-link">', '</span>' ); ?></p>
					<?php
					break;
				default:
					// Proceed with normal comments.
					global $post;
					?>
			<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
				<article id="anony-comment-<?php comment_ID(); ?>" class="anony-comment">
					<header class="anony-comment-meta comment-author vcard">
						<?php
							echo get_avatar( $comment, 44 );
							printf(
								'<cite><b class="fn">%1$s</b> %2$s</cite>',
								get_comment_author_link(),
								// If current post author is also comment author, make it known visually.
								( $comment->user_id === $post->post_author ) ? '<span>' . esc_html__( 'Post author', 'anonyengine' ) . '</span>' : ''
							);
							printf(
								'<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
								esc_url( get_comment_link( $comment->comment_ID ) ),
								esc_attr( get_comment_time( 'c' ) ),
								/* translators: 1: date, 2: time */
								sprintf( esc_html__( '%1$s at %2$s', 'anonyengine' ), esc_html( get_comment_date() ), esc_html( get_comment_time() ) )
							);
						?>
					</header><!-- .anony-comment-meta -->

					<?php if ( '0' === $comment->comment_approved ) : ?>
						<p class="anony-comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'anonyengine' ); ?></p>
					<?php endif; ?>

					<section class="anony-comment-content comment">
						<?php comment_text(); ?>
						<?php edit_comment_link( esc_html__( 'Edit', 'anonyengine' ), '<p class="edit-link">', '</p>' ); ?>
					</section><!-- .anony-comment-content -->

					<div class="reply">
						<?php
						comment_reply_link(
							array_merge(
								$args,
								array(
									'reply_text' => esc_html__( 'Reply', 'anonyengine' ),
									'after'      => ' <span>&darr;</span>',
									'depth'      => $depth,
									'max_depth'  => $args['max_depth'],
								)
							)
						);
						?>
					</div><!-- .reply -->
				</article><!-- #anony-comment-## -->
					<?php
					break;
			endswitch; // end comment_type check.
		}

	}
}
