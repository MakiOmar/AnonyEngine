<?php //phpcs:disable
$form = array(
	'id'              => 'user_profile',
	'fields_layout'   => 'columns',
	'used_in'         => array(),// An array of objects IDs that the form will be used in.
	'form_attributes' => array(
		'action'  => '',
		'method'  => 'post',
		'enctype' => 'multipart/form-data',
	),
	'fields'          => array(
		array(
			'id'          => 'thumb',
			'validate'    => 'no_html',
			'type'        => 'uploader',
			'style'       => 'one',

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
		'Profile' => array(
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

		),
	),

	'conditions'      => array(
		'logged_in' => true,
		'user_role' => array( 'administrator', 'subscriber' ),
	),

);

$init = new ANONY_Create_Form($form);
