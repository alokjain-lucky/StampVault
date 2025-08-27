<?php
/**
 * Register the 'Stamps' custom post type for StampVault.
 *
 * @package StampVault
 *
 * This file contains the registration logic for the 'stamps' custom post type.
 * The post type is used to catalog and display individual stamp entries in the StampVault plugin.
 *
 * - Registered on every request via the 'init' action.
 * - Supports title, editor, thumbnail, and custom fields.
 * - Exposed in the REST API for integration with Gutenberg and external apps.
 */

// Security check to prevent direct access to this file.
stampvault_security_check();

/**
 * Registers the 'stamps' custom post type for stamp collections.
 *
 * @see https://developer.wordpress.org/reference/functions/register_post_type/
 */
function stampvault_register_post_type_stamps() {
	$labels = array(
		'name'               => __( 'Stamps', 'stampvault' ),
		'singular_name'      => __( 'Stamp', 'stampvault' ),
		'menu_name'          => __( 'Stamps', 'stampvault' ),
		'name_admin_bar'     => __( 'Stamp', 'stampvault' ),
		'add_new'            => __( 'Add New', 'stampvault' ),
		'add_new_item'       => __( 'Add New Stamp', 'stampvault' ),
		'new_item'           => __( 'New Stamp', 'stampvault' ),
		'edit_item'          => __( 'Edit Stamp', 'stampvault' ),
		'view_item'          => __( 'View Stamp', 'stampvault' ),
		'all_items'          => __( 'All Stamps', 'stampvault' ),
		'search_items'       => __( 'Search Stamps', 'stampvault' ),
		'parent_item_colon'  => __( 'Parent Stamps:', 'stampvault' ),
		'not_found'          => __( 'No stamps found.', 'stampvault' ),
		'not_found_in_trash' => __( 'No stamps found in Trash.', 'stampvault' ),
	);

	$args = array(
		'labels'       => $labels,
		'public'       => true,
		'has_archive'  => true,
		'show_in_menu' => true,
		'supports'     => array( 'title', 'editor', 'thumbnail', 'custom-fields' ), // custom-fields ensures meta exposed properly
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-tickets-alt',
	);

	register_post_type( 'stamps', $args );
}

// Register the custom post type on the 'init' action.
add_action( 'init', 'stampvault_register_post_type_stamps' );

/**
 * Provide default block content for new Stamp posts using an external template file.
 */
function stampvault_stamps_default_content( $content, $post ) {
	if ( ! $post || 'stamps' !== $post->post_type ) {
		return $content;
	}
	if ( ! empty( $content ) ) {
		return $content; // Do not override existing content (e.g., cloning plugins).
	}
	$template_file = STAMPVAULT_PLUGIN_DIR . 'templates/stamps-default-content.html';
	if ( file_exists( $template_file ) ) {
		return file_get_contents( $template_file );
	}
	return $content;
}
add_filter( 'default_content', 'stampvault_stamps_default_content', 10, 2 );
