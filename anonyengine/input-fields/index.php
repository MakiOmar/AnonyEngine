<?php


/**
 * Holds a path to Custom fields classes folder
 * @const
 */
define( 'ANONY_INPUT_FIELDS', wp_normalize_path (ANOE_DIR . 'input-fields/'));

/**
 * Holds a path to Custom fields classes folder
 * @const
 */
define( 'ANONY_INPUT_FIELDS_URI', wp_normalize_path (ANOE_URI . 'input-fields/'));

/**
 * Holds a path to Custom fields classes folder
 * @const
 */
define( 'ANONY_FIELDS_DIR', wp_normalize_path (ANONY_INPUT_FIELDS . 'fields/'));

/**
 * Holds a path to Custom fields classes folder
 * @const
 */
define( 'ANONY_FIELDS_URI', wp_normalize_path (ANONY_INPUT_FIELDS_URI . 'fields/'));

add_action( 'wp_head', function(){?>
		<style type="text/css">
			[id*="fieldset_anony"] {
				display: inline-flex;
				flex-direction: column;
				border: 0;
			}

			.anony-multi-value-flex {
				align-items: flex-start!important;
			}
		</style>
	<?php }
);

