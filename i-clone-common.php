<?php

/**
 * Test if the user is allowed to copy posts
 */
function i_clone_is_current_user_allowed_to_copy() {
	return current_user_can('copy_posts');
}

/**
 * Test if post type is enable to be copied
 */
function i_clone_is_post_type_enabled($post_type){
	$i_clone_types_enabled = get_option('i_clone_types_enabled', array ('post', 'page'));
	if(!is_array($i_clone_types_enabled)) $i_clone_types_enabled = array($i_clone_types_enabled);
	return in_array($post_type, $i_clone_types_enabled);
}

/**
 * Wrapper for the option 'i_clone_create_user_level'
 */
function i_clone_get_copy_user_level() {
	return get_option( 'i_clone_copy_user_level' );
}

// Template tag
/**
 * Retrieve i clone link for post.
 *
 *
 * @param int $id Optional. Post ID.
 * @param string $context Optional, default to display. How to write the '&', defaults to '&amp;'.
 * @param string $draft Optional, default to true
 * @return string
 */
function i_clone_get_clone_post_link( $id = 0, $context = 'display', $draft = true ) {
	if ( !i_clone_is_current_user_allowed_to_copy() )
		return;

	if ( !$post = get_post( $id ) )
		return;
	
	if(!i_clone_is_post_type_enabled($post->post_type))
		return;

	if ($draft)
	$action_name = "i_clone_save_as_new_post_draft";
	else
	$action_name = "i_clone_save_as_new_post";

	if ( 'display' == $context )
	$action = '?action='.$action_name.'&amp;post='.$post->ID;
	else
	$action = '?action='.$action_name.'&post='.$post->ID;

	$post_type_object = get_post_type_object( $post->post_type );
	if ( !$post_type_object )
	return;

	return wp_nonce_url(apply_filters( 'i_clone_get_clone_post_link', admin_url( "admin.php". $action ), $post->ID, $context ), 'i-clone_' . $post->ID);
}
/**
 * Display i clone link for post.
 *
 * @param string $link Optional. Anchor text.
 * @param string $before Optional. Display before edit link.
 * @param string $after Optional. Display after edit link.
 * @param int $id Optional. Post ID.
 */
function i_clone_clone_post_link( $link = null, $before = '', $after = '', $id = 0 ) {
	if ( !$post = get_post( $id ) )
	return;

	if ( !$url = i_clone_get_clone_post_link( $post->ID ) )
	return;

	if ( null === $link )
	$link = esc_html__('Copy as a draft', 'i-clone');

	$link = '<a class="post-clone-link" href="' . $url . '" title="'
	. esc_attr__("Copy as a draft", 'i-clone')
	.'">' . $link . '</a>';
	echo $before . apply_filters( 'i_clone_clone_post_link', $link, $post->ID ) . $after;
}
/**
 * Get original post .
 *
 * @param int $post Optional. Post ID or Post object.
 * @param string $output Optional, default is Object. Either OBJECT, ARRAY_A, or ARRAY_N.
 * @return mixed Post data
 */
function i_clone_get_original($post = null , $output = OBJECT){
	if ( !$post = get_post( $post ) )
		return null;
	$original_ID = get_post_meta( $post->ID, '_ic_original');
	if (empty($original_ID)) return null;
	$original_post = get_post($original_ID[0],  $output);
	return $original_post;
}

function i_clone_get_edit_or_view_link( $post ){
	$post = get_post( $post );
	if ( ! $post )
		return null;

	$can_edit_post    = current_user_can( 'edit_post', $post->ID );
	$title            = _draft_or_post_title( $post );
	$post_type_object = get_post_type_object( $post->post_type );

	if ( $can_edit_post && 'trash' != $post->post_status ) {
		return sprintf(
			'<a href="%s" aria-label="%s">%s</a>',
			get_edit_post_link( $post->ID ),
			esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;', 'default' ), $title ) ),
			$title
		);
	} else if ( i_clone_is_post_type_viewable( $post_type_object ) ) {
		if ( in_array( $post->post_status, array( 'pending', 'draft', 'future' ) ) ) {
			if ( $can_edit_post ) {
				$preview_link    = get_preview_post_link( $post );
				return sprintf(
					'<a href="%s" rel="bookmark" aria-label="%s">%s</a>',
					esc_url( $preview_link ),
					esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;', 'default' ), $title ) ),
					$title
				);
			}
		} elseif ( 'trash' != $post->post_status ) {
			return sprintf(
				'<a href="%s" rel="bookmark" aria-label="%s">%s</a>',
				get_permalink( $post->ID ),
				/* translators: %s: post title */
				esc_attr( sprintf( __( 'View &#8220;%s&#8221;', 'default' ), $title ) ),
				$title
			);
		}
	}
	return $title;
}

