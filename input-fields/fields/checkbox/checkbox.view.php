<?php
/**
 * Checkbox view
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
extract( $this->data );

if ( $note ) :?>

<p class=anony-warning><?php echo $note; ?><p>

<?php endif ?>

<fieldset class="anony-row anony-row-inline" id="fieldset_<?php echo $id; ?>">
				 
	<?php if ( $context == 'meta' && $title ) : ?>

		<label class="anony-label" for="<?php echo $id; ?>"><?php echo $title; ?></label>

		<?php
	endif;

	// fix for WordPress 3.6 meta options
	if ( strpos( $id, '[]' ) === false ) :
		?>

		<input type="hidden" name="<?php echo $name; ?>" value="0"/>

	<?php endif ?> 
					
	<div class="anony-metabox-col">

	<?php
	if ( $options && is_array( $options ) ) :

		foreach ( $options as $opt => $title ) :

			$checked = ( is_array( $value ) && in_array( $opt, $value ) ) ? ' checked="checked"' : '';
			?>

			<label class="anony-inputs-row"> 
						
				<input type="checkbox" class="checkbox <?php echo $class; ?>" id="<?php echo $id; ?>[<?php echo $opt; ?>]" name="<?php echo $name; ?>[]" value="<?php echo $opt; ?>"<?php echo $checked; ?><?php echo $disabled; ?>/><?php echo $title; ?>

			</label>

		<?php endforeach ?>

	</div>

		<?php
	else :
		$checked = checked( $value, 1, false );
		?>

		<input type="checkbox" id="<?php echo $id; ?>" name="<?php echo $name; ?>" class="checkbox <?php echo $class; ?>" value="1"<?php echo $checked . $disabled; ?>/>

		<?php
	endif;

	if ( $desc && ! empty( $desc ) ) :
		?>

		<div class="description btn-desc"><?php echo $desc; ?></div>

	<?php endif ?>


</fieldset>
