<?php
/**
 * Plugin activation functions for StampVault.
 *
 * @package StampVault
 */

stampvault_security_check();

/**
 * Flush rewrite rules on plugin activation.
 * Ensures CPT is registered before flushing.
 */
function stampvault_activate_plugin() {
	// Register CPT and taxonomies directly to ensure they are available before flushing
	stampvault_register_post_type_stamps();
	stampvault_register_stamp_taxonomies();
	if ( post_type_exists( 'stamps' ) ) {
		flush_rewrite_rules();
	} else {
		// Set an option to display an admin notice if CPT is not registered
		update_option( 'stampvault_activation_cpt_missing', 1 );
	}
}

/**
 * Display admin notice if CPT was not registered during activation.
 */
function stampvault_activation_cpt_missing_notice() {
	if ( get_option( 'stampvault_activation_cpt_missing' ) ) {
		echo '<div class="notice notice-error"><p><strong>StampVault:</strong> The Stamps custom post type was not registered before flushing rewrite rules during activation. Please deactivate and reactivate the plugin, and report this issue if it persists.</p></div>';
		delete_option( 'stampvault_activation_cpt_missing' );
	}
}
add_action( 'admin_notices', 'stampvault_activation_cpt_missing_notice' );
