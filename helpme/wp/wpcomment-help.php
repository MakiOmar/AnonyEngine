<?php
/**
 * WP comments helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

if ( ! class_exists( 'ANONY_WPCOMMENT_HELP' ) ) {
	class ANONY_WPCOMMENT_HELP extends ANONY_HELP{
		/**
		 * Comments render
		 * @param  object  $comment 
		 * @param  array   $args 
		 * @param  integer $depth 
		 * @return void
		 */
		static function renderComment( $comment, $args, $depth ) {
			$GLOBALS['comment'] = $comment;
			
			switch ( $comment->comment_type ) :
				case 'pingback' :
				case 'trackback' :
				// Display trackbacks differently than normal comments.
			?>
			<li <?php comment_class(); ?> id="anony-comment-<?php comment_ID(); ?>">
				<p><?php esc_html_e( 'Pingback:', ANOE_TEXTDOM  ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( esc_html__( '(Edit)', ANOE_TEXTDOM  ), '<span class="edit-link">', '</span>' ); ?></p>
			<?php
					break;
				default :
				// Proceed with normal comments.
				global $post;
			?>
			<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
				<article id="anony-comment-<?php comment_ID(); ?>" class="anony-comment">
					<header class="anony-comment-meta comment-author vcard">
						<?php
							echo get_avatar( $comment, 44 );
							printf( '<cite><b class="fn">%1$s</b> %2$s</cite>',
								get_comment_author_link(),
								// If current post author is also comment author, make it known visually.
								( $comment->user_id === $post->post_author ) ? '<span>' . esc_html__( 'Post author', ANOE_TEXTDOM  ) . '</span>' : ''
							);
							printf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
								esc_url( get_comment_link( $comment->comment_ID ) ),
								get_comment_time( 'c' ),
								/* translators: 1: date, 2: time */
								sprintf( esc_html__( '%1$s at %2$s', ANOE_TEXTDOM  ), get_comment_date(), get_comment_time() )
							);
						?>
					</header><!-- .anony-comment-meta -->

					<?php if ( '0' == $comment->comment_approved ) : ?>
						<p class="anony-comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', ANOE_TEXTDOM  ); ?></p>
					<?php endif; ?>

					<section class="anony-comment-content comment">
						<?php comment_text(); ?>
						<?php edit_comment_link( esc_html__( 'Edit', ANOE_TEXTDOM  ), '<p class="edit-link">', '</p>' ); ?>
					</section><!-- .anony-comment-content -->

					<div class="reply">
						<?php comment_reply_link( array_merge( $args, array( 'reply_text' => esc_html__( 'Reply', ANOE_TEXTDOM  ), 'after' => ' <span>&darr;</span>', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
					</div><!-- .reply -->
				</article><!-- #anony-comment-## -->
			<?php
				break;
			endswitch; // end comment_type check
		}
				
	}
}