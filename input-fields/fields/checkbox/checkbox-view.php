<?php
/**
 * Checkbox view
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

if ( $this->data['note'] ) :?>

<p class=anony-warning><?php echo esc_html( $this->data['note'] ); ?><p>

<?php endif ?>

<fieldset class="anony-row anony-row-inline" id="fieldset_<?php echo esc_attr( $this->data['id'] ); ?>">
				 
	<?php if ( 'meta' === $this->data['context'] && $this->data['title'] ) : ?>

		<label class="anony-label" for="<?php echo esc_attr( $this->data['id'] ); ?>"><?php echo esc_html( $this->data['title'] ); ?></label>

		<?php
	endif;

	// fix for WordPress 3.6 meta options.
	if ( strpos( $this->data['id'], '[]' ) === false ) :
		?>

		<input type="hidden" name="<?php echo esc_attr( $this->data['name'] ); ?>" value="0"/>

	<?php endif ?> 
					
	<div class="anony-metabox-col">

	<?php
	if ( $this->data['options'] && is_array( $this->data['options'] ) ) :

		foreach ( $this->data['options'] as $opt => $label ) :

			$checked = ( is_array( $this->data['value'] ) && in_array( $opt, $this->data['value'], true ) ) ? ' checked="checked"' : '';
			?>

			<label class="anony-inputs-row"> 
						
				<input type="checkbox" class="checkbox <?php echo esc_attr( $this->data['class'] ); ?>" id="<?php echo esc_attr( $this->data['id'] ); ?>[<?php echo esc_html( $opt ); ?>]" name="<?php echo esc_attr( $this->data['name'] ); ?>[]" value="<?php echo esc_attr( $opt ); ?>"<?php echo esc_attr( $checked ); ?><?php echo esc_attr( $this->data['disabled'] ); ?>/><?php echo esc_html( $label ); ?>

			</label>

		<?php endforeach ?>

	</div>

		<?php
	else :
		$checked = checked( $this->data['value'], 1, false );
		?>

		<input type="checkbox" id="<?php echo esc_attr( $this->data['id'] ); ?>" name="<?php echo esc_attr( $this->data['name'] ); ?>" class="checkbox <?php echo esc_attr( $this->data['class'] ); ?>" value="1"<?php echo esc_attr( $checked . $this->data['disabled'] ); ?>/>

		<?php
	endif;

	if ( $this->data['desc'] && ! empty( $this->data['desc'] ) ) :
		?>

		<div class="description btn-desc"><?php echo esc_html( $this->data['desc'] ); ?></div>

	<?php endif ?>
</fieldset>
