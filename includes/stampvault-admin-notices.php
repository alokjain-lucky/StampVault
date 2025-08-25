<?php
/**
 * Admin notices and plugin state handlers for StampVault.
 *
 * @package StampVault
 */

/**
 * Show admin notice if plugin is deactivated and data remains in the database.
 */
function stampvault_show_data_remains_notice() {
	if ( get_option( 'stampvault_data_remains' ) ) {
		echo '<div class="notice notice-warning is-dismissible"><p><strong>StampVault:</strong> The plugin was deactivated, but your stamp collection data is still present in the database. Uninstalling the plugin will permanently delete all StampVault data.</p></div>';
	}
}

/**
 * Set flag on deactivation if data exists.
 */
function stampvault_on_deactivate() {
	$stamps = get_posts( array( 'post_type' => 'stamps', 'numberposts' => 1, 'post_status' => 'any' ) );
	if ( ! empty( $stamps ) ) {
		update_option( 'stampvault_data_remains', 1 );
	}
}

/**
 * Remove flag on activation.
 */
function stampvault_on_activate() {
	delete_option( 'stampvault_data_remains' );
}
