<?php
/**
 * Checkbox view
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
extract($this->data);

if($note):?>

<p class=anony-warning><?= $note ?><p>

<?php endif ?>

<fieldset class="anony-row anony-row-inline" id="fieldset_<?= $id?>">
				 
	<?php if($context == 'meta' && $title ) :?>

		<label class="anony-label" for="<?= $id ?>"><?= $title ?></label>

	<?php endif;

	// fix for WordPress 3.6 meta options
	if(strpos( $id ,'[]') === false) :?>

		<input type="hidden" name="<?= $name?>" value="0"/>

	<?php endif?> 
					
	<div class="anony-metabox-col">

	<?php if($options && is_array($options)):

		foreach($options as $opt => $title):

			$checked = (is_array($value) && in_array($opt, $value)) ? ' checked="checked"' : '';?>

			<label class="anony-inputs-row"> <?= $title ?> 
						
				<input type="checkbox" class="checkbox <?= $class ?>" id="<?= $id ?>[<?= $opt ?>]" name="<?= $name ?>[]" value="<?= $opt ?>"<?= $checked ?><?= $disabled ?>/>

			</label>

		<?php endforeach ?>

	</div>

	<?php else: $checked = checked($value, 1, false);?>

		<input type="checkbox" id="<?= $id ?>" name="<?= $name ?>" class="checkbox <?= $class ?>" value="1"<?= $checked.$disabled ?>/>

	<?php endif;

	if($desc && !empty($desc)):?>

		<div class="description btn-desc"><?= $desc?></div>

	<?php endif ?>


</fieldset>
