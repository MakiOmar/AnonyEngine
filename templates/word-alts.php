<fieldset class="anony-row anony-row-inline" id="fieldset_diwn_keywords_alt">
	
	<label class="anony-label" for="diwn_keywords_alt"><?= $word ?></label>
	
	<input type="text" class="words-alts" id="<?= $rel_id ?>" value="<?= $value ?>"/>
	
	<input type="hidden" class="word-element" value="<?= $word ?>"/>
	
	<input type="hidden" class="word-element-index" value="<?= $index ?>"/>
		
	<a href="#" rel-id="<?= $rel_id ?>" class="anony-middle save-alt button button-primary"><?= $button_text ?></a>
</fieldset>
