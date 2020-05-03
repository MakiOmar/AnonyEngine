<?php
/**
 * WPML helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */

if ( ! class_exists( 'ANONY_WPML_HELP' ) ) {
	class ANONY_WPML_HELP extends ANONY_HELP{
		/**
		 * Add WPML languages menu items
		 * @param  string $item menu items
		 * @return string
		 */
		static function langMenu($item = ''){

			$wpml_plugin = 'sitepress-multilingual-cms/sitepress.php';

			if ( ANONY_WPPLUGIN_HELP::isActive( $wpml_plugin) || function_exists('icl_get_languages') ) {


				$languages = icl_get_languages('skip_missing=0'/*make sure to include all available languages*/);

				if(!empty($languages)){

					$item .='<ul class="anony-lang-container">';

					foreach($languages as $l){
						
						if($l['language_code'] == ICL_LANGUAGE_CODE){
							$curr_lang = $l;
						}

						$item .='<li class="anony-lang-item">';
						$item .= '<a class="'.active_language($l['language_code']).'" href="'.$l['url'].'">';
						$item .= icl_disp_language(strtoupper($l['language_code']));
						$item .='</a>';
						$item .='</li>';
						$item .= apply_filters( 'anony_wpml_lang_item', $item );
					}
					$item .='</ul>';
					$item .= '<li id="anony-lang-toggle"><img src="'.$curr_lang['country_flag_url'].'" width="32" height="20" alt="'.$l['language_code'].'"/></li>';

					return apply_filters( 'anony_wpml_lang_menu', $item );
				}
				return $item;
			}else{
				return $item;
			}
		}

		/**
		 * Add WPML languages menu items flagged
		 * @return string
		 */
		static function langMenuFlagged(){

			$wpml_plugin = 'sitepress-multilingual-cms/sitepress.php';

			if ( ANONY_WPPLUGIN_HELP::isActive( $wpml_plugin) && function_exists('icl_get_languages') ) {

				$item = '';

				$languages = icl_get_languages('skip_missing=0'/*make sure to include all available languages*/);

				if(!empty($languages)){
					$item .='<div id="anony-lang-flagged-wrapper">';
				  
					$item .='<ul id="anony-lang-flagged">';
					foreach($languages as $l){
						if($l['language_code'] != ICL_LANGUAGE_CODE){
							$item .='<li class="anony-lang-item-flagged">';
							$item .= '<a href="'.$l['url'].'" class="anony-lang-item-link">';
							$item .= '<img src="'.$l['country_flag_url'].'" alt="'.$l['language_code'].'"/>&nbsp;<span class="anony-lang-name">'.$l['native_name'].'</span>';
							$item .='</a>';
							$item .='</li>';
						}
						
					}
					$item .='</ul>';
					$item .='</div>';
				 }
				return $item;
			}
		}

		/**
		 * Checks if plugin WPML is active
		 */
		static function isActive(){

			$wpml_plugin = 'sitepress-multilingual-cms/sitepress.php';
			
			if (  ANONY_WPPLUGIN_HELP::isActive( $wpml_plugin) || function_exists('icl_get_languages') ) return true;
			
			return false;
		}

		/**
		 * Get the AJAX url.
		 * **Description: ** Gets the AJAX url and add wpml required query strings for ajax, if WPML plugin is active
		 * @return string AJAX URL.
		 */
		static function getAjaxUrl(){
			$ajax_url = admin_url( 'admin-ajax.php' );

			if(ANONY_WPML_HELP::isActive()){

				$wpml_active_lang = apply_filters('wpml_current_language',NULL);

				if($wpml_active_lang){

					$ajax_url = add_query_arg('wp_lang',$wpml_active_lang, $ajax_url);
					

				}

			}

			return $ajax_url;
		}

		/**
		 *  Active language html class
		 *
		 * **Description: ** Just return a string which meant to be a class to be added to the active language markup.
		 *
		 * **Note: ** Only if WPML plugin is active.
		 * @param  string $lang language code to check for
		 * @return string 'active-lang' class if $lang is current active language else nothing
		 */
		static function ActiveLangClass($lang){

			if (  ANONY_WPML_HELP::isActive() ) {
				global $sitepress;
				
				if($lang == ICL_LANGUAGE_CODE){
					return 'active-lang';
				}
			}
		}

		/**
		 * Query posts when using WPML plugin
		 * @param  string $post_type    Queried post type
		 * @return mixed                An array of posts objects
		 */
		static function queryPostType($post_type = 'post'){
			$wpml_plugin = 'sitepress-multilingual-cms/sitepress.php';
			
			if ( (ANONY_WPPLUGIN_HELP::isActive( $wpml_plugin) || function_exists('icl_get_languages')) ) {
			
				global $wpdb;

				$lang = defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE :  apply_filters( 'wpml_current_language', NULL );

				$query = "SELECT * FROM {$wpdb->prefix}posts JOIN {$wpdb->prefix}icl_translations t ON {$wpdb->prefix}posts.ID = t.element_id AND t.element_type = CONCAT('post_', {$wpdb->prefix}posts.post_type)  WHERE {$wpdb->prefix}posts.post_type = '$post_type' AND {$wpdb->prefix}posts.post_status = 'publish' AND ( ( t.language_code = '$lang' AND {$wpdb->prefix}posts.post_type = '$post_type' ) )  ORDER BY {$wpdb->prefix}posts.post_date DESC";

				$results = $wpdb->get_results($query);

				return $results;
			}
			
			return [];
			
		}

		/**
		 * Get posts IDs and titles if wpml is active
		 * @param type $post_type 
		 * @return array Returns an array of post posts IDs and titles. empty array if no results
		 */
		static function queryPostTypeSimple($post_type = 'post'){
			
			$results = ANONY_WPML_HELP::queryPostType($post_type);
			
			$postIDs = [];
			
			if(!empty($results) && !is_null($results)){
				foreach($results as $result){
					$postIDs[$result->ID] = $result->post_title;
				}
			}
			
			return $postIDs;
		}
	}
}