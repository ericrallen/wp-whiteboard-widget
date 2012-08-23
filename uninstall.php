<?php

	//uninstallation
	if(!defined('WP_UNINSTALL_PLUGIN')) {
		exit ();
	} else{
		//OPTIONS
		require_once(WP_PLUGIN_DIR . '/' . basename(dirname(__FILE__)) . '/plugin-options.php');
		//uninstall stuff here
	}

?>