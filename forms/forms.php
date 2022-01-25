<?php
if(!defined('ABSPATH')) exit();

/**
 * Holds a path to forms folder
 * @const
 */
define('ANONY_FORMS_PATH', wp_normalize_path(ANOE_DIR . 'forms/')); 


/**
 * Holds forms' URI 
 * @const
 */
define('ANONY_FORMS_URI', ANOE_URI . 'forms/');

/**
 * Holds a URI to main classes folder
 * @const
 */
define('ANONY_FORMS_CLASSES', ANONY_FORMS_PATH .'classes/' );