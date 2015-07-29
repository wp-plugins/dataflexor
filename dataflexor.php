<?php
/*
Plugin Name: DataFlexor
Plugin URI: http://dataflexor.plugin.city/
Description: DataFlexor is a data management framework to allow easy extension of WordPress content. Requires the Advanced Custom Fields plugin.
Version: 1.0.0
Author: Andrew Fielden
Author URI: http://andrew.ahead4.biz/
License: GPL2
*/

//security to ensure it is being accessed via WordPress

if ( ! defined( 'WPINC' ) ) {
	die;
}

/*
|--------------------------------------------------------------------------
| CONSTANTS
|--------------------------------------------------------------------------
*/

if ( ! defined( 'AH_WN_BASE_FILE' ) )
	define( 'AH_WN_BASE_FILE', __FILE__ );
if ( ! defined( 'AH_WN_BASE_DIR' ) )
	define( 'AH_WN_BASE_DIR', plugin_dir_path( AH_WN_BASE_FILE ) );
if ( ! defined( 'AH_WN_PLUGIN_URL' ) )
	define( 'AH_WN_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
if ( ! defined( 'AH_WN_JS_URL' ) )
	define( 'AH_WN_JS_URL', plugin_dir_url( __FILE__ ) . 'includes/js/' );

/*
|--------------------------------------------------------------------------
| CLASSES
|--------------------------------------------------------------------------
*/

require_once AH_WN_BASE_DIR . 'classes/manager.php';

require_once AH_WN_BASE_DIR . 'classes/df_setup.php';

require_once AH_WN_BASE_DIR . 'classes/df_standard.php';

//require_once AH_WN_BASE_DIR . 'classes/df_fields.php';

//include the taxonomy meta class file
require_once AH_WN_BASE_DIR . 'Tax-meta-class/Tax-meta-class.php';

// initialise dataflexor

add_action( 'wp', 'df_init', 20 );

function df_init(){

	new df_manager();

}

// setup dataflexor componenets

add_action( 'init', 'df_setup', 20 );

function df_setup(){

	new df_setup();

}

add_action( 'plugins_loaded', 'df_check_acf_loaded' );

function df_check_acf_loaded(){

	if ( false == class_exists('acf') ){
		add_action( 'admin_notices', 'acf_admin_error_notice' );
	}

}

function acf_admin_error_notice() {

	$class = 'update-nag';
	$message = 'DataFlexor needs the <a href="https://wordpress.org/plugins/advanced-custom-fields/">Advanced Custom Fields plugin</a> to run';
	echo "<div class=\"$class\"> <p>$message</p></div>";

}