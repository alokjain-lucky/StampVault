<?php
/**
 * StampVault Settings bootstrap (submenu + tabs dispatcher).
 */

stampvault_security_check();

// Page slug constant.
if ( ! defined( 'STAMPVAULT_SETTINGS_PAGE' ) ) {
	define( 'STAMPVAULT_SETTINGS_PAGE', 'stampvault-settings' );
}

// Load individual tab modules.
require_once __DIR__ . '/catalogs-settings.php';
require_once __DIR__ . '/apis-settings.php';
require_once __DIR__ . '/import-settings.php';
require_once __DIR__ . '/export-settings.php';

/**
 * Register submenu under Stamps CPT.
 */
function stampvault_register_settings_submenu() {
	add_submenu_page(
		'edit.php?post_type=stamps',
		__( 'StampVault Settings', 'stampvault' ),
		__( 'Settings', 'stampvault' ),
		'manage_options',
		STAMPVAULT_SETTINGS_PAGE,
		'stampvault_render_settings_page'
	);
}
add_action( 'admin_menu', 'stampvault_register_settings_submenu' );

/**
 * Current active tab slug.
 */
function stampvault_settings_active_tab() {
	return isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'catalogs';
}

/**
 * List available tabs (slug => label).
 */
function stampvault_settings_tabs() {
	return apply_filters( 'stampvault_settings_tabs', [
		'catalogs' => __( 'Catalogs', 'stampvault' ),
		'apis'     => __( 'APIs', 'stampvault' ),
		'import'   => __( 'Import', 'stampvault' ),
		'export'   => __( 'Export', 'stampvault' ),
	] );
}

/**
 * Render entire settings page (tabs + dispatcher).
 */
function stampvault_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) return;
	$tabs = stampvault_settings_tabs();
	$active = stampvault_settings_active_tab();
	$base = admin_url( 'edit.php?post_type=stamps&page=' . STAMPVAULT_SETTINGS_PAGE );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'StampVault Settings', 'stampvault' ); ?></h1>
		<h2 class="nav-tab-wrapper" style="margin-bottom:1rem;">
			<?php foreach ( $tabs as $slug => $label ) : ?>
				<a href="<?php echo esc_url( $base . '&tab=' . $slug ); ?>" class="nav-tab <?php echo $active === $slug ? 'nav-tab-active' : ''; ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</h2>
		<div class="stampvault-settings-tab-panel">
			<?php
			$hook = 'stampvault_render_settings_tab_' . $active;
			if ( has_action( $hook ) ) {
				do_action( $hook );
			} else {
				echo '<p>' . esc_html__( 'Nothing to display for this tab yet.', 'stampvault' ) . '</p>';
			}
			?>
		</div>
	</div>
	<?php
}

/**
 * Add plugin action link (Settings) pointing to new settings page.
 */
function stampvault_settings_action_link( $links ) {
	$settings_url = admin_url( 'edit.php?post_type=stamps&page=' . STAMPVAULT_SETTINGS_PAGE );
	$settings_link = '<a href="' . esc_url( $settings_url ) . '">' . esc_html__( 'Settings', 'stampvault' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( STAMPVAULT_PLUGIN_DIR . 'stampvault.php' ), 'stampvault_settings_action_link' );
