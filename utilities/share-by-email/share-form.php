<?php
/**
 * Share form
 *
 * @package AnonyEngine
 */

defined( 'ABSPATH' ) || die();
?>

<form id='anony-share-email-form' method="post" autocomplete="on">
	<div id="anony-share-container">
		<div id="anony-close-share">
			<span><i class="fa fa-close"></i></span>
		</div>
		
		<h3><?php echo esc_html( $titel ); ?></h3>
		<p><?php echo esc_html( $subtitle ); ?></p>
		<input type="email" name="anony_share_email" placeholder="Email"/>
		<input type="hidden" name="anony_share_title" value="<?php echo esc_attr( $page_title ); ?>"/>
		<input type="hidden" name="anony_share_description" value="<?php echo esc_attr( $description ); ?>"/>
		<input type="hidden" name="anony_share_url" value="<?php echo esc_attr( $permalink ); ?>"/>
		<input type="hidden" name="anony_share_img" value="<?php echo esc_attr( $og_featured_image ); ?>"/>
		<a class="anony-email-share" href="#" id="button"><?php echo esc_html( $submit_txt ); ?></a>
		<div id="anony-share-msg"></div>
	</div>
	
</form>
