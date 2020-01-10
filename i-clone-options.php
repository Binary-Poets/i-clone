<?php
/**
 * Add an option page
 */
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_admin() ){ // admin actions
	add_action( 'admin_menu', 'i_clone_menu' );
	add_action( 'admin_init', 'i_clone_register_settings' );
}

function i_clone_register_settings() { // whitelist options

	register_setting( 'i_clone_group', 'i_clone_blacklist');
	register_setting( 'i_clone_group', 'i_clone_taxonomies_blacklist');
	register_setting( 'i_clone_group', 'i_clone_title_prefix');
	register_setting( 'i_clone_group', 'i_clone_title_suffix');
	register_setting( 'i_clone_group', 'i_clone_increase_menu_order_by');
	register_setting( 'i_clone_group', 'i_clone_roles');
	register_setting( 'i_clone_group', 'i_clone_types_enabled');
	register_setting( 'i_clone_group', 'i_clone_show_row');
	register_setting( 'i_clone_group', 'i_clone_show_adminbar');
	register_setting( 'i_clone_group', 'i_clone_show_submitbox');
	register_setting( 'i_clone_group', 'i_clone_show_bulkactions');
	register_setting( 'i_clone_group', 'i_clone_show_original_column');
	register_setting( 'i_clone_group', 'i_clone_show_original_in_post_states');
	register_setting( 'i_clone_group', 'i_clone_show_original_meta_box');
	register_setting( 'i_clone_group', 'i_clone_show_notice');
	register_setting( 'i_clone_group', 'i_clone_copytitle');
	register_setting( 'i_clone_group', 'i_clone_copydate');
	register_setting( 'i_clone_group', 'i_clone_copystatus');
	register_setting( 'i_clone_group', 'i_clone_copyslug');
	register_setting( 'i_clone_group', 'i_clone_copyexcerpt');
	register_setting( 'i_clone_group', 'i_clone_copycontent');
	register_setting( 'i_clone_group', 'i_clone_copythumbnail');
	register_setting( 'i_clone_group', 'i_clone_copytemplate');
	register_setting( 'i_clone_group', 'i_clone_copyformat');
	register_setting( 'i_clone_group', 'i_clone_copyauthor');
	register_setting( 'i_clone_group', 'i_clone_copypassword');
	register_setting( 'i_clone_group', 'i_clone_copyattachments');
	register_setting( 'i_clone_group', 'i_clone_copychildren');
	register_setting( 'i_clone_group', 'i_clone_copycomments');
	register_setting( 'i_clone_group', 'i_clone_copymenuorder');
}


function i_clone_menu() {
	add_options_page(__("iClone Options", 'i-clone'), __("iClone", 'i-clone'), 'manage_options', 'iclone', 'i_clone_options');
}

