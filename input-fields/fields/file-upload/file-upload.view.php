<?php if($note != ''){?>
		<p class=anony-warning><?= $note ?><p>
<?php }?>

<fieldset class="anony-row anony-row-inline" id="fieldset_<?= $id ?>">
	
	<?php if($is_meta && $has_title) :  ?>
		<label class="anony-label" for="<?= $id ?>"><?= $title ?></label>
	<?php endif ?>
	
	<div class="file-upload-override-button">
		<a href="#" class="file-upload button button-primary" data-editor="my-editor"><?= $select_text ?></a>
	</div>

	<div id="download-file-<?= $id ?>" class="download-file">
		
		<?php if($file_url) :?>
			
				<p class="file"><?= $current_text ?><span><?= $basename ?></span>&nbsp;<a href="<?= $file_url ?>" download><?= $download_text ?></a></p>
				
		<?php else: ?>
			
				<p class="no-file"><span><?= $no_file_text ?></span></p>
		
		<?php endif ?>

	</div>
	<!-- Caller -->
	<span id="media-caller-<?= $id ?>">
		<div class="attachment">
			<a class="hidden" href="#" download>file</a>
			<input type="hidden" name ="<?= $name ?>" value="<?= $value ?>" class="<?= $class_attr ?>">
		</div>
	</span>
	
	<div class="description"><?= $desc ?></div>

</fieldset>
