<?php
// Import tab placeholder – future: CSV import with mapping & filters.

add_action( 'stampvault_render_settings_tab_import', function() {
	?>
	<h2><?php esc_html_e( 'Import Stamps & Taxonomies (CSV)', 'stampvault' ); ?></h2>
	<p class="description"><?php esc_html_e( 'Upload a CSV to bulk create or update stamps and related taxonomies. A mapping UI and dry-run validation will be provided here.', 'stampvault' ); ?></p>
	<p><em><?php esc_html_e( 'Feature coming soon.', 'stampvault' ); ?></em></p>
	<?php
} );