function i_clone_options() {

	if ( current_user_can( 'promote_users' ) && (isset($_GET['settings-updated'])  && $_GET['settings-updated'] == true)){
		global $wp_roles;
		$roles = $wp_roles->get_names();

		$ic_roles = get_option('i_clone_roles');
		if ( $ic_roles == "" ) $ic_roles = array();

		foreach ($roles as $name => $display_name){
			$role = get_role($name);

			/* If the role doesn't have the capability and it was selected, add it. */
			if ( !$role->has_cap( 'copy_posts' )  && in_array($name, $ic_roles) )
				$role->add_cap( 'copy_posts' );

			/* If the role has the capability and it wasn't selected, remove it. */
			elseif ( $role->has_cap( 'copy_posts' ) && !in_array($name, $ic_roles) )
			$role->remove_cap( 'copy_posts' );
		}
	}
	?>
<div class="wrap">
	<div id="icon-options-general" class="icon32">
		<br>
	</div>
	<h1>
		<?php esc_html_e("iClone Options", 'i-clone'); ?>
	</h1>
	
	<div
		style="display: flex; align-items: center; margin: 9px 15px 4px 0; padding: 5px 30px; float: left; clear:left; border: solid 3px #cccccc; width: 600px;">
	<img src="../wp-content/plugins/i-clone/iclone.svg" alt="iClone">
		<div>
		<p>
			<?php esc_html_e('Serving the WordPress community since 2019.', 'i-clone'); ?>
			<br/>
			<strong><a href="https://www.binarypoets.net/iClone/donate/"><?php esc_html_e('Buy us a cup of coffee!', 'i-clone'); ?></a></strong>
		</p>
		<p>
			<a href="https://www.binarypoets.net/iClone/" aria-label="<?php esc_attr_e('Documentation for iClone', 'i-clone'); ?>"><?php esc_html_e('Documentation', 'i-clone'); ?></a>
			 - <a href="https://translate.wordpress.org/projects/wp-plugins/i-clone" aria-label="<?php esc_attr_e('Translate iClone', 'i-clone'); ?>"><?php esc_html_e('Translate', 'i-clone'); ?></a>
			 - <a href="https://wordpress.org/support/plugin/i-clone" aria-label="<?php esc_attr_e('Support forum for iClone', 'i-clone'); ?>"><?php esc_html_e('Support Forum', 'i-clone'); ?></a>
		</p>
		</div>
	</div>
		

	<script>
		var tablist;
		var tabs;
		var panels;

		// For easy reference
		var keys = {
			end: 35,
			home: 36,
			left: 37,
			up: 38,
			right: 39,
			down: 40,
			delete: 46
		};

		// Add or substract depending on key pressed
		var direction = {
			37: -1,
			38: -1,
			39: 1,
			40: 1
		};


		function generateArrays () {
			tabs = document.querySelectorAll('#i_clone_settings_form [role="tab"]');
			panels = document.querySelectorAll('#i_clone_settings_form [role="tabpanel"]');
		};

		function addListeners (index) {
			tabs[index].addEventListener('click', function(event){
				var tab = event.target;
				activateTab(tab, false);
			});
			tabs[index].addEventListener('keydown', function(event) {
				var key = event.keyCode;

				switch (key) {
					case keys.end:
						event.preventDefault();
						// Activate last tab
						activateTab(tabs[tabs.length - 1]);
						break;
					case keys.home:
						event.preventDefault();
						// Activate first tab
						activateTab(tabs[0]);
						break;
				};
			});
			tabs[index].addEventListener('keyup', function(event) {
				var key = event.keyCode;

				switch (key) {
					case keys.left:
					case keys.right:
						switchTabOnArrowPress(event);
						break;
				};
			});

			// Build an array with all tabs (<button>s) in it
			tabs[index].index = index;
		};


		// Either focus the next, previous, first, or last tab
		// depening on key pressed
		function switchTabOnArrowPress (event) {
			var pressed = event.keyCode;

			for (x = 0; x < tabs.length; x++) {
				tabs[x].addEventListener('focus', focusEventHandler);
			};

			if (direction[pressed]) {
				var target = event.target;
				if (target.index !== undefined) {
					if (tabs[target.index + direction[pressed]]) {
						tabs[target.index + direction[pressed]].focus();
					}
					else if (pressed === keys.left || pressed === keys.up) {
						focusLastTab();
					}
					else if (pressed === keys.right || pressed == keys.down) {
						focusFirstTab();
					};
				};
			};
		};

		// Activates any given tab panel
		function activateTab (tab, setFocus) {
			setFocus = setFocus || true;
			// Deactivate all other tabs
			deactivateTabs();

			// Remove tabindex attribute
			tab.removeAttribute('tabindex');

			// Set the tab as selected
			tab.setAttribute('aria-selected', 'true');

			tab.classList.add('nav-tab-active');

			// Get the value of aria-controls (which is an ID)
			var controls = tab.getAttribute('aria-controls');

			// Remove hidden attribute from tab panel to make it visible
			document.getElementById(controls).removeAttribute('hidden');

			// Set focus when required
			if (setFocus) {
				tab.focus();
			};
		};

		// Deactivate all tabs and tab panels
		function deactivateTabs () {
			for (t = 0; t < tabs.length; t++) {
				tabs[t].setAttribute('tabindex', '-1');
				tabs[t].setAttribute('aria-selected', 'false');
				tabs[t].classList.remove('nav-tab-active');
				tabs[t].removeEventListener('focus', focusEventHandler);
			};

			for (p = 0; p < panels.length; p++) {
				panels[p].setAttribute('hidden', 'hidden');
			};
		};

		// Make a guess
		function focusFirstTab () {
			tabs[0].focus();
		};

		// Make a guess
		function focusLastTab () {
			tabs[tabs.length - 1].focus();
		};

		//
		function focusEventHandler (event) {
			var target = event.target;

			checkTabFocus(target);
		};

		// Only activate tab on focus if it still has focus after the delay
		function checkTabFocus (target) {
			focused = document.activeElement;

			if (target === focused) {
				activateTab(target, false);
			};
		};

		document.addEventListener("DOMContentLoaded", function () {
			tablist = document.querySelectorAll('#i_clone_settings_form [role="tablist"]')[0];

			generateArrays();

			// Bind listeners
			for (i = 0; i < tabs.length; ++i) {
				addListeners(i);
			};


		});

	function toggle_private_taxonomies(){
		jQuery('.taxonomy_private').toggle(300);
	}

	
	jQuery(function(){
		jQuery('.taxonomy_private').hide(300);
	});
	
	</script>

	<style>
header.nav-tab-wrapper {
	margin: 22px 0 0 0;
}

header .nav-tab:focus {
	color: #555;
	box-shadow: none;
}

#sections {
	padding: 22px;
	background: #fff;
	border: 1px solid #ccc;
	border-top: 0px;
}
/*
section {
	display: none;
}

section:first-of-type {
	display: block;
}*/

