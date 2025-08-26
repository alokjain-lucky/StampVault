<?php
/**
 * Server-side render callback for Stamp Info block.
 */

if ( ! function_exists( 'stampvault_render_stamp_info_block' ) ) {
	function stampvault_render_stamp_info_block( $attributes, $content, $block ) {
		// For now static output; later can pull meta by post ID.
		if ( get_post_type() !== 'stamps' ) {
			return '';
		}
		return '<div class="wp-block-stampvault-stamp-info">' . esc_html__( 'Hello StampVault!', 'stampvault' ) . '</div>';
	}
}
