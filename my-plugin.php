<?php 

/*
Plugin Name: My Plugin
Plugin URI: http://wwproject.eu/plugins/my-plugin
Description: My brand new shiny Wordpress plugin
Version: 1.0
Author: Val Wroblewski
Author URI: http://wwproject.eu
Licence: GPL2 
*/

/* !0 TABLE OF CONTENTS	*/

/*
		1. HOOKS
				1.1 - register all shortcodes

		2. SHORTCODES
 				2.1 - my_plugin_register_shortcodes()
 				2.2 - my_plugin_form_shortcode()

		3. FILTERS

		4. EXTERNAL SCRIPTS

		5. ACTIONS

 		6. HELPERS
	
		7. CUSTOM POST TYPES

		8. ADMIN PAGES

		9. SETTINGS

		10. MISC

*/


/* !1. HOOKS */
// 1.1
add_action('init', 'my_plugin_register_shortcodes');

/* !2. SHORTCODES */
// 2.1
function my_plugin_register_shortcodes() {
	
	add_shortcode('my_plugin_form', 'my_plugin_form_shortcode');
}


// 2.2
function my_plugin_form_shortcode( $args, $content="" ) {
	

	
	// setup output variable - the html form
	$output='
		<div class="my-plugin">
			<form id="my_plugin_form" name="my_plugin_form" class="my-plugin-form" method="post">';
		
				// including content in form
			if( strlen($content) ):
				$output .= '<div class="my-plugin-content">' . wpautop($content) . '</div>';
			endif;
	
			$output .= '<p class="my-plugin-input-container">
					<label>Your Name</label><br />
					<input type="text" name="my_plugin_fname" placeholder="First Name" />
					<input type="text" name="my_plugin_lname" placeholder="Last Name" />
				</p>
				
				<p class="my-plugin-input-container">
					<label>Email address</label><br />
					<input type="email" name="my_plugin_email" placeholder="example@email.com" />
				</p>
								
				<p class="my-plugin-input-container">
					<input type="submit" name="my_plugin_submit" placeholder="Sign Me Up!" />
				</p>
			</form>
		</div>
	';
	
	// return results
	return $output;	
	
}

/* MISC */
// 1.1

function my_plugin_add_subscriber_metaboxes( $post ) {

	add_meta_box(
		'my_plugin_subscriber_details',
		'Subscriber Details',
		'my_plugin_subscriber_matabox',
		'my_plugin_subscriber',
		'normal',
		'default'
	);
	
}

add_action('add_meta_boxes_my_plugin_subscriber', 'my_plugin_add_subscriber_metaboxes');

