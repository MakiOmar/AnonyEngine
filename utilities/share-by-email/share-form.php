<form id='anony-share-email-form' method="post" autocomplete="on">
	<div id="anony-share-container">
		<div id="anony-close-share">
			<span><i class="fa fa-close"></i></span>
		</div>
		
		<h3><?php echo $titel; ?></h3>
		<p><?php echo $subtitle; ?></p>
		<input type="email" name="anony_share_email" placeholder="Email"/>
		<input type="hidden" name="anony_share_title" value="<?php echo $page_title; ?>"/>
		<input type="hidden" name="anony_share_description" value="<?php echo $description; ?>"/>
		<input type="hidden" name="anony_share_url" value="<?php echo $permalink; ?>"/>
		<input type="hidden" name="anony_share_img" value="<?php echo $og_featured_image; ?>"/>
		<a class="anony-email-share" href="#" id="button"><?php echo $submit_txt; ?></a>
		<div id="anony-share-msg"></div>
	</div>
	
</form>
