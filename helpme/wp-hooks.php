<?php
/**
 * Callback for WordPress 'post_edit_form_tag' action.
 * 
 * Append enctype - multipart/form-data and encoding - multipart/form-data
 * to allow image uploads for post type 'post'
 * 
 * @global type $post
 * @return type
 */

add_action('post_edit_form_tag', function (){
    
    global $post;
    
    //  if invalid $post object, return
    if(!$post)
        return;
    
    /*//  get the current post type
    $post_type = get_post_type($post->ID);
    
    //  if post type is not 'post', return
    if('keyword' != $post_type)
        return;
    
    //  append our form attributes
    printf(' enctype="multipart/form-data" encoding="multipart/form-data" ');
    */
    
});
/**
 * Add required scripts in footer
 */
add_action('wp_print_footer_scripts', function(){
    $scripts  = apply_filters( 'anony_print_footer_scripts', '' );
    $domReady = apply_filters( 'anony_document_ready_jquery', '' );//For jQuery document ready scripts

    if (empty($scripts) && empty($domReady)) return;
    ?>
    <script type="text/javascript">
    	jQuery(document).ready(function($){
			"use strict";
			<?= $domReady  ?>
		});

    	<?= $scripts  ?>

    </script>

<?php });

/**
* Link all post thumbnails to the post permalink and remove width and height atrr from img
*
* @param  string $html          Post thumbnail HTML.
* @param  int    $post_id       Post ID.
* @param  int    $post_image_id Post image ID.
* @return string                Filtered post image HTML.
*/
add_filter( 'post_thumbnail_html', function ( $html, $post_id, $post_image_id ) {

	$html = '<a href="' . esc_url(get_permalink( $post_id )) . '" title="' . esc_attr( get_the_title( $post_id ) ) . '">' . $html . '</a>';
	
	return preg_replace('/(width|height)="\d+"\s/', "", $html);
}, 10, 3 ); 

/**
 * Define new rewrite tag, permastruct and rewrite rule for cross parent post type.
 */
add_action( 'init', function() {
	$post_parents = apply_filters( 'anony_cross_parent_rewrite', [] );

	if(empty($post_parents) || !is_array($post_parents)) return;

	foreach ($post_parents as $post_type => $parent_post_type) {

		add_rewrite_tag('%'.$post_type.'%', '([^/]+)', $post_type . '=');

		add_permastruct($post_type, '/'.$post_type.'/%'.$parent_post_type.'%/%'.$post_type.'%', false);
		add_rewrite_rule('^'.$post_type.'/([^/]+)/([^/]+)/?','index.php?'.$parent_post_type.'=$matches[2]','top');

	}
});

/**
 * Rewrite the permalink for cross parent post type.
 * @return string Post's permalink
 */
add_filter('post_type_link', function ($permalink, $post, $leavename) {

	/**
	 * Should be array of post_type => parent_post_type
	 */ 
	$post_parents = apply_filters( 'anony_cross_parent_permalink', [] );

	if(empty($post_parents) || !is_array($post_parents)) return $permalink;


	if(!in_array($post->post_type , array_keys($post_parents)) || empty($permalink) || in_array($post->post_status, array('draft', 'pending', 'auto-draft')))
	 	return $permalink;

	$parent_post_type = $post_parents[$post->post_type];

	$parent = $post->post_parent;

	$parent_post = get_post( $parent );

	$permalink = str_replace('%'.$parent_post_type.'%', $parent_post->post_name, $permalink);

	return $permalink;
}, 10, 3);
/**
 * Force https connection.
 *
 * Adds rewrite rules to htaccess to force using https.
 */
function anony_htaccess_https(){
	$rewrite = 'RewriteEngine On
	RewriteCond %{HTTPS} !=on
	RewriteRule ^.*$ https://%{SERVER_NAME}%{REQUEST_URI} [R,L]';
	
	return $rewrite;
}


/**
 * Google map
 * 
 * You have to get the lat&lang (for example stored into meta key). also you have to define the div id (e.g. googleMap).
 * Also you have to replace the YOUR_API_KEY by your google map api key.
 * To force map language you shoud use language&region query vars with the map url
 */ 
