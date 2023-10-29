<?php


$form = array( 
    'id'              => 'my_first_form', 
    'fields_layout'   => 'columns' ,
    'form_attributes' => array(
        'action' => '',
        'method' => 'post',
        'enctype' => 'multipart/form-data',
    ) ,
    'fields' => array( 
        array(
            'id'          => 'title',
            'placeholder' => 'title',
            'validate'    => 'no_html',
            'type'        => 'text',
        ),
        array(
            'id'          => 'meta_one',
            'placeholder' => 'Meta one',
            'validate'    => 'no_html',
            'type'        => 'text',
        ),
        array(
            'id'          => 'meta_two',
            'placeholder' => 'Meta two',
            'validate'    => 'no_html',
            'type'        => 'text',
        ),
        array(
            'id'       => 'thumb',
            'title'    => esc_html__( 'Thumb', 'smartpage' ),
            'type'     => 'uploader',
            'validate' => 'no_html',
        ),
        array(
            'id'       => 'image',
            'title'    => esc_html__( 'image', 'smartpage' ),
            'type'     => 'uploader',
            'validate' => 'no_html',
        ),

        array(
            'id'       => 'gallery',
            'title'    => esc_html__( 'gallery', 'smartpage' ),
            'type'     => 'gallery',
            'validate' => 'no_html',
        ),

    ),
    'action_list' => array(
        'Insert_Post' => array(
            'post_data' => array(
                'post_title'  => '#title', // This field will map to the field input of name title;
                'post_status' => 'publish', // This field will equal to this value;
                'post_type'   => 'post',
            ),
            'meta' => array(
                'meta1' => '#meta_one', // # refers to form field, If no # it will be considered a direct value
                'meta2' => '#meta_two',
                '_thumbnail_id' => '#thumb',
                'image' => '#image',
                'gallery' => '#gallery',
            )
            
        )
    ),

    'conditions' => array(
        'logged_in' => true,
        'user_role' => array('administrator','subscriber'),
    )
);

$init = new ANONY_Create_Form($form);