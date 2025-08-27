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
		[ 'name' => 'stamp-info', 'options' => [ 'render_callback' => 'stampvault_render_stamp_info_block' ] ],
		// Future blocks can be appended here, e.g.:
		// [ 'name' => 'search-form', 'options' => [ 'render_callback' => 'stampvault_render_search_form' ] ],
	];

	foreach ( $blocks as $block ) {
		$dir = STAMPVAULT_PLUGIN_DIR . 'build/blocks/' . $block['name'];
		if ( file_exists( $dir . '/block.json' ) ) {
			$registered = register_block_type( $dir, $block['options'] ?? [] );
			if ( $registered && isset( $registered->editor_script ) && function_exists( 'wp_localize_script' ) ) {
				$catalogs = function_exists( 'stampvault_get_catalogs_option' ) ? stampvault_get_catalogs_option() : [];
				wp_localize_script( $registered->editor_script, 'StampVaultBlockData', [ 'catalogs' => $catalogs ] );
			}
		}
	}
}

// Include individual block render callbacks (PHP) from blocks directory.
if ( file_exists( STAMPVAULT_PLUGIN_DIR . 'blocks/stamp-info/render.php' ) ) {
	require_once STAMPVAULT_PLUGIN_DIR . 'blocks/stamp-info/render.php';
}

/**
 * Limit StampVault blocks to intended post types.
 * Adds a server-side safeguard in case client-side postTypes filter is bypassed.
 */
function stampvault_limit_blocks_to_stamps( $allowed_block_types, $editor_context ) {
	if ( empty( $editor_context->post ) ) {
		return $allowed_block_types;
	}
	$post_type = $editor_context->post->post_type;
	if ( 'stamps' !== $post_type ) {
		if ( is_array( $allowed_block_types ) ) {
			return array_values( array_filter( $allowed_block_types, function( $block ) {
				return strpos( $block, 'stampvault/' ) !== 0; // exclude our namespace
			} ) );
		}
		if ( class_exists( 'WP_Block_Type_Registry' ) ) {
			$registered = \WP_Block_Type_Registry::get_instance()->get_all_registered();
			return array_values( array_filter( array_keys( $registered ), function( $block_name ) {
				return strpos( $block_name, 'stampvault/' ) !== 0;
			} ) );
		}
	}
	return $allowed_block_types;
}
if ( function_exists( 'add_filter' ) ) {
	add_filter( 'allowed_block_types_all', 'stampvault_limit_blocks_to_stamps', 20, 2 );
}
