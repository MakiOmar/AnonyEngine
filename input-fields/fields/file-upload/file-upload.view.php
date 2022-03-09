<?php if ( $note != '' ) { ?>
		<p class=anony-warning><?php echo $note; ?><p>
<?php } ?>

<fieldset class="anony-row anony-row-inline" id="fieldset_<?php echo $id; ?>">
	
	<?php
	if ( $is_meta && $has_title ) :
		?>
		<label class="anony-label" for="<?php echo $id; ?>"><?php echo $title; ?></label>
	<?php endif ?>
	
	<div class="file-upload-override-button">
		<a href="#" class="file-upload button button-primary" data-editor="my-editor"><?php echo $select_text; ?></a>
	</div>

	<div id="download-file-<?php echo $id; ?>" class="download-file">
		
		<?php
		if ( $file_url ) :
			?>
			
				<p class="file"><?php echo $current_text; ?><span><?php echo $basename; ?></span>&nbsp;<a href="<?php echo $file_url; ?>" download><?php echo $download_text; ?></a></p>
				
			<?php
		else :
			?>
			
				<p class="no-file"><span><?php echo $no_file_text; ?></span></p>
		
		<?php endif ?>

	</div>
	<!-- Caller -->
	<span id="media-caller-<?php echo $id; ?>">
		<div class="attachment">
			<a class="hidden" href="#" download>file</a>
			<input type="hidden" name ="<?php echo $name; ?>" value="<?php echo $value; ?>" class="<?php echo $class_attr; ?>">
		</div>
	</span>
	
	<div class="description"><?php echo $desc; ?></div>

</fieldset>

