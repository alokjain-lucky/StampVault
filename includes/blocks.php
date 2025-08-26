<?php
/**
 * Gutenberg block registrations for StampVault.
 */

stampvault_security_check();

/**
 * Register all plugin blocks
 */
function stampvault_register_blocks() {
	$blocks = [
		[ 'name' => 'stamp-info' ],
		// Future blocks can be appended here, e.g.:
		// [ 'name' => 'search-form', 'options' => [ 'render_callback' => 'stampvault_render_search_form' ] ],
	];

	foreach ( $blocks as $block ) {
		$dir = STAMPVAULT_PLUGIN_DIR . 'build/blocks/' . $block['name'];
		if ( file_exists( $dir . '/block.json' ) ) {
			register_block_type( $dir, $block['options'] ?? [] );
		}
	}
}
