<?php
	
	/*
		Plugin Name: WP Whiteboard Widget by InternetAlche.Me
		Plugin URI: https://github.com/ericrallen/wp-whiteboard-widget/
		Description: Adds a dashboard widget for administrators to leave quick comments to each other.
		Version: v1.0a
		Author: Eric Allen
		Author URI: http://internetalche.me/
		License: MIT
	*/
	
	/*
	--------------------------------------------------- Change Log -----------------------------------------------------
	
	 + 2012-08-23  v0.1a  Plug-in created.
	
	--------------------------------------------------------------------------------------------------------------------
	*/

	
	//OPTIONS
	require_once(WP_PLUGIN_DIR . '/' . basename(dirname(__FILE__)) . '/plugin-options.php');


	//CLASSES

	//include plugin classes
	require_once(WP_PLUGIN_DIR . '/' . basename(dirname(__FILE__)) . '/assets/classes/WPWhiteboard.class.php');


	//ACTIVATION

	//add plug-in options
	function ia_whiteboard_widget_set_options() {
		global $options,$shortcode;
		foreach($options as $opt => $val) {
			if(!get_option($opt)) {
				add_option($opt,$val);
			} else {
				if(get_option($opt) !== $val) {
					update_option($opt,$val);
				}
			}
		}
		ia_whiteboard_widget_add_caps();
	}
	//run when plug-in is activated
	register_activation_hook(__FILE__,'ia_whiteboard_widget_set_options');
	

	//DEACTIVATION
	
	//remove plug-in options
	function ia_whiteboard_widget_clear_options() {
		global $options, $shortcode, $caps, $wpdb, $wp_roles;
		if(ia_whiteboard_widget_shortcode_exists($shortcode)) {
			remove_shortcode($shortcode);
		}
		
		//get users with jsfiddle meta and remove the meta
		$u_table = $wpdb->prefix . 'usermeta';
		
		$remove['meta_key'] = 'iajsfiddle';
		foreach($remove as $name => $val) {
			$query = "SELECT user_id FROM $u_table WHERE meta_key = '$val';";
			$get_users = $wpdb->get_results($query);
			if($get_users) {
				foreach($get_users as $user_id) {
					delete_user_meta($user_id,$val);
				}
			}
		}
		
		//iterate through all roles and remove the capabilities
		foreach($wp_roles->roles as $role => $info) {
			$role_obj = get_role($role);
			foreach($caps as $cap) {
				if ($role_obj->has_cap($cap)) {
					$role_obj->remove_cap($cap);
				}
			}
		}
	}
	register_deactivation_hook(__FILE__,'ia_whiteboard_widget_clear_options');

	
	//MISC
	
	//include plugin actions
	require_once(WP_PLUGIN_DIR . '/' . basename(dirname(__FILE__)) . '/assets/menu.php'); //menu based actions

	//add role caps
	function ia_whiteboard_widget_add_caps() {
		global $wp_roles, $caps;
		$min_cap = 'manage_options';
		$grant = true;
		//iterate through all roles and add the capabilities
		foreach($wp_roles->role_names as $role => $info) {
			$role_obj = get_role($role);
			foreach($caps as $cap) {
				if(!$role_obj->has_cap($cap) && $role_obj->has_cap($min_cap)) {
					$role_obj->add_cap($cap,$grant);
				}
			}
		}
	}
	
?>