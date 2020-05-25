<?php 

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>
<fieldset id="<?= $rel_id ?>-wrapper" class="anony-row anony-row-inline">
	
	<label class="anony-label" for="diwn_keywords_alt" style="display: inline-flex;align-items: center;"><p><?= $pattern ?></p>
	<p class="alt-msg success-msg" style="margin: 0 1em"><?= $success_msg ?></p>
	<p class="alt-msg failed-msg" style="margin: 0 1em"><?= $failed_msg ?></p>
	</label>
	<div>
		<select class="words-alts-select" data-key="<?= $save_action ?>">
					
				<?php foreach ($alts as $index => $alt) :?>
					<option value="<?= $parent_index?>-<?= $index ?>"><?= $alt ?></option>
				<?php endforeach ?>

		</select>
		
		<input type="text" class="words-alts" id="<?= $rel_id ?>" value=""/>
		
		<input type="hidden" class="word-element" value="<?= $pattern ?>"/>
		
		<input type="hidden" class="word-element-index" value="<?= $parent_index ?>"/>
			
		<a href="#" rel-id="<?= $rel_id ?>" class="anony-middle save-alt button button-primary" data-action="<?= $save_action ?>"><span></span><?= $button_text ?></a>
	</div>
</fieldset>
