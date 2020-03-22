<?php
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


add_action( 'init', function() {
	$post_parents = apply_filters( 'anony_cross_parent_rewrite', [] );

	if(empty($post_parents) || !is_array($post_parents)) return;

	foreach ($post_parents as $post_type => $parent_post_type) {

		add_rewrite_tag('%'.$post_type.'%', '([^/]+)', $post_type . '=');

		add_permastruct($post_type, '/'.$post_type.'/%'.$parent_post_type.'%/%'.$post_type.'%', false);
		add_rewrite_rule('^'.$post_type.'/([^/]+)/([^/]+)/?','index.php?'.$parent_post_type.'=$matches[2]','top');

	}
});


add_filter('post_type_link', function ($permalink, $post, $leavename) {

	/**
	 * Should be array of post_type => parent_post_type
	 */ 
	$post_parents = apply_filters( 'anony_cross_parent_permalink', [] );

	if(empty($post_parents) || !is_array($post_parents)) return;


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
 * Add styles to head if a shortcode exists within post content.
 * You should replace the anony_shcode_rplaceit with your shortcode
 */
add_action('wp_head', function () {
    global $post;
    if ($post) {
        setup_postdata($post);
        $content = get_the_content();
        preg_match('/\[anony_shcode_rplaceit\s*.*\]/', $content, $matches);
        if ($matches) {
            echo '<style>.some_style { font-weight:800; }</style>';
        }
    }
});

/**
 * Google map
 * 
 * You have to get the lat&lang (for example stored into meta key). also you have to define the div id (e.g. googleMap).
 * Also you have to replace the YOUR_API_KEY by your google map api key.
 * To force map language you shoud use language&region query vars with the map url
 */ 
add_action( 'wp_print_footer_scripts', function(){?>
	<!--API should be always before map script-->
	<script type='text/javascript' src='https://maps.googleapis.com/maps/api/js?v=3.exp&key=YOUR_API_KEY&ver=4.9.10&language=ar&region=EG'></script> 

	<script type="text/javascript">
		var Gisborne = new google.maps.LatLng(
	        <?php echo get_post_meta(get_the_ID(),'location_lat',true); ?>,
	        <?php echo get_post_meta(get_the_ID(),'location_lang',true); ?>
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
	                  icon:'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png'
	            });
	        marker.setMap(map);
	    }
	    google.maps.event.addDomListener(window, 'load', initialize);
	</script>
	

<?php });