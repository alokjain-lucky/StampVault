<?php
/*
Plugin Name: StampVault
Description: A WordPress plugin for philatelists to digitally catalog, manage, and display their stamp collections.
Version: 1.0.0
Author: Alok Jain
Author URI: https://alokjain.dev
Plugin URI: https://github.com/alokjain-lucky/StampVault
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: stampvault
Domain Path: /languages
Requires PHP: 7.2
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'STAMPVAULT_VERSION', '1.0.0' );
define( 'STAMPVAULT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'STAMPVAULT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
