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
 * Warn admins if build assets are missing (common when cloning the repo without running npm build).
 */
function stampvault_missing_build_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$expected = STAMPVAULT_PLUGIN_DIR . 'build/blocks/stamp-info/index.asset.php';
	if ( ! file_exists( $expected ) ) {
		echo '<div class="notice notice-error"><p><strong>StampVault:</strong> Block assets are missing. Run <code>npm install</code> then <code>npm run build</code> to generate the <code>build/</code> directory before packaging or using the block.</p></div>';
	}
}
add_action( 'admin_notices', 'stampvault_missing_build_notice' );

/**
 * Set flag on deactivation if data exists.
 */
function stampvault_on_deactivate() {
	$stamps = get_posts( array( 'post_type' => 'stamps', 'numberposts' => 1, 'post_status' => 'any' ) );
	if ( ! empty( $stamps ) ) {
		update_option( 'stampvault_data_remains', 1 );
	}
}