.no-js header.nav-tab-wrapper {
	display: none;
}

.no-js #sections {
	border-top: 1px solid #ccc;
	margin-top: 22px;
}

.no-js section {
	border-top: 1px dashed #aaa;
	margin-top: 22px;
	padding-top: 22px;
}

.no-js section:first-child {
	margin: 0px;
	padding: 0px;
	border: 0px;
}

label {
	display: block;
}

label.taxonomy_private {
	font-style: italic;
}

a.toggle_link {
	font-size: small;
}
img#donate-button{
	vertical-align: middle;
}
</style>


	<form method="post" action="options.php" style="clear: both" id="i_clone_settings_form">
		<?php settings_fields('i_clone_group'); ?>

		<header role="tablist" aria-label="<?php esc_attr_e('Settings sections', 'i-clone'); ?>" class="nav-tab-wrapper">
			<button
					type="button"
					role="tab"
					class="nav-tab nav-tab-active"
					aria-selected="true"
					aria-controls="what-tab"
					id="what"><?php esc_html_e('What to copy', 'i-clone'); ?>
			</button>
			<button
					type="button"
					role="tab"
					class="nav-tab"
					aria-selected="false"
					aria-controls="who-tab"
					id="who"
					tabindex="-1"><?php esc_html_e('Permissions', 'i-clone'); ?>
			</button>
			<button
					type="button"
					role="tab"
					class="nav-tab"
					aria-selected="false"
					aria-controls="where-tab"
					id="where"
					tabindex="-1"><?php esc_html_e('Display', 'i-clone'); ?>
			</button>
		</header>

		<section
				tabindex="0"
				role="tabpanel"
				id="what-tab"
				aria-labelledby="what">

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php esc_html_e('Post/page elements to copy', 'i-clone'); ?>
					</th>
					<td colspan="2"><label> <input type="checkbox"
							name="i_clone_copytitle" value="1" <?php  if(get_option('i_clone_copytitle') == 1) echo 'checked="checked"'; ?>/>
							<?php esc_html_e("Title", 'default'); ?>
					</label> <label> <input type="checkbox"
							name="i_clone_copydate" value="1" <?php  if(get_option('i_clone_copydate') == 1) echo 'checked="checked"'; ?>/>
							<?php esc_html_e("Date", 'default'); ?>
					</label> <label> <input type="checkbox"
							name="i_clone_copystatus" value="1" <?php  if(get_option('i_clone_copystatus') == 1) echo 'checked="checked"'; ?>/>
							<?php esc_html_e("Status", 'default'); ?>
					</label> <label> <input type="checkbox"
							name="i_clone_copyslug" value="1" <?php  if(get_option('i_clone_copyslug') == 1) echo 'checked="checked"'; ?>/>
							<?php esc_html_e("Slug", 'default'); ?>
					</label> <label> <input type="checkbox"
							name="i_clone_copyexcerpt" value="1" <?php  if(get_option('i_clone_copyexcerpt') == 1) echo 'checked="checked"'; ?>/>
							<?php esc_html_e("Excerpt", 'default'); ?>
					</label> <label> <input type="checkbox"
							name="i_clone_copycontent" value="1" <?php  if(get_option('i_clone_copycontent') == 1) echo 'checked="checked"'; ?>/>
							<?php esc_html_e("Content", 'default'); ?>
					</label> <label> <input type="checkbox"
							name="i_clone_copythumbnail" value="1" <?php  if(get_option('i_clone_copythumbnail') == 1) echo 'checked="checked"'; ?>/>
							<?php esc_html_e("Featured Image", 'default'); ?>
					</label> <label> <input type="checkbox"
							name="i_clone_copytemplate" value="1" <?php  if(get_option('i_clone_copytemplate') == 1) echo 'checked="checked"'; ?>/>
							<?php esc_html_e("Template", 'default'); ?>
					</label> <label> <input type="checkbox"
							name="i_clone_copyformat" value="1" <?php  if(get_option('i_clone_copyformat') == 1) echo 'checked="checked"'; ?>/>
							<?php echo esc_html_x("Format", 'post format', 'default'); ?>																					
					</label> <label> <input type="checkbox"
							name="i_clone_copyauthor" value="1" <?php  if(get_option('i_clone_copyauthor') == 1) echo 'checked="checked"'; ?>/>
							<?php esc_html_e("Author", 'default'); ?>
					</label> <label> <input type="checkbox"
							name="i_clone_copypassword" value="1" <?php  if(get_option('i_clone_copypassword') == 1) echo 'checked="checked"'; ?>/>
							<?php esc_html_e("Password", 'default'); ?>
					</label> <label> <input type="checkbox"
							name="i_clone_copyattachments" value="1" <?php  if(get_option('i_clone_copyattachments') == 1) echo 'checked="checked"'; ?>/>
							<?php esc_html_e("Attachments", 'i-clone');  ?> <span class="description">(<?php esc_html_e("We recommend this to be unchecked unless you know what you are really doing.", 'i-clone');  ?>)</span>
					</label> <label> <input type="checkbox"
							name="i_clone_copychildren" value="1" <?php  if(get_option('i_clone_copychildren') == 1) echo 'checked="checked"'; ?>/>
							<?php esc_html_e("Children", 'i-clone');  ?>
					</label> <label> <input type="checkbox"
							name="i_clone_copycomments" value="1" <?php  if(get_option('i_clone_copycomments') == 1) echo 'checked="checked"'; ?>/>
							<?php esc_html_e("Comments", 'default');  ?> <span class="description">(<?php esc_html_e("except pingbacks and trackbacks", 'i-clone');  ?>)</span>
					</label> <label> <input type="checkbox"
							name="i_clone_copymenuorder" value="1" <?php  if(get_option('i_clone_copymenuorder') == 1) echo 'checked="checked"'; ?>/>
							<?php esc_html_e("Menu order", 'default');  ?>
					</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="i_clone_title_prefix">
							<?php esc_html_e("Title prefix", 'i-clone'); ?>
						</label>
					</th>
					<td><input type="text" name="i_clone_title_prefix" id="i_clone_title_prefix"
						value="<?php form_option('i_clone_title_prefix'); ?>" />
					</td>
					<td><span class="description"><?php esc_html_e("Prefix to be added before the title, e.g. \"Copy of\" (blank for no prefix)", 'i-clone'); ?>
					</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="i_clone_title_suffix">
							<?php esc_html_e("Title suffix", 'i-clone'); ?>
						</label>
					</th>
					<td><input type="text" name="i_clone_title_suffix" id="i_clone_title_suffix"
						value="<?php form_option('i_clone_title_suffix'); ?>" />
					</td>
					<td><span class="description"><?php esc_html_e("Suffix to be added after the title, e.g. \"(dup)\" (blank for no suffix)", 'i-clone'); ?>
					</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="i_clone_increase_menu_order_by">
							<?php esc_html_e("Increase menu order by", 'i-clone'); ?>
						</label>
					</th>
					<td><input type="text" name="i_clone_increase_menu_order_by" id="i_clone_increase_menu_order_by"
						value="<?php form_option('i_clone_increase_menu_order_by'); ?>" />
					</td>
					<td><span class="description"><?php esc_html_e("Add this number to the original menu order (blank or zero to retain the value)", 'i-clone'); ?>
					</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="i_clone_blacklist">
							<?php esc_html_e("Do not copy these fields", 'i-clone'); ?>
						</label>
					</th>
					<td id="textfield"><input type="text"
						name="i_clone_blacklist"
					  	id="i_clone_blacklist"
						value="<?php form_option('i_clone_blacklist'); ?>" /></td>
					<td><span class="description"><?php esc_html_e("Comma-separated list of meta fields that must not be copied", 'i-clone'); ?><br />
							<small><?php esc_html_e("You can use * to match zero or more alphanumeric characters or underscores: e.g. field*", 'i-clone'); ?>
						</small> </span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e("Do not copy these taxonomies", 'i-clone'); ?><br />
						<a class="toggle_link" href="#"
						onclick="toggle_private_taxonomies();return false;"><?php esc_html_e('Show/hide private taxonomies', 'i-clone');?>
					</a>
					</th>
					<td colspan="2"><?php $taxonomies=get_taxonomies(array(),'objects'); usort($taxonomies, 'i_clone_tax_obj_cmp');
					$taxonomies_blacklist = get_option('i_clone_taxonomies_blacklist');
					if ($taxonomies_blacklist == "") $taxonomies_blacklist = array();
					foreach ($taxonomies as $taxonomy ) : 
						if($taxonomy->name == 'post_format'){
							continue;
						}
						?> <label
						class="taxonomy_<?php echo ($taxonomy->public)?'public':'private';?>">
							<input type="checkbox"
							name="i_clone_taxonomies_blacklist[]"
							value="<?php echo $taxonomy->name?>"
							<?php if(in_array($taxonomy->name, $taxonomies_blacklist)) echo 'checked="checked"'?> />
							<?php echo $taxonomy->labels->name.' ['.$taxonomy->name.']'; ?>
					</label> <?php endforeach; ?> <span class="description"><?php esc_html_e("Select the taxonomies you don't want to be copied", 'i-clone'); ?>
					</span>
					</td>
				</tr>
			</table>
		</section>
		<section
				tabindex="0"
				role="tabpanel"
				id="who-tab"
				aria-labelledby="who"
				hidden="hidden">
			<table class="form-table">
				<?php if ( current_user_can( 'promote_users' ) ){ ?>
				<tr valign="top">
					<th scope="row"><?php esc_html_e("Roles allowed to copy", 'i-clone'); ?>
					</th>
					<td><?php	global $wp_roles;
					$roles = $wp_roles->get_names();
					$post_types = get_post_types( array( 'show_ui' => true ), 'objects' );
					$edit_capabilities = array('edit_posts' => true);
					foreach( $post_types as $post_type ) {
						$edit_capabilities[$post_type->cap->edit_posts] = true;
					}
					foreach ( $roles as $name => $display_name ):
						$role = get_role( $name );
						if( count ( array_intersect_key( $role->capabilities, $edit_capabilities ) ) > 0 ): ?>
					<label> <input
							type="checkbox" name="i_clone_roles[]"
							value="<?php echo $name ?>"
							<?php if($role->has_cap('copy_posts')) echo 'checked="checked"'?> />
							<?php echo translate_user_role($display_name); ?>
					</label> <?php endif; endforeach; ?> <span class="description"><?php esc_html_e("Warning: users will be able to copy all posts, even those of other users", 'i-clone'); ?><br />
							<?php esc_html_e("Passwords and contents of password-protected posts may become visible to undesired users and visitors", 'i-clone'); ?>
					</span>
					</td>
				</tr>
				<?php } ?>
				<tr valign="top">
					<th scope="row"><?php esc_html_e("Enable for these post types", 'i-clone'); ?>
					</th>
					<td><?php $post_types = get_post_types(array('show_ui' => true),'objects');
					foreach ($post_types as $post_type_object ) :
					if ($post_type_object->name == 'attachment') continue; ?> <label> <input
							type="checkbox" name="i_clone_types_enabled[]"
							value="<?php echo $post_type_object->name?>"
							<?php if(i_clone_is_post_type_enabled($post_type_object->name)) echo 'checked="checked"'?> />
							<?php echo $post_type_object->labels->name?>
					</label> <?php endforeach; ?> <span class="description"><?php esc_html_e("Select the post types you want the plugin to be enabled", 'i-clone'); ?>
							<br /> <?php esc_html_e("Whether the links are displayed for custom post types registered by themes or plugins depends on their use of standard WordPress UI elements", 'i-clone'); ?>
					</span>
					</td>
				</tr>
			</table>
		</section>
		<section
				tabindex="0"
				role="tabpanel"
				id="where-tab"
				aria-labelledby="where"
				hidden="hidden">
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php esc_html_e("Show links in", 'i-clone'); ?>
					</th>
					<td><label><input type="checkbox" name="i_clone_show_row"
							value="1" <?php  if(get_option('i_clone_show_row') == 1) echo 'checked="checked"'; ?>/>
							<?php esc_html_e("Post list", 'i-clone'); ?> </label>
							<label><input type="checkbox" name="i_clone_show_submitbox" value="1" <?php  if(get_option('i_clone_show_submitbox') == 1) echo 'checked="checked"'; ?>/>
							<?php esc_html_e("Edit screen", 'i-clone'); ?> </label>
							<label><input type="checkbox" name="i_clone_show_adminbar" value="1" <?php  if(get_option('i_clone_show_adminbar') == 1) echo 'checked="checked"'; ?>/>
							<?php esc_html_e("Admin bar", 'i-clone'); ?> <span class="description">(<?php esc_html_e("now works on Edit screen too - check this option to use with Gutenberg enabled", 'i-clone');  ?>)</span></label>
							<?php global $wp_version;
							if( version_compare($wp_version, '4.7') >= 0 ){ ?>
							<label><input type="checkbox" name="i_clone_show_bulkactions" value="1" <?php  if(get_option('i_clone_show_bulkactions') == 1) echo 'checked="checked"'; ?>/>
							<?php esc_html_e("Bulk Actions", 'default'); ?> </label>
							<?php } ?>												
					</td>
				</tr>
				<tr valign="top">
					<td colspan="2"><span class="description"><?php esc_html_e("Whether the links are displayed for custom post types registered by themes or plugins depends on their use of standard WordPress UI elements", 'i-clone'); ?>
							<br /> <?php printf(__('You can also use the template tag i_clone_clone_post_link( $link, $before, $after, $id ). You can find more info about this on the <a href="%s">developer&apos;s guide for iClone</a>', 'i-clone'), 'https://www.binarypoets.net/iClone/'); ?>
					</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e("Show original item:", 'i-clone'); ?></th>
					<td>
						<label>
							<input type="checkbox" name="i_clone_show_original_meta_box"
							   value="1" <?php  if(get_option('i_clone_show_original_meta_box') == 1) echo 'checked="checked"'; ?>/>
							<?php esc_html_e("In a metabox in the Edit screen [Classic editor]", 'i-clone'); ?>
							<span class="description">(<?php esc_html_e("you'll also be able to delete the reference to the original item with a checkbox", 'i-clone');  ?>)</span>
						</label>
						<label>
							<input type="checkbox" name="i_clone_show_original_column"
							   value="1" <?php  if(get_option('i_clone_show_original_column') == 1) echo 'checked="checked"'; ?>/>
							<?php esc_html_e("In a column in the Post list", 'i-clone'); ?>
							<span class="description">(<?php esc_html_e("you'll also be able to delete the reference to the original item with a checkbox in Quick Edit", 'i-clone');  ?>)</span>
						</label>
						<label>
							<input type="checkbox" name="i_clone_show_original_in_post_states"
								   value="1" <?php  if(get_option('i_clone_show_original_in_post_states') == 1) echo 'checked="checked"'; ?>/>
							<?php esc_html_e("After the title in the Post list", 'i-clone'); ?>
						</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="i_clone_show_notice">
							<?php esc_html_e("Show update notice", 'i-clone'); ?>
						</label>
					</th>
					<td><input type="checkbox" name="i_clone_show_notice" id="i_clone_show_notice"
							value="1" <?php  if(get_option('i_clone_show_notice') == 1) echo 'checked="checked"'; ?>/>
					</td>
				</tr>				
			</table>
		</section>
		<p class="submit">
			<input type="submit" class="button-primary"
				value="<?php esc_html_e('Save Changes', 'i-clone') ?>" />
		</p>

	</form>
</div>
<?php
}
?>