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

			if(self::isActive()){

				$wpml_active_lang = self::gatActiveLang();

				if($wpml_active_lang){

					$ajax_url = add_query_arg('wp_lang',$wpml_active_lang, $ajax_url);
					

				}

			}

			return $ajax_url;
		}
		
		/**
		 * Return active language's code
		 * @return string
		 */
		static function gatActiveLang(){
			if(!self::isActive()) return false;
			if(defined('ICL_LANGUAGE_CODE')) return ICL_LANGUAGE_CODE;
			
			return apply_filters('wpml_current_language',NULL);
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

			if (  self::isActive() ) {
				global $sitepress;
				
				if($lang == self::gatActiveLang()){
					return 'active-lang';
				}
				
				return '';
			}
		}

		/**
		 * Query posts when using WPML plugin
		 * @param  string $post_type    Queried post type
		 * @return mixed                An array of posts objects
		 */
		static function queryPostType($post_type = 'post'){
			
			if ( !self::isActive()) return [];
			
			global $wpdb;

			$lang = self::gatActiveLang();

			$query = "SELECT * FROM {$wpdb->prefix}posts JOIN {$wpdb->prefix}icl_translations t ON {$wpdb->prefix}posts.ID = t.element_id AND t.element_type = CONCAT('post_', {$wpdb->prefix}posts.post_type)  WHERE {$wpdb->prefix}posts.post_type = '$post_type' AND {$wpdb->prefix}posts.post_status = 'publish' AND ( ( t.language_code = '$lang' AND {$wpdb->prefix}posts.post_type = '$post_type' ) )  ORDER BY {$wpdb->prefix}posts.post_date DESC";

			$results = $wpdb->get_results($query);

			return $results;
		}

		/**
		 * Get posts IDs and titles if wpml is active
		 * @param type $post_type 
		 * @return array Returns an array of post posts IDs and titles. empty array if no results
		 */
		static function queryPostTypeSimple($post_type = 'post'){
			if(!self::isActive()) return;
			$results = ANONY_WPML_HELP::queryPostType($post_type);
			
			$postIDs = [];
			
			if(!empty($results) && !is_null($results)){
				foreach($results as $result){
					$postIDs[$result->ID] = $result->post_title;
				}
			}
			
			return $postIDs;
		}
		
		/**
		 * Translates post's terms
		 * @param object $source_post The post object of which terms will be translated 
		 * @param string $lang Translation language code
		 * @return array Returns an array of taxonomies as its keys and terms' IDs as values. empty array if no results
		 */
		static function translatePostTerms($source_post, $lang){
		    $translated_terms = [];
		    /*
			 * get all current post terms ad set them to the new post draft
			 */
			$taxonomies = get_object_taxonomies( $source_post->post_type ); // returns array of taxonomy names for post type, ex array("category", "post_tag");
			
		    if( $taxonomies ) {
			   
				foreach ( $taxonomies as $taxonomy ) {
				    
					$post_terms = wp_get_object_terms( $source_post->ID, $taxonomy, array( 'fields' => 'slugs' ) );
					
					if(is_wp_error($post_terms)) continue;
					
					foreach($post_terms as $slug){
					    $post_term = get_term_by('slug', $slug, $taxonomy);
					    
					    if(!$post_term) continue 2;
					    
						$translated_term = self::getTranslatedTerm($post_term->term_id, $taxonomy, $lang);

						if(is_null($translated_term)){
							
							$translated_term = self::translateTerm( $post_term->term_id , $lang, $taxonomy );
						}
						
						$translated_terms [$taxonomy][] = (int) $translated_term->term_id;
					}
					
				
				}
			}
			
			return $translated_terms;
		}
		
		/**
		 * Assign a post to its corresponding terms
		 * @param array $translated_post_terms An array of taxonomies as its keys and terms' IDs as values
		 * @param string $new_post_id The ID of the post
		 */
		static function setTranslatedPostTerms(array $translated_post_terms, int $new_post_id){
			
			ANONY_POST_HELP::setPostTerms( $translated_post_terms,  $new_post_id);
		}
		/**
		 * Get translated term object
		 * @param  int    $term_id 
		 * @param  string $taxonomy 
		 * @return Mixed  Term object on success or null on failure
		 */
		static function getTranslatedTerm($term_id, $taxonomy, $lang) {
			if(!self::isActive()) return;
			global $sitepress;
	 
		    $translated_term_id = icl_object_id(intval($term_id), $taxonomy, false, $lang);
		    
		    if (is_null($translated_term_id)) return $translated_term_id;
		    
		    remove_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), 1 );
		    
		    $translated_term_object = get_term_by('id', intval($translated_term_id), $taxonomy);
		    
		    add_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), 1, 1 );
		 
		    return $translated_term_object;	
		    
		}
		
		/**
		 * Duplicates a post
		 * @param  int    $post_id ID of post to be duplicated 
		 * @return Mixed  Translated post id on success or null/wp_error on failure
		 */
		static function duplicate( $post_id  ){
			if(!self::isActive()) return $post_id;
			// Insert translated post
		    $post_duplicated_id = ANONY_POST_HELP::duplicate($post_id);
			
			if($post_duplicated_id){
				$wpml_element_type = apply_filters( 'wpml_element_type', 'post' );
				// get the language info of the original post
				// https://wpml.org/wpml-hook/wpml_element_language_details/
				$get_language_args = array(
					'element_id' => $post_id, 
					'element_type' => 'post' 
				);
				
				$original_post_language_info = apply_filters( 'wpml_element_language_details', null, $get_language_args );
				
				
				$set_language_args = array(
					'element_id'    => $post_duplicated_id,
					'element_type'  => $wpml_element_type,
					'trid'   => NULL,
					'language_code'   => $original_post_language_info->language_code,
					'source_language_code' => $original_post_language_info->language_code
				);

				do_action( 'wpml_set_element_language_details', $set_language_args );
			}
		}
		
		/**
		 * Add post translation without translating its terms
		 * @param  int    $post_id ID of post to be translated 
		 * @param  string $post_type
		 * @param  string $lang Language of translation
		 * @return Mixed  Translated post id on success or null/wp_error on failure
		 */
		static function translatePostType( $source_post , $post_type, $lang ){
			if ( !self::isActive()) return $source_post->ID;
			
			if( ! current_user_can( 'edit_posts' ) || is_admin() || !isset($_GET['duplicate']) )  return;

		    // Include WPML API
		    include_once( WP_PLUGIN_DIR . '/sitepress-multilingual-cms/inc/wpml-api.php' );
			
			$post_id = $source_post->ID;
			
			
			// Define title of translated post
		    $post_translated_title = $source_post->post_title . ' (' . $lang . ')';
            
		    // Insert translated post
		    $post_translated_id = ANONY_POST_HELP::duplicate($post_id, [
				'post_title'   => $post_translated_title
			]);

		    if (!$post_translated_id || is_wp_error($post_translated_id)) return $post_translated_id;
            
		    self::connectPostTranslation( $post_id ,$post_translated_id, $post_type, $lang );
		   
		    // Return translated post ID
		    return $post_translated_id;
		}
		/**
		 * Add post translation
		 * @param  int    $post_id ID of post to be translated 
		 * @param  string $lang Language of translation
		 * @return Mixed  Translated post id on success or null/wp_error on failure
		 */
		static function translatePost( $post_id, $post_type = 'post', $lang ){
			
			if($post_type == 'page') return $post_id;
			
			$source_post = get_post( $post_id );
			
		    // Insert translated post
		    $post_translated_id = self::translatePostType( $source_post, $post_type, $lang );

		    if (!$post_translated_id || is_wp_error($post_translated_id)) return $post_translated_id;
            
		    self::connectPostTranslation( $post_id ,$post_translated_id, $post_type, $lang );
		    
			$translated_terms = self::translatePostTerms($source_post, $lang);
			
			self::setTranslatedPostTerms($translated_terms,$post_translated_id);
			
			return $post_translated_id;


		}
		
		/**
		 * Add page translation
		 * @param  int    $post_id ID of post to be translated 
		 * @param  string $lang Language of translation
		 * @return Mixed  Translated post id on success or null/wp_error on failure
		 */
		static function translatePage( $post_id, $lang ){
			
			$source_post = get_post( $post_id );
			
		    // Insert translated post
		    $post_translated_id = self::translatePostType( $source_post, 'page', $lang );

		    if (!$post_translated_id || is_wp_error($post_translated_id)) return $post_translated_id;
            
		    self::connectPostTranslation( $post_id ,$post_translated_id, 'post', $lang );

			return $post_translated_id;

		}
		
		/**
		 * Connects post translation
		 * @param  int    $post_id ID of original post
		 * @param  int    $post_translated_id ID of translated post 
		 * @param  string $post_type
		 * @param  string $lang Language of translation
		 * @return void 
		 */
		static function connectPostTranslation( $post_id ,$post_translated_id, $post_type, $lang ){
			if(!self::isActive()) return;
			global $sitepress;

			$trid = wpml_get_content_trid( 'post_' . $post_type, $post_id );

			$sitepress->set_element_language_details($post_translated_id , 'post_' . $post_type, $trid, $lang);

		}

		/**
		 * Add term translation
		 * @param  int    $post_id ID of original post
		 * @param  int    $post_translated_id ID of translated post 
		 * @param  string $post_type
		 * @param  string $lang Language of translation
		 * @return void 
		 */
		static function connectTermTranslation( $translated_term_taxonomy_id, $term_taxonomy_id , $taxonomy, $lang ){
			if(!self::isActive()) return;
			global $sitepress;

			$trid = $sitepress->get_element_trid($term_taxonomy_id, 'tax_' . $taxonomy);

			$sitepress->set_element_language_details($translated_term_taxonomy_id , 'tax_' . $taxonomy, $trid, $lang, $sitepress->get_default_language());

		}

		/**
		 * Add product translation
		 * @param  int    $product_id ID of product to be translated 
		 * @param  string $lang Language of translation
		 * @return Mixed  Translated post id on success or null/wp_error on failure
		 */
		static function translateProduct( $product_id , $lang ){
			if(!self::isActive()) return;
			if(!class_exists('woocommerce')) return;

			//Check if translation already exists;
			$is_translated = apply_filters( 'wpml_element_has_translations', NULL , $product_id , 'product' );

			if($is_translated) return;

			$duplicated_product = ANONY_WOO_HELP::duplicateProduct($product_id);

			if(!$duplicated_product) return;

			$duplicated_id = $duplicated_product->get_id();


			self::connectPostTranslation( $product_id ,$duplicated_id, 'product', $lang );
		}

		/**
		 * Add product translation
		 * @param  int    $product_id ID of product to be translated 
		 * @param  string $lang Language of translation
		 * @return Mixed  Translated post id on success or null/wp_error on failure
		 */
		static function translateTerm( $term_id , $lang, $taxonomy ){
			if(!self::isActive()) return $term_id;
			
			$translated_term = self::getTranslatedTerm($term_id, $taxonomy, $lang);
				
			if(!is_null($translated_term)) return $translated_term;
			
			global $sitepress;

			//Check if translation already exists;
			$is_translated = apply_filters( 'wpml_element_has_translations', NULL , $term_id , $taxonomy );
			
			if($is_translated) return;

			$term = get_term_by('id', $term_id, $taxonomy);

			if(!$term) return;

			$args = [
				'description'=> $term->description , 
				'slug' => $term->slug.'-'. $lang, 
			];

			$inserted_term_id = wp_insert_term( $term->name.'-'. $lang, $taxonomy, $args );

			if(is_wp_error($inserted_term_id)) return;


			self::connectTermTranslation( $inserted_term_id['term_taxonomy_id'] ,$term->term_taxonomy_id, $taxonomy, $lang );
			
			return get_term_by('id',$inserted_term_id['term_id'],  $taxonomy);
			
		}
	}
}