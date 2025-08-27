<?php
/*
Plugin Name: StampVault
Description: Catalog, manage, and showcase stamp collections with advanced organizational and display features for philatelists.
Version: 1.0.0
Author: Alok Jain
Author URI: https://alokjain.dev
Plugin URI: https://github.com/alokjain-lucky/StampVault
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: stampvault
Domain Path: /languages
Requires PHP: 7.2
*/

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
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
require_once STAMPVAULT_PLUGIN_DIR . 'includes/entities/stampvault-meta-stamps.php';
// Load settings (catalog list helpers) before meta UI so helper functions are available.
require_once STAMPVAULT_PLUGIN_DIR . 'includes/settings/settings.php';
require_once STAMPVAULT_PLUGIN_DIR . 'includes/entities/meta-ui/stampvault-meta-stamps-ui.php';
require_once STAMPVAULT_PLUGIN_DIR . 'includes/blocks.php';

// (Settings page action link now added inside includes/settings/settings.php)

// Hooks
register_activation_hook( __FILE__, 'stampvault_activate_plugin' );
add_action( 'admin_notices', 'stampvault_show_data_remains_notice' );
register_deactivation_hook( __FILE__, 'stampvault_on_deactivate' );

// Gutenberg blocks init hook
add_action( 'init', 'stampvault_register_blocks' );
