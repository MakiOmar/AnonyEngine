<fieldset class="anony-row anony-row-inline" id="fieldset_<?= $metabox_id ?>">
	<label class="anony-label" for="%1$s"><?= $label ?></label>
	<div class="file-upload-override-button">
		<a href="#" class="insert-media button button-primary" data-editor="my-editor"><?= $select_text ?></a>
	</div>

	<div id="download-file">
		
		<?php if(!empty($file_url)) :?>
			
				<p><?= $current_text ?><span><?= $basename ?></span></p><a href="<?= $file_url ?>"><?= $download_text ?></a>
				
		<?php else: ?>
			
				<p><span><?= $no_file_text ?></span></p>
		
		<?php endif ?>

	</div>
	<!-- Caller -->
	<span id="media-caller">
		<div class="attachment">
			<img width="277" height="300" alt="{{ alt }}">
			<input type="hidden" name ="diwn_keywords_excel" value="{{ url }}">
		</div>
	</span>

	<!-- Results placeholder -->
	<div id="upload-result"></div>

</fieldset>
