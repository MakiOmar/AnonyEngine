<?php
/**
 * File upload view
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

if ( '' !== $note ) { ?>
		<p class=anony-warning><?php echo esc_html( $note ); ?><p>
<?php } ?>

<fieldset class="anony-row anony-row-inline" id="fieldset_<?php echo esc_attr( $id ); ?>">
	
	<?php
	if ( $is_meta && $has_title ) :
		?>
		<label class="anony-label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
	<?php endif ?>
	
	<div class="file-upload-override-button">
		<a href="#" class="file-upload button button-primary" data-editor="my-editor"><?php echo esc_html( $select_text ); ?></a>
	</div>

	<div id="download-file-<?php echo esc_attr( $id ); ?>" class="download-file">
		
		<?php
		if ( $file_url ) :
			?>
			
				<p class="file"><?php echo esc_html( $current_text ); ?><span><?php echo esc_html( $basename ); ?></span>&nbsp;<a href="<?php echo esc_url( $file_url ); ?>" download><?php echo esc_html( $download_text ); ?></a></p>
				
			<?php
		else :
			?>
			
				<p class="no-file"><span><?php echo esc_html( $no_file_text ); ?></span></p>
		
		<?php endif ?>

	</div>
	<!-- Caller -->
	<span id="media-caller-<?php echo esc_attr( $id ); ?>">
		<div class="attachment">
			<a class="hidden" href="#" download>file</a>
			<input type="hidden" name ="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" class="<?php echo esc_attr( $class_attr ); ?>">
		</div>
	</span>
	
	<div class="description"><?php echo esc_html( $desc ); ?></div>

</fieldset>

