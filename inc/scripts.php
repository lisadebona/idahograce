<?php
/**
 * Enqueue scripts and styles.
 */
function idahograce_scripts() {
	wp_enqueue_style( 'idahograce-style', get_stylesheet_uri() );

	wp_deregister_script('jquery');
		wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js', false, '3.4.1', false);
		wp_enqueue_script('jquery');

	

	wp_enqueue_script( 
			'idahograce-blocks', 
			get_template_directory_uri() . '/assets/js/vendors.js', 
			array(), '20120206', 
			true 
		);

	wp_enqueue_script( 
			'idahograce-jscookie', 
			get_template_directory_uri() . '/assets/js/vendors/cookie.js', 
			array('jquery'), '', 
			true 
		);

	wp_enqueue_script( 
			'idahograce-custom', 
			get_template_directory_uri() . '/assets/js/custom.js', 
			array(), '20120206', 
			true 
		);

	wp_enqueue_script( 
		'font-awesome', 
		'https://use.fontawesome.com/8f931eabc1.js', 
		array(), '20180424', 
		true 
	);



	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'idahograce_scripts' );