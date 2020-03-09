<?php
add_action( 'admin_init', 'i_clone_jetpack_init' );


function i_clone_jetpack_init() {
	add_filter('i_clone_blacklist_filter', 'i_clone_jetpack_add_to_blacklist', 10, 1 );
	
	if (class_exists('WPCom_Markdown')){
		add_action('i_clone_pre_copy', 'i_clone_jetpack_disable_markdown', 10);
		add_action('i_clone_post_copy', 'i_clone_jetpack_enable_markdown', 10);
	}	
}

function i_clone_jetpack_add_to_blacklist($meta_blacklist) {
	$meta_blacklist[] = '_wpas*'; //Jetpack Publicize
	$meta_blacklist[] = '_publicize*'; //Jetpack Publicize
	
	$meta_blacklist[] = '_jetpack*'; //Jetpack Subscriptions etc.
	
	return $meta_blacklist;
}

// Markdown
function i_clone_jetpack_disable_markdown(){
	WPCom_Markdown::get_instance()->unload_markdown_for_posts();
}

function i_clone_jetpack_enable_markdown(){
	WPCom_Markdown::get_instance()->load_markdown_for_posts();
}