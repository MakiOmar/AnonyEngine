<?php 
/**
 * Post types as parent/child relation.
 *
 * PHP version 7.3 Or Later.
 *
 * @package  AnonyEngine
 * @author   Makiomar <info@makior.com>
 * @license  https:// makiomar.com AnonyEngine Licence.
 * @link     https:// makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

if ( ! class_exists( 'ANONY_Cross_Post_Type_Parent' ) ) {

    /**
     * A class to have post types as parent/child relation.
     *
     * PHP version 7.3 Or Later.
     *
     * @package  AnonyEngine
     * @author   Makiomar <info@makior.com>
     * @license  https:// makiomar.com AnonyEngine Licence.
     * @link     https:// makiomar.com/anonyengine
     */
    class ANONY_Cross_Post_Type_Parent {

        /**
         * Class constructor.
         * 
         * @param array $args Array of related posts as <dode>array( 'child' => 'child_post_type', 'parent' => 'parent_post_type' )</code>.
         */ 
        public function __construct( $args ){

            $this->parent = $args[ 'parent' ];
            $this->child = $args[ 'child' ];
            $this->cross_parent = array( $args[ 'child' ] => $args[ 'parent' ] );

            // Hook meta box to just the child post type.
            add_action( "add_meta_boxes_{$this->child}", array( $this, 'parent_meta_boxes' ) );

            // Set parent post type to be hierarchical.
            add_filter( "anony_{$this->parent}_hierarchical", '__return_true' );

            // Define required rewrite rules.
            add_action( "init", array( $this, 'anony_cross_parent_rewrite_cb' ) );

            // Filter child post permalink to have parent name.
            add_filter( 'post_type_link', array( $this, 'anony_cross_parent_permalink_cb' ), 10, 3 );

            add_action('admin_menu', function() {
                remove_meta_box('pageparentdiv', $this->child, 'normal');
            });

            add_filter( "anony_{$this->child}_supports", function ( $supports ) {

                $supports[] = 'page-attributes';

                return $supports;
            } );

        }

        /**
         * Post type's parent metabox
         * 
         * @param object $post Post type object.
         */ 
        public function parent_meta_boxes( $post ) {

            add_meta_box(
                "anony-{$this->child}-parent",
                esc_html__( 'Relation', 'anonyengine' ),
                array( $this, 'parent_meta_boxes_cb' ),
                $post->post_type,
                'side',
                'core'
            );
        }

        /**
         * Renders the meta box.
         * 
         * @param object $post Metabox's post type object.
         */ 
        public function parent_meta_boxes_cb( $post ) {

            $pages = wp_dropdown_pages(array('post_type' => $this->parent, 'selected' => $post->post_parent, 'name' => 'parent_id', 'show_option_none' => __('(no parent)'), 'sort_column'=> 'menu_order, post_title', 'echo' => 0));
                if ( ! empty($pages) ) {
                    echo $pages;
                } else {
                
                echo '<select name="parent_id" class="widefat">'; // !Important! Don't change the 'parent_id' name attribute.

                    echo '<option value="">Select Relation</option>';

                echo '</select>';       
                
            }
        }

        /**
         * Define new rewrite tag, permastruct and rewrite rule for cross parent post type.
         */
        public function anony_cross_parent_rewrite_cb(){
            /**
             * Should be array of post_type => parent_post_type
             */
            $post_parents = apply_filters( 'anony_cross_parent_rewrite', $this->cross_parent );

            if ( empty( $post_parents ) || ! is_array( $post_parents ) ) {
                return;
            }

            foreach ( $post_parents as $post_type => $parent_post_type ) {

                add_rewrite_tag( "%{$post_type}%", '([^/]+)', $post_type . '=' );

                add_permastruct( $post_type, '/' . $parent_post_type . '/%' . $parent_post_type . '%/%' . $post_type . '%', false );

                // add_permastruct( $post_type, "/{$post_type}/%{$post_type}%", false );

                add_rewrite_rule( '^' . $parent_post_type . '/([^/]+)/([^/]+)/?', 'index.php?' . $post_type . '=$matches[2]', 'top' );

            }
        }

        /**
         * Rewrite the permalink for cross parent post type.
         * 
         * @param string $permalink Post's permalink.
         * @param object $post Post's object.
         * @param bool $leavename   Whether to keep the post name..
         * @return string Post's permalink
         */
        public function anony_cross_parent_permalink_cb ( $permalink, $post, $leavename ) {

            /**
             * Should be array of post_type => parent_post_type
             */
            $post_parents = apply_filters( 'anony_cross_parent_permalink', $this->cross_parent );

            if ( empty( $post_parents ) || ! is_array( $post_parents ) ) {
                return $permalink;
            }

            if ( ! in_array( $post->post_type, array_keys( $post_parents ) ) || empty( $permalink ) || in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft' ) ) ) {
                return $permalink;
            }

            $parent_post_type = $post_parents[ $post->post_type ];

            $parent = $post->post_parent;

            $parent_post = get_post( $parent );

            $permalink = str_replace( '%' . $parent_post_type . '%', $parent_post->post_name, $permalink );

            return $permalink;
        }

    }    

}