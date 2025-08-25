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


/**
 * Security check to prevent direct access to plugin files.
 */
if ( ! function_exists( 'stampvault_security_check' ) ) {
	function stampvault_security_check() {
		if ( ! defined( 'ABSPATH' ) ) {
			exit;
		}
	}
}

stampvault_security_check();

// Define plugin constants
define( 'STAMPVAULT_VERSION', '1.0.0' );
define( 'STAMPVAULT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'STAMPVAULT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Always require activation and admin notices files first for hook registration
require_once STAMPVAULT_PLUGIN_DIR . 'includes/stampvault-activation.php';
require_once STAMPVAULT_PLUGIN_DIR . 'includes/stampvault-admin-notices.php';
require_once STAMPVAULT_PLUGIN_DIR . 'includes/entities/stampvault-cpt-stamps.php';
require_once STAMPVAULT_PLUGIN_DIR . 'includes/entities/stampvault-taxonomies.php';

// Hooks
register_activation_hook( __FILE__, 'stampvault_activate_plugin' );
add_action( 'admin_notices', 'stampvault_show_data_remains_notice' );
register_deactivation_hook( __FILE__, 'stampvault_on_deactivate' );
register_activation_hook( __FILE__, 'stampvault_on_activate' );
