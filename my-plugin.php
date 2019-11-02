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
			<input type="text" name="my_plugin_first_name" required="required" class="widefat" />
		</div>
		<div class="my-plugin-field-container">
			<label>Last Name<span>*</span></label>
			<input type="text" name="my_plugin_last_name" required="required" class="widefat" />
		</div>		
	</div>
	
	<div class="my-plugin-field-row">
		<div class="my-plugin-field-container">
			<label>Email<span>*</span></label>
			<input type="email" name="my_plugin_email" required="required" class="widefat" />
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
					foreach($list_query as $list) { ?>
				
				<li>
					<label>
						<input type="checkbox" name="my_plugin_list[]" value="<?php echo $list->ID ?>" />
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