add_action( 'wp_print_footer_scripts', function(){
	if (!is_single()) return;
	$lat  = get_post_meta(get_the_ID(),'geolat',true);
	$lang = get_post_meta(get_the_ID(),'geolong',true);

	if(empty($lat) || empty($long)) return;
	?>
	<!--API should be always before map script-->
	<script type='text/javascript' src='https://maps.googleapis.com/maps/api/js?v=3.exp&key=YOUR_API_KEY&ver=4.9.10&language=ar&region=EG'></script> 

	<script type="text/javascript">
		var Gisborne = new google.maps.LatLng(
	        <?php echo $lat; ?>,
	        <?php echo $long; ?>
	    );
	    
	    function initialize(){
	        var mapProp = {
	                  center:Gisborne,
	                  zoom:13,
	                  scrollwheel: false,
	                  mapTypeId:google.maps.MapTypeId.ROADMAP
	            };  
	          
	        var map = new google.maps.Map(document.getElementById("googleMap"),mapProp);
	        
	        var marker=new google.maps.Marker({
	                  position:Gisborne,
	                  icon:"https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png"
	            });
	        marker.setMap(map);
	    }
	    google.maps.event.addDomListener(window, "load", initialize);
	</script>
	

<?php });

/**
 * Multilingual options
 */
add_action( 'after_setup_theme', function(){
		
	if (!ANONY_WPML_HELP::isActive()) return;
	
	$options = apply_filters( 'anony_wpml_multilingual_options', [] );
		
	if (is_array($options) && $options != [] ){
		
		foreach ($options as $option) {
			do_action('wpml_multilingual_options', $option);
		}
	}
		
	
}, 1);

/**
 * Display only children terms
 * 
 * @return array
 */
add_filter( 'terms_clauses', function (  $pieces, $taxonomies, $args )
{
    // Check if our custom arguments is set and set to 1, if not bail
    if (    !isset( $args['anony_exclude_top'] ) 
         || 1 !== $args['anony_exclude_top']
    )
        return $pieces;

    // Everything checks out, lets remove parents
    $pieces['where'] .= ' AND tt.parent > 0';

    return $pieces;
}, 10, 3 );

// Remove items from the admin bar

add_action('admin_bar_menu', function ($wp_admin_bar) {
	
	$items = apply_filters( 'remove_admin_bar_items', [] );
	
	if(empty($items)) return;

    /*

     * Placing items in here will only remove them from admin bar

     * when viewing the frontend of the site

    */

    if ( ! is_admin() ) {
    	
    	foreach ($items as $item) {

    		$wp_admin_bar->remove_node($item);
    	}

    }


}, 999);

/**
 * Dequeue not required styles
 * @param string   'wp_print_styles' Hook to be used for dequeue
 * @param callback
 * @return void
 */
add_action( 'wp_print_styles',  function(){
    $dequeued_styles = apply_filters( 'anony_deprint_styles', [] );

    foreach($dequeued_styles as $style){
        wp_dequeue_style( $style );
        wp_deregister_style( $style );
    }
    
}, 99);

/**
 * Dequeue not required scripts
 * @param string   'wp_print_scripts' Hook to be used for dequeue
 * @param callback
 * @return void
 */
add_action( 'wp_print_scripts', function(){
    $dequeued_stcripts =  apply_filters( 'anony_deprint_scripts', [] );

    foreach($dequeued_stcripts as $stcript){
        wp_dequeue_script( $stcript );
        wp_deregister_script( $stcript );
    }
}, 99 );


/**
 * Load proper thumbnail size within loops. should be hooked to post_thumbnail_html filter
 */
function anony_loop_proper_thumb_size ($html, $post_id, $post_thumbnail_id, $size, $attr) {
    
    $thumb_size = '';
    
    $post_type = '';
    
    if(is_single() || empty($thumb_size) || empty($post_type) || get_post_type() !== $post_type) return $html;
    
    
    // gets the id of the current post_thumbnail (in the loop)
    $id = get_post_thumbnail_id();
    
     // gets the image url specific to the passed in size (aka. custom image size)
    $src = wp_get_attachment_image_src($id, $thumb_size);
    
    // gets the post thumbnail title
    $alt = get_the_title($id);
    
    // gets classes passed to the post thumbnail, defined here for easier function access
    $class = $attr['class']; 


        $html = '<img src="' . $src[0] . '" alt="' . $alt . '" class="' . $class . '" width="277" height="316" />';

    return $html;
}