function my_plugin_subscriber_matabox() {
	
	global $post;
	
	$post_id = $post->ID;	
			
	// wp secure input field function
	wp_nonce_field(basename(__FILE__), 'my_plugin_subscriber_nonce');
	
	// get input fields values from db
	$first_name = (!empty(get_post_meta($post_id, 'my_plugin_first_name', true))) ? get_post_meta($post_id, 'my_plugin_first_name', true) : '';
	$last_name = (!empty(get_post_meta($post_id, 'my_plugin_last_name', true))) ? get_post_meta($post_id, 'my_plugin_last_name', true) : '';
	$email = (!empty(get_post_meta($post_id, 'my_plugin_email', true))) ? get_post_meta($post_id, 'my_plugin_email', true) : '';
	
	$lists = (!empty(get_post_meta($post_id, 'my_plugin_list', false))) ? get_post_meta($post_id, 'my_plugin_list', false) : [];
		
	
	?>
	
	<style type="text/css" media="screen">
	.my-plugin-field-row .my-plugin-field-container {
		margin-bottom: 10px;
	}
	.my-plugin-field-row .my-plugin-field-container label {
		width: 120px;
		display: inline-block;
	}
	.my-plugin-field-row .my-plugin-field-container label span {
		color: red;
		margin-left: 5px;
	}
	.my-plugin-field-row .my-plugin-field-container ul {
		margin-left: 40px;
		display: flex;
		flex-direction: column;
	}
	</style>
	
	<div class="my-plugin-field-row">
		<div class="my-plugin-field-container">
			<label>First Name<span>*</span></label>
			<input type="text" name="my_plugin_first_name" required="required" class="widefat" value="<?php echo $first_name; ?>" />
		</div>
		<div class="my-plugin-field-container">
			<label>Last Name<span>*</span></label>
			<input type="text" name="my_plugin_last_name" required="required" class="widefat" value="<?php echo $last_name; ?>" />
		</div>		
	</div>
	
	<div class="my-plugin-field-row">
		<div class="my-plugin-field-container">
			<label>Email<span>*</span></label>
			<input type="email" name="my_plugin_email" required="required" class="widefat" value="<?php echo $email; ?>" />
		</div>	
	</div>
	
	<div class="my-plugin-field-row">
		<div class="my-plugin-field-container">
			<label>List</label>
			<ul>
				
				<?php 
				
				global $wpdb;
				
				$list_query = $wpdb->get_results("SELECT ID,post_title FROM {$wpdb->posts} WHERE post_type = 'my_plugin_list' AND post_status IN ('draft', 'publish')");
				
				if(!is_null($list_query)) {
					
					foreach($list_query as $list) {
						 
						$checked = ( in_array($list->ID, $lists) ) ? 'checked="checked"' : '';	?>
				
				<li>
					<label>
						<input type="checkbox" name="my_plugin_list[]" value="<?php echo $list->ID ?>" <?php echo $checked; ?> />
						<?php echo $list->post_title; ?>
					</label>
				</li>
				
				<?php
				
					}					
				}

				?>

			</ul>
		</div>	
	</div>		
	
	<?php
	
}

function my_plugin_save_subscriber_data($post_id, $post) {
	
	// verify nonce
	if(!isset($_POST['my_plugin_subscriber_nonce']) || !wp_verify_nonce($_POST['my_plugin_subscriber_nonce'], basename(__FILE__))) {
		return $post_id;
	}
	
	// get post type object
	$post_type = get_post_type_object($post->post_type);
	
	// check if current user have permission to edit
	if(!current_user_can($post_type->cap->edit_post, $post_id)) {
		return $post_id;
	}
	
	// get posted data and sanitize it
	$first_name = (isset($_POST['my_plugin_first_name'])) ? sanitize_text_field($_POST['my_plugin_first_name']) : '';
	$last_name = (isset($_POST['my_plugin_last_name'])) ? sanitize_text_field($_POST['my_plugin_last_name']) : '';
	$email = (isset($_POST['my_plugin_email'])) ? sanitize_text_field($_POST['my_plugin_email']) : '';
	$lists = (isset($_POST['my_plugin_list'])) && is_array($_POST['my_plugin_list']) ? (array) $_POST['my_plugin_list'] : [];
	

	// update post meta
	update_post_meta($post_id, 'my_plugin_first_name', $first_name);
	update_post_meta($post_id, 'my_plugin_last_name', $last_name);
	update_post_meta($post_id, 'my_plugin_email', $email);
	
	// delete existing post meta
	delete_post_meta($post_id, 'my_plugin_list');
	
	// add new meta
	if(!empty($lists)) {
		foreach($lists as $index => $list_id) {
			
			// add list relational meta
			add_post_meta($post_id, 'my_plugin_list', $list_id, false); // not unique meta key
		}
	}
	
	
	
}

add_action('save_post', 'my_plugin_save_subscriber_data', 10, 2);


// add post title to subscriber
function my_plugin_edit_post_change_title() {
	
	global $post;
	
	if($post->post_type == 'my_plugin_subscriber') {
		
		add_filter(
			'the_title',
			'my_plugin_subscriber_title',
			100,
			2
		);
	}
}

add_action(
	'admin_head-edit.php',
	'my_plugin_edit_post_change_title'
);

function my_plugin_subscriber_title($title, $post_id) {
	$new_title = get_post_meta($post_id, 'my_plugin_first_name', true) . ' ' . get_post_meta($post_id, 'my_plugin_last_name', true);
	return $new_title;
}







