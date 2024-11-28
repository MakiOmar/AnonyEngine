<?php //phpcs:disable
$form = array(
	'id'              => 'user_insert_profile',
	'used_in'         => array(),// An array of objects IDs that the form will be used in.
	'fields_layout'   => 'columns',
	'form_attributes' => array(
		'action'  => '',
		'method'  => 'post',
		'enctype' => 'multipart/form-data',
	),
	'fields'          => array(
		array(
			'id'       => 'thumb2',
			'validate' => 'no_html',
			'type'     => 'uploader',
			'style'    => 'two',

		),

		array(
			'id'           => 'category',
			'validate'     => 'no_html',
			'type'         => 'select',
			'options'      => ANONY_TERM_HELP::wp_top_level_term_query( 'provider_categories', 'id=>name' ),
			'first_option' => 'القسم',
			'on_change'    => array(
				'target' => 'sub_category', // Field ID to be affected after this field change.
				'action' => 'get_term_children_options', // ajax action to trigger. Predefined actions ( get_term_children_options )
				'data'   => array(
					'taxonomy' => 'provider_categories',
				), // Data sent with ajax request
			),

		),

		array(
			'id'           => 'sub_category',
			'validate'     => 'no_html',
			'type'         => 'select',
			'options'      => array(),
			'first_option' => 'القسم الفرعي',
		),

		array(
			'id'          => 'title',
			'placeholder' => 'إسم العمل',
			'validate'    => 'no_html',
			'type'        => 'text',

		),


		array(
			'id'          => 'location',
			'placeholder' => 'العنوان',
			'validate'    => 'no_html',
			'type'        => 'location',
		),
		array(
			'id'              => 'phone',
			'placeholder'     => 'رقم الهاتف',
			'validate'        => 'no_html',
			'type'            => 'tel',
			'with-dial-codes' => 'yes',
		),

		array(
			'id'              => 'second_phone',
			'placeholder'     => 'رقم هاتف آخر',
			'validate'        => 'no_html',
			'type'            => 'tel',
			'with-dial-codes' => 'yes',
		),

		array(
			'id'              => 'whatsapp',
			'placeholder'     => 'واتساب',
			'validate'        => 'no_html',
			'type'            => 'tel',
			'with-dial-codes' => 'yes',
		),

		array(
			'id'          => 'website',
			'placeholder' => 'الموقع الآلكتروني',
			'type'        => 'url',
			'validate'    => 'no_html',
		),

		array(
			'id'          => 'facebook',
			'placeholder' => 'صفحة الفيسبوك',
			'type'        => 'url',
			'validate'    => 'no_html',
		),

		array(
			'id'          => 'instagram',
			'placeholder' => 'صفحة إنستاجرام',
			'type'        => 'url',
			'validate'    => 'no_html',
		),

		array(
			'id'          => 'description',
			'placeholder' => 'الوصف',
			'type'        => 'textarea',
			'validate'    => 'no_html',
		),
	),
	'action_list'     => array(
		'Update_post' => array(
			'post_data' => array(
				'post_title'   => '#title', // This field will map to the field input of name title;
				'post_status'  => 'pending', // This field will equal to this value;
				'post_type'    => 'providers',
				'post_content' => '#description',
			),
			'meta'      => array(
				'location'      => '#location',
				'phone'         => '#phone',
				'second_phone'  => '#second_phone',
				'whatsapp'      => '#whatsapp',
				'website'       => '#website',
				'facebook'      => '#facebook',
				'instagram'     => '#instagram',
				'_thumbnail_id' => '#thumb',
			),
			// As taxonomy => #field_id.
			'tax_query' => array(

				'provider_categories' => array( '#category', '#sub_category' ),

			),

		),
	),

	'conditions'      => array(
		'logged_in' => true,
		'user_role' => array( 'administrator', 'subscriber' ),
	),
	'defaults'        => array(
		'object_type'    => 'post', // Accepts post, term, user
		'object_id_from' => 'query_variable', // Accepts current_user, current_term, current_post , query_variable or shortcode_attr
		'query_variable' => '_post_id',
	),
);

$init = new ANONY_Create_Form( $form );
