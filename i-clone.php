<?php
/*
 Plugin Name: iClone
 Plugin URI: https://www.binarypoets.net/iClone/
 Description: Clone posts and pages. 
 Version: 1.0.0
 Author: BinaryPoets
 Author URI: https://www.binarypoets.net/
 Text Domain: i-clone
 */

/*  Copyright 2019-2020	Masum Hasan  (email : hello@binarypoets.net)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Version of the plugin
define('I_CLONE_CURRENT_VERSION', '1.0.0' );


/**
 * Initialise the internationalisation domain
 */
function i_clone_load_plugin_textdomain() {
    load_plugin_textdomain( 'i-clone', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'i_clone_load_plugin_textdomain' );


add_filter("plugin_action_links_".plugin_basename(__FILE__), "i_clone_plugin_actions", 10, 4);

function i_clone_plugin_actions( $actions, $plugin_file, $plugin_data, $context ) {
	array_unshift($actions,
		sprintf('<a href="%s" aria-label="%s">%s</a>',
			menu_page_url('iclone', false),
			esc_attr__( 'Settings for iClone', 'i-clone'),
			esc_html__("Settings", 'default')
		)
	);
	return $actions;
}

require_once (dirname(__FILE__).'/i-clone-common.php');

if (is_admin()){
	require_once (dirname(__FILE__).'/i-clone-admin.php');
}
