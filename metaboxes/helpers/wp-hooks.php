<?php
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
        preg_match('/\[anony_shcode_rplaceit\]/', $content, $matches);
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