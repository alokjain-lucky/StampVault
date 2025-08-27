<?php
// Export tab placeholder – future: CSV export with filters.

add_action( 'stampvault_render_settings_tab_export', function() {
	?>
	<h2><?php esc_html_e( 'Export Stamps & Taxonomies (CSV)', 'stampvault' ); ?></h2>
	<p class="description"><?php esc_html_e( 'Select filters below to export a structured CSV of stamps and taxonomy terms.', 'stampvault' ); ?></p>
	<p><em><?php esc_html_e( 'Feature coming soon.', 'stampvault' ); ?></em></p>
	<?php
} );
