<?php 

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>
<fieldset class="anony-row anony-row-inline">
	
	<label class="anony-label" for="diwn_keywords_alt"><?= $pattern ?></label>
	<div>
		<p class="alt-msg success-msg"><?= $success_msg ?></p>
		<p class="alt-msg failed-msg"><?= $failed_msg ?></p>
		<select class="words-alts-select">
					
				<?php foreach ($alts as $alt) :?>
					<option value="><?= $alt ?>"><?= $alt ?></option>
				<?php endforeach ?>

		</select>
		
		<input type="text" class="words-alts" id="<?= $rel_id ?>" value=""/>
		
		<input type="hidden" class="word-element" value="<?= $pattern ?>"/>
		
		<input type="hidden" class="word-element-index" value="<?= $index ?>"/>
			
		<a href="#" rel-id="<?= $rel_id ?>" class="anony-middle save-alt button button-primary"><?= $button_text ?></a>
	</div>
</fieldset>
