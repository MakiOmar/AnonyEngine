<?php
/**
 * Elementor custom fonts.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makiomar.com>
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/AnonyEngine.
 */

defined( 'ABSPATH' ) || die();

/**
 * Elementor custom fonts' class
 */
class Anony_Elementor_Custom_Fonts
{
    
    public function __construct($argument)
    {
        add_action( 'elementor/editor/wp_head', array( $this, 'editor_custom_fonts' ) );

        add_filter( 'elementor/fonts/additional_fonts', array( $this, 'custom_fonts' ) , 999);

        add_action( 'wp_head', array( $this, 'insert_font_face' ) );
    }

    public function editor_custom_fonts()
    {
        $custom_fonts = ANONY_Post_Help::queryPostTypeSimple( 'anony_fonts' );

        $font_faces = '';
        if ( !empty( $custom_fonts ) ) {

            foreach( $custom_fonts as $id => $title ) {
                $font_faces .= $this->render_font_face ( $id );
            }
        }

        if( !empty( $font_faces ) ) : ?>
            <style id="anony-editor-custom-fonts">
                <?php echo $font_faces ?>
            </style>
        <?php  endif;
    }

    public function get_font_family() {
        $anony_options = ANONY_Options_Model::get_instance();

        if ( !empty( $anony_options->anony_general_font ) ){

            $font_variations = get_post_meta( intval( $anony_options->anony_general_font ), 'anony_font_variations', true );

            if( empty( $font_variations[ 'font_family' ] ) ){

                $font_family = sanitize_title( get_the_title( intval( $anony_options->anony_general_font ) ) );

            }else{

                $font_family = sanitize_title( $font_variations[ 'font_family' ] );
            }
        }else{
            $font_family = 'Arial';
        }
        return $font_family;
    }

    public function render_font_face ( $post_id ) {
        $font_variations = get_post_meta( intval( $post_id ), 'anony_font_variations', true );
        $url = '';
        $font_face = false;
        if( $font_variations && !empty( $font_variations ) ) {
            $font_variations = array_map( 'intval', $font_variations );
            if( !empty( $font_variations[ 'eot' ] ) ){
                $eot_url = wp_get_attachment_url( $font_variations[ 'eot' ] );

                if ( $eot_url ) {
                    $url .= "url('{$eot_url}') format('embedded-opentype'),";
                }
            }

            if( !empty( $font_variations[ 'woff' ] ) ){
                $woff = wp_get_attachment_url( $font_variations[ 'woff' ] );

                if ( $woff ) {
                    $url .= "url('{$woff}') format('woff'),";
                }
            }


            if( !empty( $font_variations[ 'woff2' ] ) ){
                $woff2 = wp_get_attachment_url( $font_variations[ 'woff2' ] );

                if ( $woff2 ) {
                    $url .= "url('{$woff2}') format('woff2'),";
                }
            }

            if( !empty( $font_variations[ 'svg' ] ) ){
                $svg = wp_get_attachment_url( $font_variations[ 'svg' ] );

                if ( $svg ) {
                    $url .= "url('{$svg}') format('svg'),";
                }
            }

            if ( !empty( $url ) ){

                $url = rtrim( $url,  ',');

                if( empty( $font_variations[ 'font_family' ] ) ){
                    $font_family = sanitize_title( get_the_title( intval( $post_id ) ) );
                }else{
                    $font_family = sanitize_title( $font_variations[ 'font_family' ] );
                }


                $font_face = '@font-face{
                        font-family:"' . $font_family . '";
                        src:'. $url . ';
                        font-weight:normal;
                        font-style:normal;

                    }';
            }

        }

        return $font_face;

    }

    public function custom_fonts( $fonts ) {

        $custom_fonts = ANONY_Post_Help::queryPostTypeSimple( 'anony_fonts' );

        if ( !empty( $custom_fonts ) ) {
            foreach ( $custom_fonts as $id => $title ) {
                $fonts[ sanitize_title( $title ) ] = 'AnonyEngine';
            }
        }
        return $fonts;
    }

    function insert_font_face() {
        $anony_options = ANONY_Options_Model::get_instance();

        if ( !empty( $anony_options->anony_general_font ) ){

            $font_face = $this->render_font_face ( $anony_options->anony_general_font );

            if ( $font_face ) : ?>
                <style id="anony-custom-font">
                    <?php echo $font_face ?>
                </style>
            <?php endif;
        }
    }

}