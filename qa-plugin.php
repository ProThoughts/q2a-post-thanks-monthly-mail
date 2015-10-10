<?php
/*
	Plugin Name: active-thanks-email
	Plugin URI: 
	Plugin Description: 
	Plugin Version: 0.3
	Plugin Date: 2015-06-21
	Plugin Author:
	Plugin Author URI:
	Plugin License: GPLv2
	Plugin Minimum Question2Answer Version: 1.7
	Plugin Update Check URI: 
*/
if (!defined('QA_VERSION')) {
	header('Location: ../../');
	exit;
}

qa_register_plugin_module('module', 'admin.php', 'q2a_active_thanks_admin', 'active thanks');