/*
 * Workaround for is_post_type_viewable (introduced in WP 4.4)
 */
function i_clone_is_post_type_viewable( $post_type ) {
	if ( function_exists( 'is_post_type_viewable' ) ){
		return is_post_type_viewable( $post_type );
	} else {
		if ( is_scalar( $post_type ) ) {
			$post_type = get_post_type_object( $post_type );
			if ( ! $post_type ) {
				return false;
			}
		}
		return $post_type->publicly_queryable || ( $post_type->_builtin && $post_type->public );
	}
}

// Admin bar
function i_clone_admin_bar_render() {
	if(!is_admin_bar_showing()) return;
	global $wp_admin_bar;
	$current_object = get_queried_object();
	if ( !empty($current_object) ){
		if ( ! empty( $current_object->post_type )
			&& ( $post_type_object = get_post_type_object( $current_object->post_type ) )
			&& i_clone_is_current_user_allowed_to_copy()
			&& ( $post_type_object->show_ui || 'attachment' == $current_object->post_type )
			&& (i_clone_is_post_type_enabled($current_object->post_type) ) )
		{
			$wp_admin_bar->add_menu( array(
	        	'id' => 'new_draft',
	        	'title' => esc_attr__("Copy as a draft", 'i-clone'),
	        	'href' => i_clone_get_clone_post_link( $current_object->ID )
			) );	
		}
	} else if ( is_admin() && isset( $_GET['post'] )){
		$id = $_GET['post'];
		$post = get_post($id);
		if( !is_null($post) 
				&& i_clone_is_current_user_allowed_to_copy()
				&& i_clone_is_post_type_enabled($post->post_type)) {
					$wp_admin_bar->add_menu( array(
						'id' => 'new_draft',
						'title' => esc_attr__("Copy as a draft", 'i-clone'),
						'href' => i_clone_get_clone_post_link( $id )
					) );
		}
	}
}

function i_clone_add_css() {
	if(!is_admin_bar_showing()) return;
	$current_object = get_queried_object();
	if ( !empty($current_object) ){
		if ( ! empty( $current_object->post_type )
			&& ( $post_type_object = get_post_type_object( $current_object->post_type ) )
			&& i_clone_is_current_user_allowed_to_copy()
			&& ( $post_type_object->show_ui || 'attachment' == $current_object->post_type )
			&& (i_clone_is_post_type_enabled($current_object->post_type) ) )
		{
			wp_enqueue_style ( 'i-clone', plugins_url('/i-clone.css', __FILE__), array(), I_CLONE_CURRENT_VERSION );
		}
	} else if ( is_admin() && isset( $_GET['post'] )){
		$id = $_GET['post'];
		$post = get_post($id);
		if( !is_null($post)
				&& i_clone_is_current_user_allowed_to_copy()
				&& i_clone_is_post_type_enabled($post->post_type)) {
					wp_enqueue_style ( 'i-clone', plugins_url('/i-clone.css', __FILE__), array(), I_CLONE_CURRENT_VERSION );
				}
	}
}


add_action('init', 'i_clone_init');

function i_clone_init(){
	if (get_option ( 'i_clone_show_adminbar' ) == 1) {
		add_action ( 'wp_before_admin_bar_render', 'i_clone_admin_bar_render' );
		add_action ( 'wp_enqueue_scripts', 'i_clone_add_css' );
		add_action ( 'admin_enqueue_scripts', 'i_clone_add_css' );
	}
}

/**
 * Sort taxonomy objects: first public, then private
 */
function i_clone_tax_obj_cmp($a, $b) {
	return ($a->public < $b->public);
}